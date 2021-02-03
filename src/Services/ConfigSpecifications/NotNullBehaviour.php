<?php


namespace App\Services\ConfigSpecifications;


/**
 * Trait NotNullBehaviour
 * @package App\Services\ConfigSpecifications
 */
trait NotNullBehaviour
{
    /**
     * @param array $data
     * @param array $objects
     */
    public function isValid(array $data, array &$objects)
    {
        if ($data['value'] === '') {
            $objects['errors'] []= 'La valeur donnée en ligne : ' . $data['rowIndex'] . ' est vide.';
        }
    }
}