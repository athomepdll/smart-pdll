<?php


namespace App\ImportReader;

use PhpOffice\PhpSpreadsheet\Reader\IReadFilter;

/**
 * Class ChunkReadFilter
 * @package App\ImportReader
 */
class ChunkReadFilter implements IReadFilter
{

    private $startRow = 0;
    private $endRow   = 0;
    private $columns = [];

    /**
     * ChunkReadFilter constructor.
     * @param array $columns
     */
    public function __construct(array $columns)
    {
        $this->columns = $columns;
    }

    /**  Set the list of rows that we want to read
     * @param $startRow
     * @param $chunkSize
     */
    public function setRows(int $startRow, int $chunkSize): void
    {
        $this->startRow = $startRow;
        $this->endRow   = $startRow + $chunkSize;
    }

    /**
     * @param string $column
     * @param int $row
     * @param string $worksheetName
     * @return bool
     */
    public function readCell($column, $row, $worksheetName = '')
    {
        //  Only read the heading row, and the configured rows
        if (($row == 1) || ($row >= $this->startRow && $row < $this->endRow)) {
            if (in_array($column, $this->columns)) {
                return true;
            }
        }
        return false;
    }
}
