<?php

namespace Dbh\SfCoreBundle\Common;

use by\component\paging\vo\PagingParams;
use Doctrine\ORM\EntityManager;

interface BaseServiceInterface
{
    public function findById($id, $lockMode = null, $lockVersion = null);

    public function getEntityManager(): EntityManager;

    public function getLastSQL();

    /**
     * 满足条件删除
     * @param $map
     */
    public function deleteWhere($map);

    /**
     * @param $entity
     * @param bool $noFlush
     * @return mixed
     */
    public function add($entity, $noFlush = false);

    /**
     * 持久化删除
     * @param $entity
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function delete($entity);

    /**
     * 手动刷新
     * @param null $entity
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function flush($entity = null);

    /**
     * 更新单个记录
     * @param array $map
     * @param array $updateArr
     * @return null|object
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function updateOne($map, $updateArr = []);

    /**
     * 更新
     * 1. 仅支持and的条件 和 thinkphp 类似
     * @param array $map 条件
     * @param array $updateArr
     * @return mixed
     */
    public function updateWhere($map, $updateArr = []);

    /**
     * 注意这里返回的是一个单纯的数组，而不是数组包含了 Entity对象,
     * 仅支持2层数组，以及 eq, lt, lte, neq, gt, gte
     * 比如以下：表示 查询语句 uid = 1 expire_time > 0 and expire_time < 1000
     * ['uid' => 1, 'expire_time'=>['gt', 0, 'lt', 1000]]
     *
     * @param array $map 过滤的条件
     * @param PagingParams $pagingParams 分页条件
     * @param array $order 键值对的数组
     * @param string $fields 查询的字段
     * @return mixed
     */
    public function queryBy($map, PagingParams $pagingParams, $order = [], $fields = null);


    /**
     * 注意这里返回的是一个单纯的数组，而不是数组包含了 Entity对象,
     * 仅支持2层数组，以及 eq, lt, lte, neq, gt, gte
     * 比如以下：表示 查询语句 uid = 1 expire_time > 0 and expire_time < 1000
     * ['uid' => 1, 'expire_time'=>['gt', 0, 'lt', 1000]]
     *
     * @param array $map 过滤的条件
     * @param PagingParams $pagingParams 分页条件
     * @param array $order 键值对的数组
     * @param string $fields 查询的字段
     * @return mixed
     */
    public function queryAndCount($map, PagingParams $pagingParams, $order = [], $fields = null);

    /**
     *
     * 注意这里返回的是一个单纯的数组，而不是数组包含了 Entity对象,
     * @param $map
     * @param array $order 键值对的数组 ["sort"=>"desc"]
     * @param null $fields
     * @return mixed
     */
    public function queryAllBy($map, $order = [], $fields = null);

    /**
     * 严格查询
     * @param array $criteria
     * @param array|null $orderBy
     * @return mixed
     */
    public function info(array $criteria, array $orderBy = null);

    /**
     * 统计和
     * @param array $criteria
     * @param string $field
     * @return mixed
     */
    public function sum(array $criteria, string $field);

    /**
     * 统计数量
     * @param $criteria
     * @param string $field
     * @return mixed
     */
    public function count($criteria, $field = "id");
}
