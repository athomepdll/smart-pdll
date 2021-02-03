<?php


namespace App\Factory\Query;

/**
 * Class DataViewFactory
 * @package App\Factory\Query
 */
class DataExportQueryFactory extends AbstractSqlFactory
{

    const SELECT = [
        'data_line_id as id',
        'city_name as "Nom Commune"',
        'carrier_name as "Nom structure porteuse"',
        'import_model_name as "Modèle d\'import"',
        'data_view.year as "Année"',
        'column_name',
        'value'
    ];

    const ORDER_BY = [
        'data_view.year',
    ];

    const GROUP_BY = [
        'data_line_id',
        'data_view.city_name',
        'data_view.carrier_name',
        'data_view.year',
        'data_view.import_model_name',
        'data_view.column_name',
        'data_view.value'
    ];

    /**
     * @var CityChangeQueryFactory
     */
    private $cityChangeQuery;
    /**
     * @var ProjectCostHtQueryFactory
     */
    private $projectCostHtQuery;
    /**
     * @var ProjectGrantQueryFactory
     */
    private $projectGrantQuery;

    /**
     * DataViewFactory constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->cityChangeQuery = new CityChangeQueryFactory();
        $this->projectCostHtQuery = new ProjectCostHtQueryFactory();
        $this->projectGrantQuery = new ProjectGrantQueryFactory();
    }

    /**
     * @param array $filters
     * @return string
     */
    public function create(array $filters)
    {
        $select = join(', ', self::SELECT);
        $sourceQuery = "SELECT {$select} FROM data_view ";
        $sourceQuery .= " WHERE ";

        $this->sqlQueryBuilderHelper
            ->addDataLevelFilter($filters, $sourceQuery)
            ->addImportModelId($filters, $sourceQuery, 'data_view')
            ->addYearStart($filters, $sourceQuery, 'data_view')
            ->addYearEnd($filters, $sourceQuery, 'data_view')
            ->addDepartmentFilter($filters, $sourceQuery, 'data_view')
            ->addDistrict($filters, $sourceQuery)
            ->addEpci($filters, $sourceQuery)
            ->addCity($filters, $sourceQuery)
            ->addSirenCarrierWithHistory($filters, $sourceQuery, 'data_view');

        return $sourceQuery . ' GROUP BY ' . join(', ', self::GROUP_BY) . ' ORDER BY ' . join(', ', self::ORDER_BY);
    }
}
