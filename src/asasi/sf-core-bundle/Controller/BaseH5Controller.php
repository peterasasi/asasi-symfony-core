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
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\KernelInterface;

abstract class BaseH5Controller extends AbstractController implements GetRequestInterface
{
    protected static $user;
    /**
     * @var LoginSessionInterface
     */
    protected $loginSession;
    protected $userAccountService;
    protected $kernel;
    /**
     * @var Request
     */
    protected $request;

    public function __construct(UserAccountServiceInterface $userAccountService, LoginSessionInterface $loginSession,
                                KernelInterface $kernel)
    {
        $this->kernel = $kernel;
        $this->loginSession = $loginSession;
        $this->userAccountService = $userAccountService;
        self::$user = null;
    }

    public function setRequest(Request $request)
    {
        $this->request = $request;
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

    protected function getUid()
    {
        return $this->request->cookies->get('uid', '');
    }

    /**
     * @throws NotLoginException
     */
    protected function checkLogin()
    {
        $sid = $this->request->cookies->get('sid', '');
        $uid = $this->getUid();
        $deviceType = $this->request->cookies->get('deviceType', '');
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
        $logService->log($this->getUid(), $note, ByUserLogType::Operation, $this->request->getClientIp(), "h5", $this->request->headers->get('user-agent') ?? "");
    }
}
