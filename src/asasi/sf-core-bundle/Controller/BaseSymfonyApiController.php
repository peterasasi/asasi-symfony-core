<?php
/**
 * Created by PhpStorm.
 * User: asasi
 * Date: 2018/8/3
 * Time: 18:24
 */

namespace Dbh\SfCoreBundle\Controller;


use by\component\exception\NoParamException;
use Dbh\SfCoreBundle\Common\ApiGuardInterface;
use Dbh\SfCoreBundle\Common\ByAppType;
use Dbh\SfCoreBundle\Common\ByRequestContext;
use Dbh\SfCoreBundle\Common\GetRequestInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\KernelInterface;

abstract class BaseSymfonyApiController extends AbstractController implements ApiGuardInterface, GetRequestInterface
{

    /**
     * @var Request
     */
    protected $request;

    /**
     * @var ByRequestContext
     */
    protected $context;

    /**
     * @var KernelInterface
     */
    protected $kernel;

    protected $supportLanguages = ['zh-cn', 'en'];

    public function __construct(KernelInterface $kernel)
    {
        $this->kernel = $kernel;
    }

    public function setRequest(Request $request)
    {
        // 设置语言
        $this->request = $request;
        if ($this->context) {
            $this->request->setLocale($this->getLang());
        }
    }

    /**
     * 获取请求的语言
     */
    protected function getLang()
    {
        $lang = $this->context->getLang();
        if (in_array($lang, $this->supportLanguages)) {
            return $lang;
        } else {
            // 默认
            return 'zh-cn';
        }
    }

    /**
     * @param string $key
     * @param string $default
     * @param bool $whenDefaultThenFail
     * @return string
     * @throws NoParamException
     */
    public function getParam($key = '', $default = '', $whenDefaultThenFail = false)
    {
        $data = $this->context->getDecryptData();
        if (!array_key_exists($key, $data)) {
            if ($whenDefaultThenFail === true) {
                throw new NoParamException("parameter `" . $key . "` is need");
            }
            $value = $default;
        } else {
            $value = $data[$key];
        }

        return $value;
    }

    /*
     * 当前请求环境下的发起者用户ID，没有也可能
     */

    /**
     * 设置当前环境
     * @param ByRequestContext $context
     */
    public function setContext(ByRequestContext $context)
    {
        $this->context = $context;
        if ($this->request) {
            $this->request->setLocale($this->getLang());
        }
    }

    /**
     * 获取clientId 对应的 用户id
     * @return int
     */
    protected function getClientUid()
    {
        return $this->context->getClientInfo()->getUid();
    }

    /**
     * 获取传过来的ClientId
     * @return string
     */
    protected function getClientId()
    {
        return $this->context->getClientId();
    }

    /**
     * 获取传过来的uid
     * @return string
     */
    protected function getUid()
    {
        return $this->context->getUid();
    }

    /**
     * 获取当前请求环境下的SessionId 与 session 无关，是登录后产生的凭证
     * 会校验这个作为用户UID的有效性
     * @return string
     */
    protected function getSId()
    {
        return $this->context->getSid();
    }

    /**
     * 获取当前请求clientId对应的项目id
     * @return string
     */
    protected function getProjectId()
    {
        return $this->context->getProjectId();
    }

    /**
     * 获取当前请求服务类型
     * @return string
     */
    protected function getServiceType()
    {
        return $this->context->getServiceType();
    }

    /**
     * 请求的接口版本号
     * @return mixed
     */
    protected function getServiceVersion()
    {
        return $this->context->getServiceVersion();
    }

    /**
     * 当前请求接口的应用类型（ios,android,web,h5,等等）
     * @return string
     */
    protected function getAppType()
    {
        return ByAppType::getAppType($this->context->getAppType());
    }

    /**
     * 当前请求接口的应用版本（符合版本规则）
     * @return string
     */
    protected function getAppVersion()
    {
        return $this->context->getAppVersion();
    }
}
