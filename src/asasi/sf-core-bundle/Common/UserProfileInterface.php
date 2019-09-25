<?php


namespace Dbh\SfCoreBundle\Common;


interface UserProfileInterface
{
    // 获取昵称
    public function getNickname();

    public function setNickname(string $nickname);

    // 设置头像
    public function setHead(string $head);

    public function getHead();
}
