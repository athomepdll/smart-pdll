<?php


namespace App\Entity;

/**
 * Class FinancialField
 * @package App\Entity
 */
class FinancialField extends Enumeration
{
    const FINANCIAL_FIELDS_FORM_VALIDATION = [
        'Nom du projet' => 'Nom du projet',
        'Coût du projet HT' => 'Coût du projet HT',
        'Subvention' => 'Subvention',
        'Thème' => 'Thème'
    ];

    const PROJECT_NAME = 'projectName';
    const PROJECT_COST_HT = 'projectCostHt';
    const GRANT = 'grant';
    const THEME = 'theme';

    const PROJECT_NAME_VALUE = 'Coût du projet HT';
    const PROJECT_COST_HT_VALUE = 'Coût du projet HT';
    const GRANT_VALUE = 'Subvention';
    const THEME_VALUE = 'Thème';

    const TYPE = [
        'Nom du projet' => 'varchar',
        'Coût du projet HT' => 'numeric',
        'Subvention' => 'numeric',
        'Thème' => 'varchar'
    ];
}
