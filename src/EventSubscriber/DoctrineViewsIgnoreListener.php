<?php


namespace App\EventSubscriber;

use Doctrine\Common\EventSubscriber;
use Doctrine\DBAL\Event\SchemaCreateTableEventArgs;
use Doctrine\DBAL\Events;
use Doctrine\ORM\Tools\Event\GenerateSchemaEventArgs;

/**
 * Class DoctrineViewsIgnoreListener
 * @package App\EventSubscriber
 */
class DoctrineViewsIgnoreListener implements EventSubscriber
{
    private $ignoredTables = [
        'data_view',
        'carrying_structure_view'
    ];

    /**
     * Returns an array of events this subscriber wants to listen to.
     *
     * @return string[]
     */
    public function getSubscribedEvents()
    {
        return [
            'postGenerateSchema' => 'onPostGenerateSchema',
            'postDropSchema' => 'onPostGenerateSchema',
        ];
    }

    /**
     * @param GenerateSchemaEventArgs $args
     */
    public function postGenerateSchema(GenerateSchemaEventArgs $args)
    {
        $schemaName = $args->getSchema()->getName();
        $filteredIgnoredTables = array_map(function ($item) use ($schemaName) {
            return $schemaName.'.'.$item;
        }, $this->ignoredTables);

        $schema = $args->getSchema();
        $tableNames = $schema->getTableNames();
        foreach ($tableNames as $tableName) {
            if (in_array($tableName, $filteredIgnoredTables)) {
                // remove table from schema
                $schema->dropTable($tableName);
            }
        }
    }
}
