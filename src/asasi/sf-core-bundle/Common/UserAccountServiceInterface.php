<?php
/**
 * Created by PhpStorm.
 * User: asasi
 * Date: 2018/8/8
 * Time: 11:04
 */

namespace Dbh\SfCoreBundle\Common;

use by\infrastructure\base\CallResult;

interface UserAccountServiceInterface extends BaseServiceInterface
{
    /**
     * @param $mobile
     * @param $ip
     * @param $projectId
     * @param string $countryNo
     * @return CallResult
     */
    function getUserOrCreate($mobile, $ip, $projectId, $countryNo = '86');

    function create(UserAccountInterface $userAccount, UserProfileInterface $userProfile);

    function delete($userAccount);

    function findOne($map);

    function updatePassword($map, $newPwd);
}
