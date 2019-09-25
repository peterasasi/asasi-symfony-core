<?php
/**
 * Created by PhpStorm.
 * User: asasi
 * Date: 2018/8/7
 * Time: 11:16
 */

namespace Dbh\SfCoreBundle\Subscriber;


use by\component\encrypt\factory\TransportFactory;
use by\component\encrypt\rsa\Rsa;
use by\component\exception\InvalidArgumentException;
use by\infrastructure\constants\BaseErrorCode;
use by\infrastructure\helper\ArrayHelper;
use by\infrastructure\helper\Object2DataArrayHelper;
use Dbh\SfCoreBundle\Common\ApiGuardInterface;
use Dbh\SfCoreBundle\Common\ByCacheKeys;
use Dbh\SfCoreBundle\Common\ByEnv;
use Dbh\SfCoreBundle\Common\ByRequestContext;
use Dbh\SfCoreBundle\Common\ClientsInterface;
use Dbh\SfCoreBundle\Common\GetClientsInterface;
use Dbh\SfCoreBundle\Common\GetRequestInterface;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Exception;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * 处理异常以及设置一些上下文环境
 * @package App\Listeners
 */
class ApiGuardSubscriber implements EventSubscriberInterface
{
    /**
     * @var ByRequestContext
     */
    protected $requestPo;
    /**
     * @var GetClientsInterface
     */
    protected $clientRepo;
    /**
     * @var ClientsInterface
     */
    protected $clientInfo;

    /**
     * @var LoggerInterface
     */
    protected $logger;
    protected $isDebug;

    /**
     * @var TranslatorInterface
     */
    protected $translator;

    /**
     * @var CacheItemPoolInterface
     */
    protected $cache;

    protected $env;
    protected $container;

    public function __construct(ClientsInterface $clientInfo, GetClientsInterface $repository, ContainerInterface $container, CacheItemPoolInterface $cache,
                                TranslatorInterface $translator, KernelInterface $kernel, LoggerInterface $logger)
    {
        $this->clientInfo = $clientInfo;
        $this->container = $container;
        $this->translator = $translator;
        $this->clientRepo = $repository;
        $this->requestPo = new ByRequestContext();
        $this->isDebug = $kernel->isDebug();
        $this->env = $kernel->getEnvironment();
        $this->logger = $logger;
        $this->cache = $cache;
    }

    public static function getSubscribedEvents()
    {
        return array(
            KernelEvents::CONTROLLER => ['onKernelController', 10],
            KernelEvents::EXCEPTION => ['onKernelException', 0]
        );
    }

    /**
     * 自定义异常
     * @param ExceptionEvent $event
     */
    public function onKernelException(ExceptionEvent $event)
    {
        $ret = [
            'notify_id' => -1,
            'code' => BaseErrorCode::Api_EXCEPTION,
            'msg' => $event->getException()->getMessage(),
            'data' => $event->getException()->getMessage()
        ];

        $ret['code'] = $event->getException()->getCode() === 0 ? BaseErrorCode::Api_EXCEPTION : $event->getException()->getCode();

        if ($this->container->has('by.global.request')) {
            $requestPo = $this->container->get('by.global.request');
            if ($requestPo instanceof ByRequestContext) {
                $ret['notify_id'] = $requestPo->getNotifyId();
            }
        }

        if ($event->getException() instanceof UniqueConstraintViolationException) {
            $ret['msg'] = "have exist";
            $ret['data'] = '';
        } else {
            if ($this->isDebug) {
                $ret['data'] = [
                    'type' => get_class($event->getException()),
                    'file' => $event->getException()->getFile(),
                    'line' => $event->getException()->getLine(),
                    'trace' => $event->getException()->getTraceAsString()
                ];
            } else {
                $ret['msg'] = $event->getException()->getMessage();
            }

            // 记录日志
        }

        // 非调试模式下 或 正式环境下 不返回 data
        if ($this->env == 'prod' || !$this->isDebug) {
            $ret['data'] = '';
        }

        // 消息翻译
        if (is_string($ret['msg'])) {
            $ret['msg'] = $this->translator->trans($ret['msg']);
        } elseif (is_array($ret['msg'])) {
            $ret['msg'] = $this->translator->trans($ret['msg'][0], $ret['msg'][1]);
        }

        // 记录错误日志
        $this->logger->error('exception-data:' . $ret['msg'] . ';' . ($event->getException()->getTraceAsString()));

        // 自定义status返回200 默认返回500
        $event->allowCustomResponseCode();
        $response = new JsonResponse($ret, 200);
        $event->setResponse($response);
    }

    /**
     * @param ControllerEvent $event
     * @throws \Psr\Cache\InvalidArgumentException
     * @throws Exception
     */
    public function onKernelController(ControllerEvent $event)
    {
        $controller = $event->getController();

        if (!is_array($controller)) {
            return;
        }

        if ($controller[0] instanceof ApiGuardInterface) {
            if ($event->isMasterRequest()) {
                if (method_exists($controller[0], "setRequest")) {
                    $controller[0]->setRequest($event->getRequest());
                }
            } else {
                $controller[0]->setContext($this->requestPo);
                if ($controller[0] instanceof GetRequestInterface) {
                    // 使用解密的数据 替换 请求中的参数
                    $event->getRequest()->request->replace($this->requestPo->getDecryptData());
                    $controller[0]->setRequest($event->getRequest());
                }
                return;
            }
            $this->logger->debug('api guard decrypt');
            $postParams = $event->getRequest()->request->all();
            $getParams = $event->getRequest()->query->all();
            // Post参数优先
            $allParams = array_merge($getParams, $postParams);

            Object2DataArrayHelper::setData($this->requestPo, $allParams);
            // 获取通信算法相关信息，做好解密工作准备
            $this->getClientInfo();

            $this->requestPo->check();
            $requiredKey = array_keys($this->requestPo->toArray());
            ArrayHelper::filter($allParams, $requiredKey);
            $this->requestPo->setData($allParams);
            // 解密工作
            $this->decrypt();
            $controller[0]->setContext($this->requestPo);
            // 设置全局请求参数
            $this->container->set('by.global.request', $this->requestPo);
        }
    }

    /**
     *
     * 获取对应Client_id的一些信息，包含通信算法，密钥等
     * @throws \Psr\Cache\InvalidArgumentException
     */
    protected function getClientInfo()
    {

        $this->logger->debug('ApiGuard getClientInfo');
        $clientId = $this->requestPo->getClientId();
        if (empty($clientId) && $this->isDebug && $this->env != 'prod') {
            $clientId = ByEnv::get('TEST_CLIENT_ID');
            if (empty($clientId)) {
                throw new InvalidArgumentException("the client_id is empty");
            }
            $this->requestPo->setClientId($clientId);
            $this->logger->debug('use test client id ' . $clientId);
        }

        $cacheItem = $this->cache->getItem(ByCacheKeys::ClientId . $clientId);
        if ($cacheItem->isHit()) {
            $data = $cacheItem->get();
            $this->logger->debug('use cache client info');
        } else {
            $clientId = $this->requestPo->getClientId();
            $client = $this->clientRepo->getClientBy($clientId);
            if (!($client instanceof ClientsInterface)) {
                throw new InvalidArgumentException("the $clientId client_id not exists");
            }
            $data = json_encode($client->toArrayData());
            $cacheItem->expiresAfter(ByCacheKeys::getExpireTime(ByCacheKeys::ClientId));
            $cacheItem->set($data);
            $this->cache->save($cacheItem);
            $this->logger->debug('use database client info');
        }

        $data = json_decode($data, JSON_OBJECT_AS_ARRAY);
        $this->clientInfo->setArrayData($data);
        $this->requestPo->setClientId($this->clientInfo->getClientId());
        $this->requestPo->setClientInfo($this->clientInfo);
        $this->requestPo->setClientSecret($this->clientInfo->getClientSecret());
        $this->requestPo->setProjectId($this->clientInfo->getProjectId());
        $this->requestPo->setAlg($this->clientInfo->getApiAlg());
    }

    /**
     * 解密通信数据
     * @throws Exception
     */
    protected function decrypt()
    {
        $algInstance = (TransportFactory::getAlg($this->requestPo->getAlg(), $this->requestPo->getData()));
        $data = $this->requestPo->getData();
        if (!is_array($data)) {
            $data = [];
        }

        if (empty($algInstance)) {
            throw new InvalidArgumentException('Invalid Alg Of ' . $this->requestPo->getClientId() . $this->requestPo->getAlg());
        }


        $this->logger->debug('use alg ' . get_class($algInstance));

        $data['client_secret'] = $this->requestPo->getClientSecret();
        // RSA 密钥
        $data['my_private_key'] = $this->requestPo->getClientInfo()->getUserPrivateKey();
        $data['my_private_key'] = Rsa::formatPrivateText($data['my_private_key']);
        $data['my_public_key'] = $this->requestPo->getClientInfo()->getUserPublicKey();
        $data['my_public_key'] = Rsa::formatPublicText($data['my_public_key']);

        $data['sys_private_key'] = $this->requestPo->getClientInfo()->getSysPrivateKey();
        $data['sys_private_key'] = Rsa::formatPrivateText($data['sys_private_key']);
        $data['sys_public_key'] = $this->requestPo->getClientInfo()->getSysPublicKey();
        $data['sys_public_key'] = Rsa::formatPublicText($data['sys_public_key']);

        $poArr = $this->requestPo->toArray();
        unset($poArr['create_time']);
        unset($poArr['update_time']);
        unset($poArr['data']);
        $decryptData = $algInstance->decrypt(array_merge($data, $poArr));
        $this->requestPo->setDecryptData($decryptData);
        return $decryptData;
    }
}
