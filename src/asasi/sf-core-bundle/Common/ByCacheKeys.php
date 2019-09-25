<?php
// +----------------------------------------------------------------------
// |
// +----------------------------------------------------------------------
// | ©2018 California State Lottery All rights reserved.
// +----------------------------------------------------------------------
// | Author: Smith Jack
// +----------------------------------------------------------------------

namespace Dbh\SfCoreBundle\Common;

class ByCacheKeys
{

    const ClientId = "sys_client_id_";
    // 支付渠道开关
    const PaymentChannelSwitch = 'sys_payment_switch';

    // 接口入口的授权策略缓存
    const ApiIndexAuthPolices = "api_index_auth_polices_";

    public static $ExpireTimes = [
        ByCacheKeys::ApiIndexAuthPolices => 3600,
        ByCacheKeys::ClientId => 3600,
        ByCacheKeys::PaymentChannelSwitch => 3600
    ];

    public static function getExpireTime($key)
    {
        if (array_key_exists($key, self::$ExpireTimes)) {
            return self::$ExpireTimes[$key];
        } else {
            return 30;
        }
    }
}
