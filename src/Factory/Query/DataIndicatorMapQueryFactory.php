<?php


namespace App\Factory\Query;

/**
 * Class DataIndicatorMapQueryFactory
 * @package App\Factory\Query
 */
class DataIndicatorMapQueryFactory extends AbstractSqlFactory
{

    const SELECT  = [
        'data_view.import_model_name as import_model_name',
        'data_view.import_model_color as import_model_color',
        'data_view.city_siren',
        "CONCAT(city_name, ', ', import_model_name, ' ', year) as name",
        'st_x(ST_Centroid(polygons)) as long',
        'st_y(ST_Centroid(polygons)) as lat'
    ];

    const GROUP_BY = [
        'data_view.import_model_name',
        'data_view.import_model_color',
        'data_view.city_siren',
        'data_view.city_name',
        'data_view.import_model_name',
        'data_view.year',
        'place.polygons'
    ];

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @param array $filters
     * @return string
     */
    public function create(array $filters)
    {
        $select = join(', ', self::SELECT);

        $sourceQuery = "SELECT {$select} FROM data_view";
        $sourceQuery .= " left join place on place.code = data_view.city_insee WHERE";

        $this->sqlQueryBuilderHelper
            ->addDataLevelFilter($filters, $sourceQuery)
            ->addImportModelId($filters, $sourceQuery)
            ->addYearStart($filters, $sourceQuery)
            ->addYearEnd($filters, $sourceQuery)
            ->addDepartmentFilter($filters, $sourceQuery)
            ->addDistrict($filters, $sourceQuery)
            ->addEpci($filters, $sourceQuery)
            ->addCity($filters, $sourceQuery)
            ->addSirenCarrier($filters, $sourceQuery);

        $groupBy = implode(', ', self::GROUP_BY);
        $sourceQuery .= " GROUP BY {$groupBy}";

        return $sourceQuery;
    }
}
