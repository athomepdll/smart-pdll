<?php


namespace AthomeSolution\ImportBundle\Services\ConfigSpecification;


/**
 * Class AbstractConfigSpecification
 * @package AthomeSolution\ImportBundle\Services\ConfigSpecification
 */
abstract class AbstractConfigSpecification implements ConfigSpecificationInterface
{
    /**
     * @var AbstractConfigSpecification
     */
    protected $successor;

    /**
     * @param AbstractConfigSpecification $specification
     */
    public function setSuccessor(AbstractConfigSpecification $specification)
    {
        $this->successor = $specification;
    }

    /**
     * @param array $data
     * @param array $objects
     * @throws \Exception
     */
    public function support(array $data, array &$objects)
    {
        if (!is_array($data)) {
            $this->successor->support($data, $objects);
        }

        $this->execute($data, $objects);
    }

    /**
     * @param array $data
     * @param array $objects
     * @return mixed|void
     */
    public function execute(array $data, array &$objects)
    {
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