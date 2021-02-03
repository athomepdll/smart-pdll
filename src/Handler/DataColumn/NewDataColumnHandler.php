<?php


namespace App\Handler\DataColumn;

use App\Entity\Columns\DataColumn;
use App\Entity\Data;
use App\Entity\DataLine;
use App\Factory\DataColumnFactory;
use App\Handler\AbstractHandler;
use App\Manager\DataColumnManager;
use AthomeSolution\ImportBundle\Entity\ColumnInterface;
use Symfony\Component\Form\FormInterface;

/**
 * Class DataColumnHandler
 * @package App\Handler
 */
class NewDataColumnHandler extends AbstractHandler
{
    /**
     * @var DataColumnManager
     */
    private $dataColumnManager;

    /**
     * DataColumnHandler constructor.
     * @param DataColumnManager $dataColumnManager
     */
    public function __construct(
        DataColumnManager $dataColumnManager
    ) {
        $this->dataColumnManager = $dataColumnManager;
    }

    /**
     * @param FormInterface $form
     * @return ColumnInterface|bool
     */
    public function handle(FormInterface $form)
    {
        if (!$this->validate($form)) {
            return false;
        }

        $dataColumn = $form->getData();
        $this->setAttribute($dataColumn);

        return $this->dataColumnManager->saveEntity($dataColumn);
    }

    /**
     * @param DataColumn $dataColumn
     */
    private function setAttribute(DataColumn &$dataColumn)
    {
        $dataColumn->setAttribute('value');
        $dataColumn->setMethod('setValue');
        $dataColumn->setClass(Data::class);
    }

    /**
     * @param FormInterface $form
     * @return bool
     */
    public function validate(FormInterface $form): bool
    {
        if (!$form->getData() instanceof DataColumn) {
            return false;
        }
        return true;
    }
}
