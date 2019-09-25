<?php


namespace Dbh\SfCoreBundle\Common;


use by\infrastructure\base\BaseEntity;
use by\infrastructure\interfaces\CheckInterfaces;
use InvalidArgumentException;

/**
 * Class ApiRequestPo
 * 接口通用参数 - 不管采用何种传输算法
 * @package app\api\controller\dto
 */
class ByRequestContext extends BaseEntity implements CheckInterfaces
{

    private $decryptData;
    /**
     * 通信算法
     * @var string
     */
    private $alg;
    /**
     * 请求的项目id
     * @var string
     */
    private $projectId;
    /**
     * 应用信息
     * @var
     */
    private $clientInfo;
    /**
     * 应用端标识
     * @var string
     */
    private $clientId;
    /**
     * 应用端标识对应密钥
     * @var string
     */
    private $clientSecret;
    /**
     * 请求编号
     * @var string
     */
    private $notifyId;
    /**
     * 发起请求应用的类型
     * @var string
     */
    private $appType;
    /**
     * 发起请求应用版本号
     * @var string
     */
    private $appVersion;
    /**
     * 客户端发起请求的时间（基于客户端时间）
     * @var string
     */
    private $appRequestTime;
    /**
     * 请求服务的版本号
     * @var string
     */
    private $serviceVersion;
    /**
     * 请求的语言
     * @var string
     */
    private $lang;
    /**
     * 请求服务
     * @var string
     */
    private $serviceType;
    /**
     * 当前请求传输的用户ID如果有，
     * @var string
     */
    private $uid;
    /**
     * 当前请求的会话ID如果有，会检测用户会话是否有效，如果没有 则在启用权限验证下，会验证clientUid是否有相应的权限
     * @var string
     */
    private $sid;
    /**
     * 传输给业务的数据
     * @var array
     */
    private $data;

    function check()
    {
        if (empty($this->getLang())) {
            $this->setLang("en");
        }
        if (empty($this->getClientId())) {
            throw new InvalidArgumentException("invalid client_id");
        }

        if (empty($this->getServiceType())) {
            throw new InvalidArgumentException("invalid service_type");
        }

        if (empty($this->getAppType())) {
            throw new InvalidArgumentException("invalid app_type");
        }

        if (empty($this->getAppVersion())) {
            throw new InvalidArgumentException("invalid app_version");
        }

    }

    /**
     * @return string
     */
    public function getLang()
    {
        return $this->lang;
    }

    /**
     * @param string $lang
     */
    public function setLang($lang)
    {
        $this->lang = $lang;
    }

    /**
     * @return string
     */
    public function getClientId()
    {
        return $this->clientId;
    }

    /**
     * @param string $clientId
     */
    public function setClientId($clientId)
    {
        $this->clientId = $clientId;
    }

    /**
     * @return string
     */
    public function getServiceType()
    {
        return $this->serviceType;
    }

    /**
     * @param string $serviceType
     */
    public function setServiceType($serviceType)
    {
        $this->serviceType = $serviceType;
    }

    /**
     * @return string
     */
    public function getAppType()
    {
        return $this->appType;
    }

    /**
     * @param string $appType
     */
    public function setAppType($appType)
    {
        $this->appType = $appType;
    }

    /**
     * @return string
     */
    public function getAppVersion()
    {
        return $this->appVersion;
    }

    /**
     * @param string $appVersion
     */
    public function setAppVersion($appVersion)
    {
        $this->appVersion = $appVersion;
    }

    /**
     * Clients Object
     * @return object
     */
    public function getClientInfo()
    {
        return $this->clientInfo;
    }

    /**
     * must be Clients object
     * @param  $clientInfo
     */
    public function setClientInfo($clientInfo): void
    {
        $this->clientInfo = $clientInfo;
    }

    /**
     * @return mixed
     */
    public function getDecryptData()
    {
        return $this->decryptData;
    }

    /**
     * @param mixed $decryptData
     */
    public function setDecryptData($decryptData)
    {
        $this->decryptData = $decryptData;
    }

    /**
     * @return mixed
     */
    public function getAlg()
    {
        return $this->alg;
    }

    /**
     * @param mixed $alg
     */
    public function setAlg($alg)
    {
        $this->alg = $alg;
    }

    /**
     * @return string
     */
    public function getUid()
    {
        return $this->uid;
    }

    /**
     * @param string $uid
     */
    public function setUid($uid)
    {
        $this->uid = $uid;
    }

    /**
     * @return string
     */
    public function getSid()
    {
        return $this->sid;
    }

    /**
     * @param string $sid
     */
    public function setSid($sid)
    {
        $this->sid = $sid;
    }

    /**
     * @return string
     */
    public function getProjectId()
    {
        return $this->projectId;
    }

    /**
     * @param string $projectId
     */
    public function setProjectId($projectId)
    {
        $this->projectId = $projectId;
    }

    /**
     * @return string
     */
    public function getNotifyId()
    {
        return $this->notifyId;
    }

    /**
     * @param string $notifyId
     */
    public function setNotifyId($notifyId)
    {
        $this->notifyId = $notifyId;
    }

    /**
     * @return string
     */
    public function getClientSecret()
    {
        return $this->clientSecret;
    }

    /**
     * @param string $clientSecret
     */
    public function setClientSecret($clientSecret)
    {
        $this->clientSecret = $clientSecret;
    }

    /**
     * @return string
     */
    public function getAppRequestTime()
    {
        return $this->appRequestTime;
    }

    /**
     * @param string $appRequestTime
     */
    public function setAppRequestTime($appRequestTime)
    {
        $this->appRequestTime = $appRequestTime;
    }

    /**
     * @return string
     */
    public function getServiceVersion()
    {
        return $this->serviceVersion;
    }

    /**
     * @param string $serviceVersion
     */
    public function setServiceVersion($serviceVersion)
    {
        $this->serviceVersion = $serviceVersion;
    }

    /**
     *
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param array $data
     */
    public function setData(array $data)
    {
        $this->data = $data;
    }

}
