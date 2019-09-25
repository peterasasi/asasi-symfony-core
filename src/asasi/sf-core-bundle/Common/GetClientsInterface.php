<?php

namespace Dbh\SfCoreBundle\Common;

/**
 * Interface GetClientsInterface
 * 获取clients
 * @package Dbh\SfCoreBundle\Common
 */
interface GetClientsInterface
{
    /**
     * 获取
     * @param $clientId
     * @return ClientsInterface|null
     */
    function getClientBy($clientId): ?ClientsInterface;
}
