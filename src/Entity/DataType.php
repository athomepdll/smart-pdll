<?php


namespace App\Entity;

/**
 * Class DataType
 * @package App\Entity
 */
class DataType extends Enumeration
{
    const YES_NO = 'yes_no';
    const NUMBER = 'number';
    const DATE = 'date';
    const PERCENT = 'percent';
    const INTEGER = 'integer';
    const TEXT = 'text';
}
