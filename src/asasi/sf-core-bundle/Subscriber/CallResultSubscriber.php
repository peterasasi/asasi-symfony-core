<?php
/**
 * Created by PhpStorm.
 * User: asasi
 * Date: 2018/8/4
 * Time: 15:47
 */

namespace Dbh\SfCoreBundle\Subscriber;


use by\component\exception\InvalidArgumentException;
use by\infrastructure\base\CallResult;
use by\infrastructure\constants\BaseErrorCode;
use by\infrastructure\helper\CallResultHelper;
use Dbh\SfCoreBundle\Common\ByRequestContext;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Contracts\Translation\TranslatorInterface;

class CallResultSubscriber implements EventSubscriberInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;
    /**
     * @var TranslatorInterface
     */
    private $translator;

    private $logger;
    private $resultFormat;

    public function __construct(
        LoggerInterface $logger,
        TranslatorInterface $translator,
        ContainerInterface $container)
    {
        $this->container = $container;
        $this->translator = $translator;
        $this->logger = $logger;
        $this->resultFormat = true;
        if ($this->container->hasParameter('dbh.sfcore.resultformat')) {
            $this->resultFormat = boolval($this->container->getParameter('dbh.sfcore.resultformat'));
        }
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::VIEW => ['onKernelView', 8]
        ];
    }


    /**
     * 如果返回的是字符串
     *      则如果为success则成功、否则失败
     *
     * @param ViewEvent $event
     */
    public function onKernelView(ViewEvent $event)
    {
        if (!$this->resultFormat) {
            return;
        }
        $value = $event->getControllerResult();
        // 如果返回的是响应类，则直接返回不处理
        if ($value instanceof Response) {
            return;
        }
        // 如果返回的不是CallResult 类 ，则进行处理，并封装到CallResult类中
        if (!($value instanceof CallResult)) {

            if (is_string($value) && $value !== 'success') {
                // 如果是字符串 且不等于 success , 则视作请求失败
                $value = new CallResult([], $value, BaseErrorCode::Retry);
            } elseif (is_bool($value)) {
                // 如果是bool类型，则根据true为成功 false为失败处理
                if ($value) {
                    $value = CallResultHelper::success($value);
                } else {
                    $value = CallResultHelper::fail();
                }
            } else {
                // 其它情况 视为成功，且返回数据作为data进行返回
                $value = new CallResult($value, 'success');
            }
        }

        $data = $value;
        // 对返回的字符串消息进行 语言转换
        $msg = $data->getMsg();
        // 针对返回的消息为数组的情况
        // 主要是为了解决 Message Placeholders 传参数的问题，数组的第2位作为参数
        // https://symfony.com/doc/current/components/translation/usage.html#component-translation-placeholders
        if (is_array($msg) && count($msg) == 2) {
            if ($data->isFail() && $msg[0] !== 'success') {
                $data->setCode(BaseErrorCode::Retry);
            }
            $transMsg = $this->translator->trans($msg[0], $msg[1]);
        } else {
            $transMsg = $this->translator->trans($msg);
        }
        $data->setMsg($transMsg);


        // 对CallResult进行json编码
        if ($this->container->has('serializer')) {
            $serializer = $this->container->get('serializer');
            $json = $serializer->serialize($data, 'json', array_merge(array(
                'json_encode_options' => JsonResponse::DEFAULT_ENCODING_OPTIONS,
            ), []));
            $json = json_decode($json, JSON_OBJECT_AS_ARRAY);
            $json['data'] = $this->stringVal($json['data']);
            // TODO: 对返回结果加密
            // TODO: 平台私钥加密
        } else {
            $json = [
                'msg' => $data->getMsg(),
                'data' => $data->getData(),
                'code' => $data->getCode(),
            ];
        }

        if (!defined('BY_APP_START_TIME')) throw new InvalidArgumentException('Not Defined \'BY_APP_START_TIME\' Constant');

        $json['_start'] = BY_APP_START_TIME;
        $json['_cost'] = 0;

        if ($this->container->has('by.global.request')) {
            $requestPo = $this->container->get('by.global.request');
            if ($requestPo instanceof ByRequestContext) {
                $json['notify_id'] = $requestPo->getNotifyId();
            }
        }

        ksort($json);
        // 获取请求开始时间，计算耗时时间
        $costTime = intval(1000000 * (microtime(true) - BY_APP_START_TIME)) . 'us';
        $json['_cost'] = $costTime;

        if ($json['code'] != 0) {
            $this->logger->debug('RETURN Failed =>' . $json['msg']);
        }
        $response = new JsonResponse(json_encode($json), 200, [], true);

        $event->setResponse($response);
    }

    protected function stringVal($json)
    {
        if (is_bool($json)) {
            return intval($json);
        } elseif (is_integer($json) || is_long($json)) {
            return $json;
        } elseif (is_array($json)) {
            foreach ($json as $k => &$v) {
                $v = $this->stringVal($v);
            }
            return $json;
        } elseif (is_object($json)) {
            return "[object]";
        } elseif (is_null($json)) {
            return '';
        } else {
            return $json;
        }
    }

    protected function logFailed()
    {
        // TODO: 记录失败记录
    }
}
