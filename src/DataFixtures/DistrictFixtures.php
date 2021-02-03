<?php


namespace App\DataFixtures;

use App\Entity\Department;
use App\Entity\District;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * Class DistrictFixtures
 * @package App\DataFixtures
 */
class DistrictFixtures extends Fixture
{
    public function getDependencies()
    {
        return array(
            DepartmentFixtures::class,
        );
    }

    const DATA = [
        [
            'department' => '44',
            'code' => '442',
            'name' => 'Nantes'
        ],
        [
            'department' => '44',
            'code' => '443',
            'name' => 'Saint-Nazaire'
        ],
        [
            'department' => '44',
            'code' => '445',
            'name' => 'Châteaubriant-Ancenis'
        ],
        [
            'department' => '44',
            'code' => '441',
            'name' => 'Châteaubriant'
        ],
        [
            'department' => '44',
            'code' => '444',
            'name' => 'Ancenis'
        ],
        [
            'department' => '49',
            'code' => '491',
            'name' => 'Angers'
        ],
        [
            'department' => '49',
            'code' => '492',
            'name' => 'Cholet'
        ],
        [
            'department' => '49',
            'code' => '493',
            'name' => 'Saumur'
        ],
        [
            'department' => '49',
            'code' => '494',
            'name' => 'Segré'
        ],
        [
            'department' => '53',
            'code' => '531',
            'name' => 'Château-Gontier'
        ],
        [
            'department' => '53',
            'code' => '532',
            'name' => 'Laval'
        ],
        [
            'department' => '53',
            'code' => '533',
            'name' => 'Mayenne'
        ],
        [
            'department' => '72',
            'code' => '721',
            'name' => 'Flèche'
        ],
        [
            'department' => '72',
            'code' => '722',
            'name' => 'Mamers'
        ],
        [
            'department' => '72',
            'code' => '723',
            'name' => 'Mans'
        ],
        [
            'department' => '85',
            'code' => '851',
            'name' => 'Fontenay-le-Comte'
        ],
        [
            'department' => '85',
            'code' => '852',
            'name' => 'Roche-sur-Yon'
        ],
        [
            'department' => '85',
            'code' => '853',
            'name' => 'Sables-d\'Olonne'
        ],
    ];

    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $departmentRepository = $manager->getRepository(Department::class);
        foreach (self::DATA as $data) {
            $district = new District();
            $district->setName($data['name']);
            $district->setCode($data['code']);
            $department = $departmentRepository->findOneBy(['code' => $data['department']]);
            $district->setDepartment($department);

            $manager->persist($district);
        }

        $manager->flush();
    }
}
