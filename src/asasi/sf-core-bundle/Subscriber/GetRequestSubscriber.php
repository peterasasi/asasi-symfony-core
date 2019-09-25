<?php
/**
 * Created by PhpStorm.
 * User: asasi
 * Date: 2018/8/7
 * Time: 11:16
 */

namespace Dbh\SfCoreBundle\Subscriber;


use Dbh\SfCoreBundle\Common\GetRequestInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * 获取request
 * @package App\Listeners
 */
class GetRequestSubscriber implements EventSubscriberInterface
{


    public static function getSubscribedEvents()
    {
        return array(
            KernelEvents::CONTROLLER => ['onKernelController', 20],
        );
    }

    /**
     * @param ControllerEvent $event
     */
    public function onKernelController(ControllerEvent $event)
    {
        $controller = $event->getController();

        if (!is_array($controller)) {
            return;
        }

        if ($controller[0] instanceof GetRequestInterface) {
            $controller[0]->setRequest($event->getRequest());
        }
    }
}
