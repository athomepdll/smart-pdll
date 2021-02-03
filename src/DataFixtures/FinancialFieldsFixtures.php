<?php


namespace App\DataFixtures;

use App\Entity\FinancialField;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * Class FinancialFieldsFixtures
 * @package App\DataFixtures
 */
class FinancialFieldsFixtures extends Fixture
{
    const FINANCIAL_FIELDS = [
        'projectName' => ['Nom du projet', 1],
        'projectCostHt' => ['Coût du projet HT', 4],
        'grant' => ['Subvention', 2],
        'theme' => ['Thématique', 3]
    ];

    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        foreach (self::FINANCIAL_FIELDS as $key => $field) {
            $financialField = new FinancialField();
            $financialField->setName($key);
            $financialField->setPosition($field[1]);
            $financialField->setValue($field[0]);

            $manager->persist($financialField);
        }

        $manager->flush();
    }
}
