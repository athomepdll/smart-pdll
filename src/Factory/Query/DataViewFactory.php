<?php


namespace App\Factory\Query;

/**
 * Class DataViewFactory
 * @package App\Factory\Query
 */
class DataViewFactory extends AbstractSqlFactory
{

    const ORDER_BY = [
        'actual_city_name',
        'data_view.year',
        'import_model_name',
        'data_line_id'
    ];

    const GROUP_BY = [
        'data_line_id',
        'data_view_id',
        'city_id',
        'carrier_name',
        'data_view.year',
        'city_name',
        'import_model_name',
        'data_view.import_model_id',
        'data_view.column_name',
        'data_view.value',
        'data_view.department_id',
        'data_view.district_id',
        'data_view.siren_carrier',
        'actual_city.name',
        'actual_city.id'
    ];

    /**
     * @var CityChangeQueryFactory
     */
    private $cityChangeQueryFactory;
    /**
     * @var ProjectCostHtQueryFactory
     */
    private $projectCostHtQueryFactory;
    /**
     * @var ProjectGrantQueryFactory
     */
    private $projectGrantQueryFactory;

    /**
     * DataViewFactory constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->cityChangeQueryFactory = new CityChangeQueryFactory();
        $this->projectCostHtQueryFactory = new ProjectCostHtQueryFactory();
        $this->projectGrantQueryFactory = new ProjectGrantQueryFactory();
    }

    /**
     * @param array $filters
     * @return string
     */
    public function create(array $filters)
    {
        $cityChangeQuery = $this->cityChangeQueryFactory->create($filters);
        $projectCostHtQuery = $this->projectCostHtQueryFactory->create($filters);
        $projectGrant = $this->projectGrantQueryFactory->create($filters);
        $sourceQuery = "SELECT data_line_id, data_view_id, city_id,";
        $sourceQuery .= " CONCAT(COALESCE(actual_city.name, city_name), ', ', import_model_name, ' ', data_view.year),";
        $sourceQuery .= " COALESCE(actual_city.id, 0) as actual_city_id, carrier_name,";
        $sourceQuery .= " ({$cityChangeQuery}) as has_city_changed,";
        $sourceQuery .= " COALESCE(actual_city.name, city_name) as actual_city_name, data_view.year, city_name, import_model_name,";
        $sourceQuery .= " {$projectCostHtQuery} as total_ht, ({$projectGrant}) as total_grant, column_name, value ";
        $sourceQuery .= " FROM data_view";
        $sourceQuery .= " LEFT JOIN city actual_city ON actual_city.id = data_view.actual_city_id";
        $sourceQuery .= " WHERE ";

        $this->sqlQueryBuilderHelper
            ->addDataLevelFilter($filters, $sourceQuery)
            ->addImportModelId($filters, $sourceQuery, 'data_view')
            ->addYearStart($filters, $sourceQuery, 'data_view')
            ->addYearEnd($filters, $sourceQuery, 'data_view')
            ->addDepartmentFilter($filters, $sourceQuery)
            ->addDistrict($filters, $sourceQuery, 'data_view')
            ->addEpci($filters, $sourceQuery, 'data_view')
            ->addCity($filters, $sourceQuery)
            ->addSirenCarrierWithHistory($filters, $sourceQuery, 'data_view');

        $sourceQuery .= ' GROUP BY ' . join(', ', self::GROUP_BY) . ' ORDER BY ' . join(', ', self::ORDER_BY);

//        $sourceQuery .= " ORDER BY " . join(', ', self::ORDER_BY);

        return $sourceQuery;
    }
}
