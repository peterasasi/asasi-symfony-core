<?php
/**
 * Created by PhpStorm.
 * User: asasi
 * Date: 2018/8/15
 * Time: 17:51
 */

namespace Dbh\SfCoreBundle\Subscriber;


use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class LocaleSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return array(
            KernelEvents::REQUEST => ['onKernelRequest', 20]
        );
    }

    public function onKernelRequest(RequestEvent $event)
    {
        $lang = $event->getRequest()->request->get('lang', 'zh-cn');
        if (empty($lang)) $lang = "zh-cn";
        $event->getRequest()->setLocale($lang);
    }
}
