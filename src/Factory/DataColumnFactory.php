<?php


namespace App\Factory;

use App\Entity\Columns\DataBoolColumn;
use App\Entity\Columns\DataColumn;
use App\Entity\Columns\DataDateColumn;
use App\Entity\Columns\DataFloatColumn;
use App\Entity\Columns\DataIntColumn;
use App\Entity\Columns\DataPercentColumn;
use App\Entity\Columns\DataTextColumn;
use App\Entity\DataType;

/**
 * Class DataColumnFactory
 * @package App\Factory
 */
class DataColumnFactory
{
    /**
     * @param string|null $dataType
     * @return DataBoolColumn|DataColumn|DataDateColumn|DataFloatColumn|DataIntColumn|DataPercentColumn|DataTextColumn
     */
    public function create($dataType)
    {
        switch ($dataType) {
            case DataType::NUMBER:
                $column = new DataFloatColumn();
                break;
            case DataType::YES_NO:
                $column = new DataBoolColumn();
                break;
            case DataType::INTEGER:
                $column = new DataIntColumn();
                break;
            case DataType::PERCENT:
                $column = new DataPercentColumn();
                break;
            case DataType::DATE:
                $column = new DataDateColumn();
                break;
            case DataType::TEXT:
                $column = new DataTextColumn();
                break;
            default:
                $column = new DataColumn();
                break;
        }

        return $column;
    }
}
