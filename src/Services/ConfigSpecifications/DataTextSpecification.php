<?php


namespace App\Services\ConfigSpecifications;

use App\Entity\Columns\DataTextColumn;
use AthomeSolution\ImportBundle\Services\ConfigSpecification\AbstractConfigSpecification;

/**
 * Class DataTextSpecification
 * @package App\Services\ConfigSpecifications
 */
class DataTextSpecification extends AbstractConfigSpecification
{
    use NotNullBehaviour;

    /**
     * @param array $data
     * @param array $objects
     * @return mixed
     * @throws \Exception
     */
    public function support(array $data, array &$objects)
    {
        if (!is_array($data) || !$data['column'] instanceof DataTextColumn) {
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
        $this->isValid($data, $objects);
        $this->useMethod($data['column']->getMethod(), $data['value'], $objects[$data['column']->getClass()]);
    }
}
