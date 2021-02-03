<?php


namespace App\Entity;

use Athome\UserBundle\Model\User as BaseUser;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Class User
 * @package App\Entity
 */
class User extends BaseUser
{
    public $firstName;

    public $lastName;

    /** @var Department */
    public $department;

    /** @var District */
    public $district;
}
