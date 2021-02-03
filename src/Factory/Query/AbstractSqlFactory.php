<?php


namespace App\Factory\Query;

use App\Helper\SqlQueryBuilderHelper;

/**
 * Class AbstractSqlFactory
 * @package App\Factory\Query
 */
abstract class AbstractSqlFactory
{
    /** @var SqlQueryBuilderHelper */
    protected $sqlQueryBuilderHelper;

    public function __construct()
    {
        $this->sqlQueryBuilderHelper = new SqlQueryBuilderHelper();
    }
}
