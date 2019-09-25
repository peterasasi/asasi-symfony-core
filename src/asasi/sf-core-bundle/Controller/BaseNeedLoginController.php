<?php
// +----------------------------------------------------------------------
// |
// +----------------------------------------------------------------------
// | ©2018 California State Lottery All rights reserved.
// +----------------------------------------------------------------------
// | Author: Smith Jack
// +----------------------------------------------------------------------

namespace Dbh\SfCoreBundle\Controller;

use by\component\exception\NotLoginException;
use by\infrastructure\base\CallResult;
use Dbh\SfCoreBundle\Common\ByUserLogType;
use Dbh\SfCoreBundle\Common\GetRequestInterface;
use Dbh\SfCoreBundle\Common\LoginSessionInterface;
use Dbh\SfCoreBundle\Common\UserAccountInterface;
use Dbh\SfCoreBundle\Common\UserAccountServiceInterface;
use Dbh\SfCoreBundle\Common\UserLogServiceInterface;
use Symfony\Component\HttpKernel\KernelInterface;

abstract class BaseNeedLoginController extends BaseSymfonyApiController implements GetRequestInterface
{
    protected static $user;
    /**
     * @var LoginSessionInterface
     */
    protected $loginSession;
    protected $userAccountService;

    public function __construct(UserAccountServiceInterface $userAccountService, LoginSessionInterface $loginSession, KernelInterface $kernel)
    {
        parent::__construct($kernel);
        $this->loginSession = $loginSession;
        $this->userAccountService = $userAccountService;
        self::$user = null;
    }

    protected function getLoginUserNick()
    {
        $user = $this->getUser();
        if ($user instanceof UserAccountInterface) {
            return $user->getProfile()->getNickname();
        }
        return '';
    }

    protected function getUser()
    {
        if (!self::$user instanceof UserAccountInterface) {
            self::$user = $this->userAccountService->info(['id' => $this->getUid()]);
        }
        return self::$user;
    }

    /**
     * @throws NotLoginException
     */
    protected function checkLogin()
    {
        $sid = $this->getSId();
        $uid = $this->getUid();
        $deviceType = $this->request->get('deviceType', '');
        if (!$this->kernel->isDebug()) {
            // 非正式环境下 开启调试模式
            $ret = $this->loginSession->check($uid, $sid, $deviceType);
            if ($ret instanceof CallResult && $ret->isFail()) {
                throw new NotLoginException('Please Login Again ' . $ret->getMsg());
            }
        }
    }

    protected function logUserAction(UserLogServiceInterface $logService, $note = '')
    {
        $logService->log($this->getUid(), $note, ByUserLogType::Operation, $this->request->getClientIp(), $this->getAppType() ?? "", $this->request->headers->get('user-agent') ?? "");
    }
}
