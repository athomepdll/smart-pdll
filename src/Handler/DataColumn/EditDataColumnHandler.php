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
class EditDataColumnHandler extends AbstractHandler
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
        $dataColumn = $this->setDataType($dataColumn);
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
     * @param DataColumn $dataColumn
     * @return \App\Entity\Columns\DataBoolColumn|DataColumn|\App\Entity\Columns\DataDateColumn|\App\Entity\Columns\DataFloatColumn|\App\Entity\Columns\DataIntColumn|\App\Entity\Columns\DataPercentColumn|\App\Entity\Columns\DataTextColumn
     */
    private function setDataType(DataColumn $dataColumn)
    {
        $dataColumnFactory =  new DataColumnFactory();
        $type = $dataColumn->getDataType() ? $dataColumn->getDataType()->getName() : null;
        $column = $dataColumnFactory->create($type);

        if (get_class($dataColumn) !== get_class($column)) {
            $column->setAttribute($dataColumn->getAttribute());
            $column->setClass($dataColumn->getClass());
            $column->setConfig($dataColumn->getConfig());
            $column->setColumnName($dataColumn->getColumnName());
            $column->setDataLevel($dataColumn->getDataLevel());
            $column->setDataType($dataColumn->getDataType());
            $column->setIdentifier($dataColumn->getIdentifier());
            $column->setMethod($dataColumn->getMethod());
            $column->setId($dataColumn->getId());

            $this->dataColumnManager->removeEntity($dataColumn);

            return $column;
        }

        return $dataColumn;
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
