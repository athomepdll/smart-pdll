<?php


namespace App\Entity\Columns;

use AthomeSolution\ImportBundle\Entity\Column;

/**
 * Class SirenRootColumn
 * @package App\Entity\Columns
 */
class SirenRootColumn extends Column
{
    const SIREN_ROOT_COLUMN = 'SIREN Implantation';
    const SIREN_ROOT_IDENTIFIER = 'siren_root';

    public function __construct()
    {
        $this->columnName = self::SIREN_ROOT_COLUMN;
    }
}
