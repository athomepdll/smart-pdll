<?php


namespace App\Handler;

use Symfony\Component\Form\FormInterface;

/**
 * Class AbstractHandler
 * @package App\Handler
 */
abstract class AbstractHandler implements HandlerInterface
{
    /**
     * @param FormInterface $form
     * @return mixed
     */
    public function handle(FormInterface $form)
    {
        if (!$this->validate($form)) {
            return false;
        }

        return true;
    }

    /**
     * @param FormInterface $form
     * @return bool
     */
    public function validate(FormInterface $form): bool
    {
        if (!$form instanceof FormInterface) {
            return false;
        }

        return true;
    }
}
