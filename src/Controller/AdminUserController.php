<?php


namespace App\Controller;

use App\Entity\User;
use Athome\UserBundle\Model\UserManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Controller\EasyAdminController as BaseController;

/**
 * Class AdminUserController
 * @package App\Controller
 */
class AdminUserController extends BaseController
{

    /**
     * @var UserManagerInterface
     */
    private $userManager;

    /**
     * AdminUserController constructor.
     * @param UserManagerInterface $userManager
     */
    public function __construct(
        UserManagerInterface $userManager
    ) {
        $this->userManager = $userManager;
    }

    /**
     * Creates a new object of the current managed entity.
     * This method is mostly here for override convenience, because it allows
     * the user to use his own method to customize the entity instantiation.
     *
     * @return object
     */
    public function createNewEntity()
    {
        $user = new User();
        $user->addRole('ROLE_EXPERT');
        $user->setEnabled(true);

        return $user;
    }

    /**
     * Allows applications to modify the entity associated with the item being
     * created while persisting it.
     *
     * @param object $entity
     */
    public function persistEntity($entity)
    {
        if ($entity->getPlainPassword()) {
            $this->userManager->updatePassword($entity);
        }
        parent::persistEntity($entity);
    }

    /**
     * Allows applications to modify the entity associated with the item being
     * edited before updating it.
     *
     * @param object $entity
     */
    public function updateEntity($entity)
    {
        if ($entity->getPlainPassword()) {
            $this->userManager->updatePassword($entity);
        }
        parent::updateEntity($entity);
    }
}
