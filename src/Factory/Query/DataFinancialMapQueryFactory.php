<?php


namespace App\Factory\Query;

use App\Entity\FinancialField;

/**
 * Class DataFinancialMapQuery
 * @package App\Factory\Query
 */
class DataFinancialMapQueryFactory extends AbstractSqlFactory
{
    const SELECT = [
        "CONCAT(data_view.city_name, ', ', data_view.import_model_name, ' ', data_view.year)",
        'data_view.city_id',
        'COALESCE(actual_city.name, city_name) as city_name',
        'data_view.import_model_name',
        'data_view.import_model_color',
        'st_x(ST_Centroid(polygons)) as long',
        'st_y(ST_Centroid(polygons)) as lat'
    ];

    const ORDER_BY = [
        'data_view.import_model_name'
    ];

    const GROUP_BY = [
        'data_view.city_name',
        'data_view.import_model_name',
        'data_view.import_model_color',
        'data_view.year',
        'data_view.city_id',
        'data_view.city_name',
        'data_view.department_id',
        'data_view.district_id',
        'place.polygons',
        'data_view.import_model_id',
        'actual_city.name',
    ];

    /**
     * @var ProjectGrantQueryFactory
     */
    private $projectGrantQueryFactory;
    /**
     * @var LocalizedInfoQueryFactory
     */
    private $localizedQueryFactory;

    /**
     * DataFinancialMapQueryFactory constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->projectGrantQueryFactory = new ProjectGrantQueryFactory();
        $this->localizedQueryFactory = new LocalizedInfoQueryFactory();
    }

    /**
     * @param array $filters
     * @return string
     */
    public function create(array $filters)
    {
        $sumProjectGrant = $this->getSumProjectGrantQuery($filters);
        $totalSumProjectGrantForCity = $this->getTotalSumProjectGrantQuery($filters);


        $select = JOIN(', ', self::SELECT);

        $sourceQuery = "SELECT {$select}, ({$sumProjectGrant}) as sum_grant, ({$totalSumProjectGrantForCity}) as total_sum_grant";
        $sourceQuery .= " FROM data_view";
        $sourceQuery .= " JOIN enumeration on enumeration.value = data_view.column_name ";
        $sourceQuery .= " LEFT JOIN city actual_city ON actual_city.id = data_view.actual_city_id";
        $sourceQuery .= " LEFT JOIN place on place.code = data_view.city_insee WHERE";

        $this->sqlQueryBuilderHelper
            ->addDataLevelFilter($filters, $sourceQuery)
            ->addEnumeration(FinancialField::GRANT_VALUE, $sourceQuery)
            ->addImportModelIds($filters, $sourceQuery)
            ->addYearStart($filters, $sourceQuery, 'data_view')
            ->addYearEnd($filters, $sourceQuery, 'data_view')
            ->addDepartmentFilter($filters, $sourceQuery, 'data_view')
            ->addDistrict($filters, $sourceQuery, 'data_view')
            ->addEpci($filters, $sourceQuery, 'data_view')
            ->addCity($filters, $sourceQuery)
            ->addUndefined($filters, $sourceQuery, 'data_view')
            ->addSirenCarrierWithHistory($filters, $sourceQuery, 'data_view');

        $orderBy = JOIN(', ', self::ORDER_BY);
        $groupBy = JOIN(', ', self::GROUP_BY);

        return $sourceQuery .= " GROUP BY {$groupBy} ORDER BY {$orderBy}";
    }

    /**
     * @param array $filters
     * @return string
     */
    private function getSumProjectGrantQuery(array $filters)
    {
        return $this->projectGrantQueryFactory->create(
            $filters,
            ProjectGrantQueryFactory::GET_SUMMED_DATA,
            ProjectGrantQueryFactory::SUM
        );
    }

    /**
     * @param array $filters
     * @return string
     */
    private function getTotalSumProjectGrantQuery(array $filters)
    {
        return $this->projectGrantQueryFactory->create(
            $filters,
            ProjectGrantQueryFactory::GET_SUMMED_CITY_DATA,
            ProjectGrantQueryFactory::SUM
        );
    }
}
