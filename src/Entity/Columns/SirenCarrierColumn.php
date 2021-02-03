<?php


namespace App\Entity\Columns;

use AthomeSolution\ImportBundle\Entity\Column;

/**
 * Class InseeCarrierColumn
 * @package App\Entity\Columns
 */
class SirenCarrierColumn extends Column
{
    const SIREN_CARRIER_COLUMN = 'SIREN Structure Porteuse';
    const SIREN_CARRIER_IDENTIFIER = 'siren_carrier';

    public function __construct()
    {
        $this->columnName = self::SIREN_CARRIER_COLUMN;
    }
}
