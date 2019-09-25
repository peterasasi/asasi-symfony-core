<?php

namespace Dbh\SfCoreBundle\Common;

use by\component\paging\vo\PagingParams;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMException;

abstract class BaseService implements BaseServiceInterface
{
    /**
     * @var BaseRepository
     */
    protected $repo;

    public function findById($id, $lockMode = null, $lockVersion = null) {
        return $this->repo->find($id, $lockMode, $lockVersion);
    }

    public function getEntityManager(): EntityManager
    {
        return $this->repo->getEntityManager();
    }

    /**
     * @return mixed
     */
    public function getLastSQL()
    {
        return $this->repo->getLastSql();
    }


    /**
     * @param $entity
     * @param bool $noFlush
     * @return mixed
     * @throws ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function add($entity, $noFlush = false)
    {
        return $this->repo->add($entity, $noFlush);
    }

    /**
     * 持久化删除
     * @param $entity
     * @throws ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function delete($entity)
    {
        return $this->repo->delete($entity);
    }

    /**
     * 手动刷新
     * @param null $entity
     * @throws ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function flush($entity = null)
    {
        return $this->repo->flush($entity);
    }

    /**
     * 更新单个记录
     * @param array $map
     * @param array $updateArr
     * @return null|object
     * @throws ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function updateOne($map, $updateArr = [])
    {
        return $this->repo->updateOne($map, $updateArr);
    }

    /**
     * 删除
     * @param $map
     * @return mixed
     */
    public function deleteWhere($map)
    {
        return $this->repo->deleteWhere($map);
    }

    /**
     * 更新
     * 1. 仅支持and的条件 和 thinkphp 类似
     * @param array $map 条件
     * @param array $updateArr
     * @return mixed
     */
    public function updateWhere($map, $updateArr = [])
    {
        return $this->repo->updateWhere($map, $updateArr);
    }

    /**
     * 仅支持2层数组，以及 like,eq, lt, lte, neq, gt, gte
     * 比如以下：表示 查询语句 uid = 1 expire_time > 0 and expire_time < 1000
     * ['uid' => 1, 'expire_time'=>['gt', 0, 'lt', 1000]]
     *
     * @param array $map 过滤的条件
     * @param PagingParams $pagingParams 分页条件
     * @param array $order 键值对的数组
     * @param string $fields 查询的字段
     * @return mixed
     */
    public function queryBy($map, PagingParams $pagingParams, $order = [], $fields = null)
    {
        return $this->repo->queryBy($map, $pagingParams, $order, $fields);
    }

    public function queryAllBy($map, $order = [], $fields = null)
    {
        return $this->repo->queryAllBy($map, $order, $fields);
    }

    public function info(array $criteria, array $orderBy = null)
    {
        return $this->repo->findOneBy($criteria, $orderBy);
    }

    /**
     * @param array $criteria
     * @param string $field
     * @return mixed
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function sum(array $criteria, string $field)
    {
        return $this->repo->sum($criteria, $field);
    }

    public function count($criteria, $field = "id")
    {
        return $this->repo->enhanceCount($criteria, $field);
    }

    public function queryAndCount($map, PagingParams $pagingParams, $order = [], $fields = null)
    {
        return $this->repo->queryAndCount($map, $pagingParams, $order, $fields);
    }
}
