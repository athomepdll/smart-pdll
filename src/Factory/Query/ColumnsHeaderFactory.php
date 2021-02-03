<?php


namespace App\Factory\Query;

/**
 * Class ColumnsHeaderFactory
 * @package App\Factory\Query
 */
class ColumnsHeaderFactory extends AbstractSqlFactory
{
    /**
     * @param array $filters
     * @return string
     */
    public function create(array $filters)
    {
        return $this->createColumnsHeaderQuery($filters);
    }

    /**
     * @param array $filters
     * @return string
     */
    private function createColumnsHeaderQuery(array $filters)
    {
        $categoryQuery = "SELECT column_name FROM data_view WHERE";

        $this->sqlQueryBuilderHelper
            ->addDataLevelFilter($filters, $categoryQuery)
            ->addImportModelId($filters, $categoryQuery);

        return $categoryQuery . " GROUP BY column_name ORDER BY SUM(data_line_id + data_view_id)";
    }
}
