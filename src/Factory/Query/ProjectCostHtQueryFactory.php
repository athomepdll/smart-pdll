<?php


namespace App\Factory\Query;

use App\Entity\FinancialField;

/**
 * Class ProjectCostHtQuery
 * @package App\Factory\Query
 */
class ProjectCostHtQueryFactory extends AbstractSqlFactory
{
    /**
     * @param array $filters
     * @return string
     */
    public function create(array $filters)
    {
        if (!isset($filters['isFinancial']) || $filters['isFinancial'] === false) {
            return '0';
        }

        $projectCostHt = FinancialField::PROJECT_COST_HT;

        $query = "(select SUM(cast(d.value as numeric)) from data_view d join enumeration en on en.value = d.column_name";
        $query .= " where en.name = '{$projectCostHt}'";
        $query .= "and CONCAT(d.city_name, ', ', d.import_model_name, ' ', d.year)";
        $query .= " = CONCAT(data_view.city_name, ', ', data_view.import_model_name, ' ', data_view.year) ";

        $this->sqlQueryBuilderHelper
            ->addSirenCarrier($filters, $query, 'd')
            ->addDepartmentFilter($filters, $query, 'd')
            ->addDistrict($filters, $query, 'd');

//        return $query .= " and d.department_id = data_view.department_id and d.district_id = data_view.district_id)";
        return $query .= ")";
    }
}
