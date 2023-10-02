<?php

namespace core\entity;

use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Table;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\UniqueConstraint;

/**
 * @Entity
 * @Table(name="users",uniqueConstraints={@UniqueConstraint(name="id_unique", columns={"id"})})
 */
class User
{
    /** @Id @Column(type="integer") @GeneratedValue */
    public int $id;

    /** @Column(type="string") */
    public string $google_uid;

    /** @Column(type="string") */
    public string $email;

    /** @Column(type="string") */
    public string $name;

    /** @Column(type="string") */
    public string $picture_url;
}