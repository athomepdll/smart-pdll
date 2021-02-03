<?php


namespace App\Factory\Query;

/**
 * Class CityChangeQuery
 * @package App\Factory\Query
 */
class CityChangeQueryFactory extends AbstractSqlFactory
{
    /**
     * @param array $filters
     * @return string
     */
    public function create(array $filters)
    {
        $query = 'SELECT COUNT(*) FROM city where city.actual_city_id = city_id';

        $this->sqlQueryBuilderHelper
            ->addYearStart($filters, $query, 'data_view')
            ->addYearEnd($filters, $query, 'data_view');

        return $query;
    }
}
