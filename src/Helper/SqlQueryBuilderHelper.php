<?php


namespace App\Helper;

use App\Entity\CarryingStructure;
use App\Entity\City;
use App\Entity\Department;
use App\Entity\District;
use App\Entity\Epci;
use App\Entity\ImportModel;

/**
 * Class SqlQueryBuilderHelper
 * @package App\Helper
 */
class SqlQueryBuilderHelper
{
    /**
     * @param array $filters
     * @param string $sourceQuery
     * @return SqlQueryBuilderHelper
     */
    public function addDataLevelFilter(array $filters, string &$sourceQuery)
    {
        if (!isset($filters['dataLevel']) || $filters['dataLevel'] === null) {
            return $this;
        }

        if (is_array($filters['dataLevel']) && !empty($filters['dataLevel'])) {
            $dataLevels = array_map(function ($dataLevel) {
                return "'{$dataLevel}'";
            }, $filters['dataLevel']);

            $dataLevels = join(', ', $dataLevels);
            $sourceQuery .= " data_level in ({$dataLevels})";

            return $this;
        }

        $sourceQuery .= " data_level = '{$filters['dataLevel']}'";

        return $this;
    }

    /**
     * @param array $filters
     * @param string $sourceQuery
     * @param string $alias
     * @return SqlQueryBuilderHelper
     */
    public function addImportModelId(array $filters, string &$sourceQuery, string $alias = '')
    {
        if (!isset($filters['importModel']) || !$filters['importModel'] instanceof ImportModel) {
            return $this;
        }

        if ($alias !== '') {
            $alias .= '.';
        }

        $sourceQuery .= " AND {$alias}import_model_id = {$filters['importModel']->getId()}";

        return $this;
    }

    /**
     * @param array $filters
     * @param string $sourceQuery
     * @return SqlQueryBuilderHelper
     */
    public function addImportModelIds(array $filters, string &$sourceQuery)
    {
        if (!isset($filters['importModels']) || empty($filters['importModels'])) {
            return $this;
        }

        $joinedIds = join(', ', $filters['importModels']);

        $sourceQuery .= " AND import_model_id in ({$joinedIds})";

        return $this;
    }

    /**
     * @param array $filters
     * @param string $sourceQuery
     * @param string $alias
     * @return SqlQueryBuilderHelper
     */
    public function addYearStart(array $filters, string &$sourceQuery, $alias = '')
    {
        if (!isset($filters['yearStart'])) {
            return $this;
        }

        $operator = '=';
        $yearStart = $filters['yearStart'];

        if (isset($filters['yearEnd'])) {
            $operator = '>=';
        }

        if ($alias !== '') {
            $alias .= '.';
        }

        $sourceQuery .= " AND {$alias}year {$operator} {$yearStart}";

        return $this;
    }

    /**
     * @param array $filters
     * @param string $sourceQuery
     * @param string $alias
     * @return $this
     */
    public function addYearEnd(array $filters, string &$sourceQuery, $alias = '')
    {
        if (!isset($filters['yearEnd'])) {
            return $this;
        }

        $yearEnd = $filters['yearEnd'];

        if ($alias !== '') {
            $alias .= '.';
        }

        $sourceQuery .= " AND {$alias}year <= {$yearEnd}";

        return $this;
    }

    /**
     * @param array $filters
     * @param string $sourceQuery
     * @param string $alias
     * @return SqlQueryBuilderHelper
     */
    public function addDepartmentFilter(array $filters, string &$sourceQuery, string $alias = '')
    {
        if (!isset($filters['department']) || !$filters['department'] instanceof Department) {
            return $this;
        }

        if ($alias !== '') {
            $alias .= '.';
        }

        $sourceQuery .= " AND {$alias}department_id = {$filters['department']->getId()}";

        return $this;
    }

    /**
     * @param array $filters
     * @param string $sourceQuery
     * @param string $alias
     * @return SqlQueryBuilderHelper
     */
    public function addDistrict(array $filters, string &$sourceQuery, string $alias = '')
    {
        if (!isset($filters['district']) || !$filters['district'] instanceof District) {
            return $this;
        }

        if ($alias !== '') {
            $alias.= '.';
        }

        $sourceQuery .= " AND {$alias}district_id = {$filters['district']->getId()}";

        return $this;
    }

    /**
     * @param array $filters
     * @param string $sourceQuery
     * @param string $alias
     * @return SqlQueryBuilderHelper
     */
    public function addEpci(array $filters, string &$sourceQuery, string $alias = '')
    {
        if (!isset($filters['epci']) || !$filters['epci'] instanceof Epci) {
            return $this;
        }

        if ($alias !== '') {
            $alias .= '.';
        }

        $selectQuery = "SELECT city_id FROM city_epci";
        $selectQuery .= " JOIN epci e on e.id = epci_id ";
        $selectQuery .= " JOIN city city_link ON city_link.id = city_id";
        $selectQuery .= " WHERE e.siren = '{$filters['epci']->getSiren()}'";

        $this->addYearStart($filters, $selectQuery, 'e');
        $this->addYearEnd($filters, $selectQuery, 'e');

        $sourceQuery .= " AND ({$alias}city_id IN ({$selectQuery})";
        $sourceQuery .= " OR {$alias}actual_city_id IN ({$selectQuery}))";

        return $this;
    }

    /**
     * @param array $filters
     * @param string $sourceQuery
     * @param string $alias
     * @return SqlQueryBuilderHelper
     */
    public function addCity(array $filters, string &$sourceQuery, string $alias = '')
    {
        if (!isset($filters['city']) || !$filters['city'] instanceof City) {
            return $this;
        }

        if ($alias !== '') {
            $alias .= '.';
        }

        $sourceQuery .= " AND ({$alias}city_siren = '{$filters['city']->getSiren()}' or {$alias}actual_city_siren = '{$filters['city']->getSiren()}')";

        return $this;
    }

    /**
     * @param array $filters
     * @param string $sourceQuery
     * @param string $alias
     * @return SqlQueryBuilderHelper
     */
    public function addSirenCarrier(array $filters, string &$sourceQuery, string $alias = '')
    {
        if (!isset($filters['carryingStructure']) || !$filters['carryingStructure'] instanceof CarryingStructure) {
            return $this;
        }

        if ($alias !== '') {
            $alias .= '.';
        }

        $sourceQuery .= " AND {$alias}siren_carrier = '{$filters['carryingStructure']->getSiren()}'";

        return $this;
    }

    /**
     * @param array $filters
     * @param string $sourceQuery
     * @param string $alias
     * @return SqlQueryBuilderHelper
     */
    public function addSirenCarrierWithHistory(array $filters, string &$sourceQuery, string $alias = '')
    {
        if (!isset($filters['carryingStructure']) || !$filters['carryingStructure'] instanceof CarryingStructure) {
            return $this;
        }

        if ($alias !== '') {
            $alias .= '.';
        }

        $sourceQuery .= " AND ({$alias}siren_carrier = '{$filters['carryingStructure']->getSiren()}'";
        $sourceQuery .= " OR {$alias}actual_city_id IN ";
        $sourceQuery .= " (SELECT history_data.city_id FROM data_view history_data WHERE history_data.siren_carrier =";
        $sourceQuery .= " '{$filters['carryingStructure']->getSiren()}' group by history_data.city_id))";

        return $this;
    }

    /**
     * @param string $value
     * @param string $sourceQuery
     * @return SqlQueryBuilderHelper
     */
    public function addEnumeration(string $value, string &$sourceQuery)
    {
        if (!is_string($value)) {
            return $this;
        }

        $sourceQuery .= " AND enumeration.value = '{$value}'";

        return $this;
    }

    /**
     * @param array $filters
     * @param string $sourceQuery
     * @param string $alias
     * @param string $type
     * @return SqlQueryBuilderHelper
     */
    public function addUndefined(array $filters, string &$sourceQuery, $alias = '', string  $type = 'IS NOT NULL')
    {
        if ($alias !== '') {
            $alias .= '.';
        }

        $sourceQuery .= " AND {$alias}city_id {$type}";

        return $this;
    }
}
