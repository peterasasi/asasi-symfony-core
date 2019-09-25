<?php


namespace Dbh\SfCoreBundle\Common;


interface UserAccountInterface
{
    public function getProfile(): ?UserProfileInterface;
}
