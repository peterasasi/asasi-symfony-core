<?php
/**
 * Created by PhpStorm.
 * User: peter
 * Date: 2018/12/24
 * Time: 11:30
 */

namespace Dbh\SfCoreBundle\Common;


interface UserLogServiceInterface extends BaseServiceInterface
{
    public function log($uid, $note, $logType, $ip, $deviceType, $ua);
}
