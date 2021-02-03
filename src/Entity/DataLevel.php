<?php


namespace App\Entity;

/**
 * Class DataLevel
 * @package App\Entity
 */
class DataLevel extends Enumeration
{
    const DETAIL = 'detail';
    const IGNORE = 'ignore';
    const STOCK = 'stock';
    const SUMMARY = 'summary';
}
