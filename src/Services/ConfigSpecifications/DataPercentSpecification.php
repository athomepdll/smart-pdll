<?php


namespace App\Services\ConfigSpecifications;

use App\Entity\Columns\DataColumn;
use App\Entity\Columns\DataPercentColumn;
use App\Entity\DataType;
use App\Exceptions\ImportData\ValueTypeException;
use AthomeSolution\ImportBundle\Services\ConfigSpecification\AbstractConfigSpecification;

/**
 * Class DataYesNoSpecification
 * @package App\Services\ConfigSpecifications
 */
class DataPercentSpecification extends AbstractConfigSpecification
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
        if (!is_array($data) || !$data['column'] instanceof DataPercentColumn
            || $data['column']->getDataType()->getName() !== DataType::PERCENT) {
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
            $value = str_replace([' ', ','], ['', '.'], $data['value']);
            $value = round(floatval($value), 2);
        } catch (\Exception $exception) {
            throw new ValueTypeException('La valeur donnée en ligne : ' . $data['rowIndex'] . ' , ne correspond pas au paramétrage de type pourcentage');
        }
        $this->useMethod($column->getMethod(), $value, $dataObject);
    }
}
