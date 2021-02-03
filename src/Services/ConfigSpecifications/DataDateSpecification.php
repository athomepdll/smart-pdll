<?php


namespace App\Services\ConfigSpecifications;

use App\Entity\Columns\DataColumn;
use App\Entity\Columns\DataDateColumn;
use App\Exceptions\ImportData\ValueTypeException;
use AthomeSolution\ImportBundle\Services\ConfigSpecification\AbstractConfigSpecification;

/**
 * Class DataYesNoSpecification
 * @package App\Services\ConfigSpecifications
 */
class DataDateSpecification extends AbstractConfigSpecification
{
    use NotNullBehaviour;

    /**
     * @param array $data
     * @param array $objects
     * @return mixed
     * @throws ValueTypeException | \Exception
     */
    public function support(array $data, array &$objects)
    {
        if (!is_array($data) || !$data['column'] instanceof DataDateColumn) {
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
     * @throws ValueTypeException
     */
    public function execute(array $data, array &$objects)
    {
        /** @var DataColumn $column */
        $column = $data['column'];

        $dataObject = $this->useClass($column->getClass(), $objects);

        $this->isValid($data, $objects);

        try {
            $data = new \DateTime(str_replace('/', '-', $data['value']));
        } catch (\Exception $exception) {
            throw new ValueTypeException('La valeur donnée en ligne : ' . $data['rowIndex'] . ' , ne correspond pas au paramétrage de type date');
        }
        $this->useMethod($column->getMethod(), $data->format('d/m/Y'), $dataObject);
    }
}
