<?php
/**
 * Created by PhpStorm.
 * User: asasi
 * Date: 2018/8/9
 * Time: 16:12
 */

namespace Dbh\SfCoreBundle\Subscriber;


use Doctrine\Common\EventSubscriber;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;

class PrepareEntitySubscriber implements EventSubscriber
{

    public function getSubscribedEvents()
    {
        return [
            Events::prePersist,
            Events::preUpdate,
        ];
    }


    public function prePersist(LifecycleEventArgs $eventArgs)
    {

        $obj = $eventArgs->getObject();
        if (method_exists($obj, "setCreateTime")) {
            if (method_exists($obj, "getCreateTime")
                && $obj->getCreateTime() === 0) {
                $obj->setCreateTime(time());
            }
        }

        if (method_exists($obj, "setUpdateTime")) {
            if (method_exists($obj, "getUpdateTime")
                && $obj->getUpdateTime() === 0) {
                $obj->setUpdateTime(time());
            }
        }
    }

    /**
     * 这种也只能针对单个，如果是批量更新 要同时更新时间字段
     * @param LifecycleEventArgs $eventArgs
     */
    public function preUpdate(LifecycleEventArgs $eventArgs)
    {
        $obj = $eventArgs->getObject();
        if (method_exists($obj, "setUpdateTime")) {
            if (method_exists($obj, "getUpdateTime")) {
                $obj->setUpdateTime(time());
            }
        }
    }
}
