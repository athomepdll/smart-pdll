<?php


namespace AthomeSolution\ImportBundle\Services\ConfigSpecification;


use Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use Symfony\Component\PropertyAccess\PropertyAccessor;

/**
 * Class DataProcessorSpecification
 * @package AthomeSolution\ImportBundle\Services\ConfigSpecification
 */
class DataProcessorSpecification extends AbstractConfigSpecification
{
    /**
     * @var ExpressionLanguage
     */
    private $expressionLanguage;

    /**
     * DataProcessorSpecification constructor.
     */
    public function __construct()
    {
        $this->expressionLanguage = new ExpressionLanguage();
    }

    /**
     * @param array $data
     * @param array $objects
     */
    public function support(array $data, array &$objects)
    {
        parent::support($data, $objects);
    }

    /**
     * @param array $data
     * @param array $objects
     * @return mixed
     */
    public function execute(array $data, array &$objects)
    {
        $column = $data['column'];
        $value = $data['value'];
        $object = $this->useClass($column['class'], $objects);
        $this->useMethod($column['method'], $value, $object);
        $this->useAttribute($column['attribute'], $value, $object);
        $this->successor->support($data, $objects);

        return true;
    }

    /**
     * @param string $class
     * @param array $objects
     * @return mixed
     */
    protected function useClass(string $class, array &$objects)
    {
        if (!isset($objects[$class])) {
            $objects[$class] = new $class();
        }

        return $objects[$class];
    }

    /**
     * @param string $method
     * @param $value
     * @param $object
     * @return bool
     */
    protected function useMethod($method, $value, &$object)
    {
        if ($method == "" || $method == "~" || $method === null) {
            return false;
        }

        try {
            if (!method_exists($object, $method)) {
                return false;
            }
            $object->$method($value);
        } catch (\Exception $exception) {
            return false;
        }

        return true;
    }

    /**
     * @param string $attribute
     * @param $value
     * @param $object
     * @return bool
     */
    protected function useAttribute($attribute, $value, &$object)
    {
        $propertyAccessor = new PropertyAccessor();
        if ($attribute == "" || $attribute == "~" || $attribute === null) {
            return false;
        }
        try {
            $propertyAccessor->setValue($object, $attribute, $value);
        } catch (\Exception $exception) {
            return false;
        }

        return true;
    }
}