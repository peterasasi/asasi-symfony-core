<?php


namespace Dbh\SfCoreBundle\Common;


class ByAppType
{
    const H5 = 'h5';

    const Mobile = 'mobile';

    const Web = 'web';

    const IOS = 'ios';

    const Android = 'android';

    const Pc = 'pc';

    const AdminPc = 'admin_pc';

    // 服务器用于php调用php接口
    const Server = 'server';

    const Unknown = 'unknown';

    public static function getAppType($appType)
    {
        switch ($appType) {
            case self::H5:
                return self::H5;
            case self::IOS:
                return self::IOS;
            case self::Android:
                return self::Android;
            case self::Pc:
                return self::Pc;
            case self::AdminPc:
                return self::AdminPc;
            case self::Server:
                return self::Server;
            default:
                return self::Unknown;
        }
    }
}
