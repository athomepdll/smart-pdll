<?php


namespace App\Services\ConfigSpecifications;

use App\Entity\Columns\DataColumn;
use App\Entity\Columns\SirenCarrierColumn;
use App\Entity\Columns\SirenRootColumn;
use App\Entity\Data;
use App\Entity\DataLevel;
use App\Entity\DataType;
use App\Repository\DataLevelRepository;
use App\Repository\DataTypeRepository;
use AthomeSolution\ImportBundle\Services\ConfigSpecification\AbstractConfigSpecification;

/**
 * Class DataColumnSpecification
 * @package App\Services\ConfigSpecifications
 */
class DataColumnSpecification extends AbstractConfigSpecification
{
    /**
     * @var DataLevelRepository
     */
    private $dataLevelRepository;
    /**
     * @var DataTypeRepository
     */
    private $dataTypeRepository;

    /**
     * DataColumnSpecification constructor.
     * @param DataLevelRepository $dataLevelRepository
     * @param DataTypeRepository $dataTypeRepository
     */
    public function __construct(
        DataLevelRepository $dataLevelRepository,
        DataTypeRepository $dataTypeRepository
    ) {
        $this->dataLevelRepository = $dataLevelRepository;
        $this->dataTypeRepository = $dataTypeRepository;
    }

    /**
     * @param array $data
     * @param array $objects
     * @return mixed
     * @throws \Exception
     */
    public function support(array $data, array &$objects)
    {
        if (!is_array($data) || $data['column'] instanceof SirenRootColumn || $data['column'] instanceof SirenCarrierColumn) {
            $this->successor->support($data, $objects);
            return false;
        }

        $this->execute($data, $objects);

        $this->successor->support($data, $objects);

        return true;
    }

    /**
     * @param array $data
     * @param array $objects
     * @return mixed|void
     */
    public function execute(array $data, array &$objects)
    {
        /** @var DataColumn $column */
        $column = $data['column'];

        /** @var DataType $dataType */
        $dataType = !$data['isMapped'] ? $this->dataTypeRepository->findOneBy(['name' => DataType::TEXT])
            : $column->getDataType();
        /** @var DataLevel $dataLevel */
        $dataLevel =  !$data['isMapped'] ? $this->dataLevelRepository->findOneBy(['name' => DataLevel::STOCK])
            : $column->getDataLevel();
        /** @var string $identifier */
        $identifier =  !$data['isMapped'] ? '' : $column->getIdentifier() !== null ? $column->getIdentifier() : $column->getColumnName();

        $data = new Data();
        $data->setDataType($dataType);
        $data->setDataLevel($dataLevel);
        $data->setDataLine($objects['dataLine']);
        $data->setName($identifier);

        $objects[Data::class] = $data;
    }
}
