<?php
/**
 * Created by PhpStorm.
 * User: asasi
 * Date: 2018/8/7
 * Time: 11:12
 */

namespace Dbh\SfCoreBundle\Common;


/**
 * Interface ApiGuardInterface
 * 接口守护/接口上下文环境设置
 * @package App\Interfaces
 */
interface ApiGuardInterface
{
    public function setContext(ByRequestContext $context);
}
