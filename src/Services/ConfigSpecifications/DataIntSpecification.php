<?php


namespace App\Services\ConfigSpecifications;

use App\Entity\Columns\DataColumn;
use App\Entity\Columns\DataIntColumn;
use App\Exceptions\ImportData\ValueTypeException;
use AthomeSolution\ImportBundle\Services\ConfigSpecification\AbstractConfigSpecification;

/**
 * Class DataFloatSpecification
 * @package App\Services\ConfigSpecifications
 */
class DataIntSpecification extends AbstractConfigSpecification
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
        if (!is_array($data) || !$data['column'] instanceof DataIntColumn) {
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
            $value = $data['value'] === '' ? null : $data['value'];
            if ($data['value'] !== null && $data['value'] !== '') {
                $value = str_replace([' ', ' ', ',', '€'], ['', '', '.', ''], $data['value']);
                $value = intval($value);
            }
        } catch (\Exception $exception) {
            $objects['errors'] []= 'La valeur donnée en ligne : ' . $data['rowIndex'] . ' , ne correspond pas au paramétrage de type numérique';
//            throw new ValueTypeException('La valeur donnée en ligne : ' . $data['rowIndex'] . ' , ne correspond pas au paramétrage de type entier');
        }


        $this->useMethod($column->getMethod(), $value, $dataObject);
    }

    /**
     * @param string $data
     * @return bool
     */
    private function isInteger(string $data)
    {
        $re = '/[a-zA-Z]/m';
        preg_match_all($re, $data, $matches, PREG_SET_ORDER, 0);

        return empty($matches);
    }
}
