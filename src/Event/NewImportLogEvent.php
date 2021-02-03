<?php


namespace App\Event;

use App\Entity\ImportLog;
use Symfony\Component\EventDispatcher\Event;

/**
 * Class NewImportDataEvent
 * @package App\Event
 */
class NewImportLogEvent extends Event
{
    /** @var ImportLog */
    public $importLog;

    /**
     * NewImportLogEvent constructor.
     * @param ImportLog $importLog
     */
    public function __construct(ImportLog $importLog)
    {
        $this->importLog = $importLog;
    }
}
