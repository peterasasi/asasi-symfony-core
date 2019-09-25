<?php

namespace Dbh\SfCoreBundle\Common;


interface ClientsInterface
{


    /**
     * @return int
     */
    public function getId(): int;

    /**
     * @param int $id
     */
    public function setId(int $id): void;

    /**
     * @return string
     */
    public function getClientSecret(): string;

    /**
     * @param string $clientSecret
     */
    public function setClientSecret(string $clientSecret): void;

    /**
     * @return string
     */
    public function getClientId(): string;

    /**
     * @param string $clientId
     */
    public function setClientId(string $clientId): void;

    /**
     * @return int
     */
    public function getUid(): int;

    /**
     * @param int $uid
     */
    public function setUid(int $uid): void;

    /**
     * @return int
     */
    public function getCreateTime(): int;

    /**
     * @param int $createTime
     */
    public function setCreateTime(int $createTime): void;

    /**
     * @return int
     */
    public function getUpdateTime(): int;

    /**
     * @param int $updateTime
     */
    public function setUpdateTime(int $updateTime): void;

    public function getDayLimit(): int;

    public function setDayLimit(int $dayLimit): self;

    public function getTotalLimit(): ?int;

    public function setTotalLimit(int $totalLimit): self;

    /**
     * @return string
     */
    public function getClientName(): string;

    /**
     * @param string $clientName
     */
    public function setClientName(string $clientName): void;

    /**
     * @return string
     */
    public function getProjectId(): string;

    /**
     * @param string $projectId
     */
    public function setProjectId(string $projectId): void;

    /**
     * @return string
     */
    public function getApiAlg(): string;

    /**
     * @param string $apiAlg
     */
    public function setApiAlg(string $apiAlg): void;

    public function getUserPrivateKey(): ?string;

    public function setUserPrivateKey(string $userPrivateKey): self;

    public function getUserPublicKey(): ?string;

    public function setUserPublicKey(string $userPublicKey): self;

    public function getSysPrivateKey(): ?string;

    public function setSysPrivateKey(string $sysPrivateKey): self;

    public function getSysPublicKey(): ?string;

    public function setSysPublicKey(string $sysPublicKey): self;

    public function setArrayData(array $data);

    public function toArrayData();
}
