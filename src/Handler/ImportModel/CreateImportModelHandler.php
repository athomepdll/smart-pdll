<?php


namespace App\Handler\ImportModel;

use App\Entity\Entity;
use App\Entity\ImportModel;
use App\Handler\AbstractHandler;
use App\Manager\ImportModelManager;
use AthomeSolution\ImportBundle\Entity\Config;
use AthomeSolution\ImportBundle\Manager\ConfigManager;
use Symfony\Component\Form\FormInterface;

/**
 * Class ImportModelHandler
 * @package App\Handler
 */
class CreateImportModelHandler extends AbstractHandler
{
    /**
     * @var ImportModelManager
     */
    private $importModelManager;
    /**
     * @var ConfigManager
     */
    private $configManager;

    /**
     * ImportModelHandler constructor.
     * @param ConfigManager $configManager
     * @param ImportModelManager $importModelManager
     */
    public function __construct(
        ConfigManager $configManager,
        ImportModelManager $importModelManager
    ) {
        $this->importModelManager = $importModelManager;
        $this->configManager = $configManager;
    }


    /**
     * @param FormInterface $form
     * @return bool
     */
    public function handle(FormInterface $form)
    {
        if (!$this->validate($form)) {
            return false;
        }

        /** @var ImportModel $importModel */
        $importModel = $form->getData();
        /** @var Config $config */
        $config = $importModel->getConfig();
        $config->name = $importModel->getName();

        $this->configManager->saveEntity($config);
        $this->importModelManager->saveEntity($importModel);

        return true;
    }

    /**
     * @param FormInterface $form
     * @return bool
     */
    public function validate(FormInterface $form): bool
    {
        return $form->getData() instanceof ImportModel;
    }
}
