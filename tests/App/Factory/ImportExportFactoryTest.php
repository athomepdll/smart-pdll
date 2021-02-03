<?php

namespace App\Tests\App\Factory;

use App\Entity\ImportExport;
use App\Factory\ImportExportFactory;
use PHPUnit\Framework\TestCase;

/**
 * Class ImportExportFactoryTest
 * @package App\Tests\App\Factory
 */
class ImportExportFactoryTest extends TestCase
{
    /**
     * @dataProvider parametersForCreate
     * @param string $type
     * @param array $parameters
     */
    public function testCreate(array $parameters)
    {
        $importExportFactory = new ImportExportFactory();
        $importExport = $importExportFactory->create($parameters);
        $this->assertSame($parameters['type'], $importExport->getType());
        $this->assertSame($parameters['filePath'], $importExport->getFilePath());
        $this->assertSame($parameters['fileName'], $importExport->getFileName());
        $this->assertSame(ImportExport::STATE_NEW, $importExport->getState());
    }

    /**
     * @return array
     */
    public function parametersForCreate()
    {
        return [
            [
                [
                    'type' => ImportExportFactory::INSEE_TYPE,
                    'filePath' => 'test/',
                    'fileName' => 'testName'
                ],
            ],
            [
                [
                    'type' => ImportExportFactory::DATA_TYPE,
                    'filePath' => 'test/',
                    'fileName' => 'testName'
                ],
            ],
        ];
    }
}
