<?php


namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * Class UserFixtures
 * @package App\DataFixtures
 */
class UserFixtures extends Fixture implements FixtureGroupInterface
{

    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $admin = new User();
        $admin->addRole('ROLE_ADMIN');
        $admin->lastName = 'admin';
        $admin->firstName = 'admin';
        $admin->setEmail('admin@athome-solution.fr');
        $admin->setPassword('$2y$10$ePWPcgSiDerTXz16Pbz.IePQERQHqYrzWTxCxH89gSuZOlLRL9VAK');
        $admin->setEnabled(true);

        $manager->persist($admin);

        $superAdmin = new User();
        $superAdmin->addRole('ROLE_ADMIN');
        $superAdmin->lastName = 'super admin';
        $superAdmin->firstName = 'super admin';
        $superAdmin->setEmail('super_admin@athome-solution.fr');
        $superAdmin->setPassword('$2y$10$Y6NyYVv/bsXavnugbHdtkOJpF5JEJf5x3eqt.VSjpGY1EWJuzI6hq');
        $superAdmin->setEnabled(true);

        $manager->persist($superAdmin);

        $user = new User();
        $user->addRole('ROLE_VIEWER');
        $user->setEmail('smart@smart.fr');
        $user->setEnabled(true);
        $user->setPassword('2y$10$hlLmXfjrZ5pkXYFBiNlJde6KxahYWuanMUP9wLli/E3uKHlzIm3Ga');
        $user->lastName = 'smart';
        $user->firstName = 'viewer';

        $manager->persist($user);

        $manager->flush();
    }

    /**
     * This method must return an array of groups
     * on which the implementing class belongs to
     *
     * @return string[]
     */
    public static function getGroups(): array
    {
        return ['user'];
    }
}
