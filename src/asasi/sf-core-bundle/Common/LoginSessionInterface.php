<?php
/**
 * Created by PhpStorm.
 * User: asasi
 * Date: 2018/8/9
 * Time: 14:09
 */

namespace Dbh\SfCoreBundle\Common;

use by\component\paging\vo\PagingParams;

interface LoginSessionInterface
{
    function query($uid, PagingParams $pagingParams, $order = [], $fields = []);

    /**
     * 检测
     * @param $uid
     * @param $loginSessionId
     * @param $deviceType
     * @param int $sessionExpireTime
     * @return mixed
     */
    function check($uid, $loginSessionId, $deviceType, $sessionExpireTime = 1296000);

    /**
     * 登录
     * @param $uid
     * @param $deviceToken
     * @param $deviceType
     * @param $loginInfo
     * @param int $loginSessionMaxCount
     * @param int $sessionExpireTime
     * @return mixed
     */
    function login($uid, $deviceToken, $deviceType, $loginInfo, $loginSessionMaxCount = 1, $sessionExpireTime = 1296000);

    /**
     * 注销
     * @param $uid
     * @param $sId
     * @return mixed
     */
    function logout($uid, $sId);
}
