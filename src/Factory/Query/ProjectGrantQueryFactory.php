<?php


namespace App\Factory\Query;

use App\Entity\CarryingStructure;
use App\Entity\FinancialField;
use App\Helper\SqlQueryBuilderHelper;

/**
 * Class ProjectGrantQuery
 * @package App\Factory\Query
 */
class ProjectGrantQueryFactory extends AbstractSqlFactory
{
    const BY_CITY_NAME_IMPORT_MODEL_YEAR = 'city_import_model_year';
    const BY_IMPORT_MODEL_YEAR = 'import_model_year';
    const BY_MAX_IMPORT_MODEL_YEAR = 'max_sum_import_model_year';

    const GET_MAX_PIE = 'max_pie';
    const GET_MEDIAN_PIE = 'median_pie';
    const GET_MIN_PIE = 'min_pie';
    const GET_SUMMED_DATA = 'summed_data';
    const GET_SUMMED_CITY_DATA = 'summed_city_data';

    const MAX = 'MAX';
    const MIN  = 'MIN';
    const SUM = 'SUM';
    const MEDIAN = 'MEDIAN';

    const AGGREGATION_TYPES = [
        'SUM',
        'MAX',
        'MIN',
        'MEDIAN'
    ];

    private $projectGrant = FinancialField::GRANT;

    /**
     * ProjectGrantQueryFactory constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @param array $filters
     * @param string $type
     * @param string $aggregationType
     * @return string
     */
    public function create(array $filters, string $type = 'summed_data', string $aggregationType = 'SUM')
    {

        if (!isset($filters['isFinancial']) || $filters['isFinancial'] === false
            && !in_array($aggregationType, self::AGGREGATION_TYPES)
        ) {
            return '0';
        }

        $sourceQuery = $this->getSourceQuery($filters, $type);

        switch ($type) {
            case self::GET_MAX_PIE:
                return "SELECT MAX(summed_by_city.subquery_sum) from ({$sourceQuery}) summed_by_city";
                break;
            case self::GET_MIN_PIE:
                return "SELECT MIN(summed_by_city.subquery_sum) from ({$sourceQuery}) summed_by_city";
                break;
            case self::GET_MEDIAN_PIE:
                return "SELECT MEDIAN(summed_by_city.subquery_sum) from ({$sourceQuery}) summed_by_city";
                break;
            case self::GET_SUMMED_DATA:
            case self::GET_SUMMED_CITY_DATA:
                return $this->getSumQuery($filters, $type);
                break;
            default:
                return $this;
        }

        // to_char(120000, '999 999 999D99 L')
    }

    /**
     * @param array $filters
     * @param string $type
     * @return string
     */
    private function getSourceQuery(array $filters, string $type)
    {
        $sumQuery = $this->getSumQuery($filters, $type);

        $sourceQuery = "select ({$sumQuery}) as subquery_sum FROM data_view";
        $sourceQuery .= " join enumeration on enumeration.value = data_view.column_name ";
        $sourceQuery .= " left join place on place.code = data_view.city_insee WHERE";

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
            ->addUndefined($filters, $sourceQuery)
            ->addSirenCarrierWithHistory($filters, $sourceQuery, 'data_view');

        return $sourceQuery . ' GROUP BY data_view.city_name';
    }

    /**
     * @param array $filters
     * @param string $type
     * @return string
     */
    private function getSumQuery(array $filters, string $type)
    {
        $sumQuery = "select SUM(cast(d.value as numeric)) as d_max";
        $sumQuery .= " from data_view d";
        $sumQuery .= " join enumeration en on en.value = d.column_name";
        $sumQuery .= " where en.name = '{$this->projectGrant}'";
        $sumQuery .= " and d.city_name = data_view.city_name";

        $this->sqlQueryBuilderHelper
            ->addDepartmentFilter($filters, $sumQuery, 'd')
            ->addDistrict($filters, $sumQuery, 'd')
            ->addSirenCarrierWithHistory($filters, $sumQuery, 'd');

        if ($type === self::GET_SUMMED_DATA) {
            $sumQuery .= " and d.year = data_view.year";
            $sumQuery .= " and d.import_model_id = data_view.import_model_id";
        } else {
            $this->sqlQueryBuilderHelper
                ->addYearStart($filters, $sumQuery, 'd')
                ->addYearEnd($filters, $sumQuery, 'd')
                ->addUndefined($filters, $sumQuery, 'd')
            ;

            if (empty($filters['importModels'])) {
                return '0';
            }
            $joinedIds = join(', ', $filters['importModels']);
            $sumQuery .= " and d.import_model_id in ({$joinedIds})";
        }

        return $sumQuery;
    }
}
