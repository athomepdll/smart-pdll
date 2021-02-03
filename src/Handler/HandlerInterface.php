<?php


namespace App\Handler;

use Symfony\Component\Form\FormInterface;

/**
 * Interface HandlerInterface
 * @package App\Handler
 */
interface HandlerInterface
{
    /**
     * @param FormInterface $form
     * @return bool
     */
    function handle(FormInterface $form);

    /**
     * @param FormInterface $form
     * @return bool
     */
    function validate(FormInterface $form): bool;
}
