<?php


namespace App\Factory\Query;

use App\Entity\FinancialField;

/**
 * Class LocalisedInfoQueryFactory
 * @package App\Factory\Query
 */
class LocalizedInfoQueryFactory extends AbstractSqlFactory
{
    private const GROUP_BY = [
        'data_view.year',
        'data_view.department_id',
    ];

    private $projectGrant = FinancialField::GRANT;

    /**
     * @param array $filters
     * @return string
     */
    public function create(array $filters)
    {

        $totalSum = $this->getTotalSum($filters);
        $totalSumLocalized = $this->getTotalSumLocalized($filters);
        $totalSumNotLocalized = $this->getTotalSumNotLocalized($filters);
        $countLocalized = $this->getCountLocalized($filters);
        $countTotal = $this->getCountTotal($filters);


        $sourceQuery = "SELECT ";
        $sourceQuery .= " ({$totalSum}) as total_sum, ({$totalSumLocalized}) as total_sum_localized,";
        $sourceQuery .= " ({$totalSumNotLocalized}) as total_sum_not_localized,";
        $sourceQuery .= " ({$countLocalized}) as count_localized, ({$countTotal}) as count_total";
        $sourceQuery .= " FROM data_view";
        $sourceQuery .= " JOIN enumeration on enumeration.value = data_view.column_name ";
        $sourceQuery .= " left JOIN place on place.code = data_view.city_insee WHERE";

        $this->sqlQueryBuilderHelper
            ->addDataLevelFilter($filters, $sourceQuery)
            ->addEnumeration(FinancialField::GRANT_VALUE, $sourceQuery)
            ->addImportModelIds($filters, $sourceQuery)
            ->addYearStart($filters, $sourceQuery)
            ->addYearEnd($filters, $sourceQuery)
            ->addDepartmentFilter($filters, $sourceQuery)
            ->addDistrict($filters, $sourceQuery)
            ->addEpci($filters, $sourceQuery)
            ->addCity($filters, $sourceQuery)
            ->addSirenCarrierWithHistory($filters, $sourceQuery, 'data_view');

        $groupBy = JOIN(', ', self::GROUP_BY);

        return $sourceQuery . " GROUP BY {$groupBy}";
    }

    /**
     * @param array $filters
     * @return string
     */
    public function getTotalSum(array $filters)
    {
        return $this->getQuery($filters, 'cast(d.value as numeric)', 'SUM');
    }


    /**
     * @param array $filters
     * @return string
     */
    public function getTotalSumLocalized(array $filters)
    {
        return $this->getQuery($filters, 'cast(d.value as numeric)', 'SUM', 'IS NOT NULL');
    }

    /**
     * @param array $filters
     * @return string
     */
    public function getTotalSumNotLocalized(array $filters)
    {
        return $this->getQuery($filters, 'cast(d.value as numeric)', 'SUM', 'IS NULL');
    }

    /**
     * @param array $filters
     * @return string
     */
    public function getCountLocalized(array $filters)
    {
        return $this->getQuery($filters, '*', 'COUNT', 'IS NOT NULL');
    }

    /**
     * @param array $filters
     * @return string
     */
    public function getCountTotal(array $filters)
    {
        return $this->getQuery($filters, '*', 'COUNT');
    }

    /**
     * @param array $filters
     * @param string $fieldName
     * @param string $aggregateType
     * @param string $localizationType
     * @return string
     */
    private function getQuery(array $filters, string $fieldName, string $aggregateType, string $localizationType = '')
    {
        $query = "select {$aggregateType}({$fieldName})";
        $query .= " from data_view d";
        $query .= " join enumeration en on en.value = d.column_name";
        $query .= " where en.name = '{$this->projectGrant}'";

        $this->sqlQueryBuilderHelper
            ->addDepartmentFilter($filters, $query, 'd')
            ->addDistrict($filters, $query, 'd')
            ->addCity($filters, $query, 'd')
            ->addEpci($filters, $query, 'd')
            ->addSirenCarrierWithHistory($filters, $query, 'd')
            ->addYearStart($filters, $query, 'd')
            ->addYearEnd($filters, $query, 'd')
        ;

        if ($localizationType !== '') {
            $this->sqlQueryBuilderHelper->addUndefined($filters, $query, 'd', $localizationType);
        }

        if (empty($filters['importModels'])) {
            return '0';
        }
        $joinedIds = join(', ', $filters['importModels']);
        return $query . " and d.import_model_id in ({$joinedIds})";
    }
}
