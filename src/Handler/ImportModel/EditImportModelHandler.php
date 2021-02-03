<?php


namespace App\Handler\ImportModel;

use App\Entity\Entity;
use App\Entity\ImportModel;
use App\Handler\AbstractHandler;
use App\Manager\ImportModelManager;
use App\Repository\ImportLogRepository;
use AthomeSolution\ImportBundle\Manager\ConfigManager;
use Symfony\Component\Form\FormInterface;

/**
 * Class ImportModelHandler
 * @package App\Handler
 */
class EditImportModelHandler extends AbstractHandler
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
     * @var ImportLogRepository
     */
    private $importLogRepository;

    /**
     * ImportModelHandler constructor.
     * @param ConfigManager $configManager
     * @param ImportLogRepository $importLogRepository
     * @param ImportModelManager $importModelManager
     */
    public function __construct(
        ConfigManager $configManager,
        ImportLogRepository $importLogRepository,
        ImportModelManager $importModelManager
    ) {
        $this->importModelManager = $importModelManager;
        $this->importLogRepository = $importLogRepository;
        $this->configManager = $configManager;
    }


    /**
     * @param FormInterface $form
     * @return Entity|bool
     */
    public function handle(FormInterface $form)
    {
        if (!$this->validate($form)) {
            return false;
        }

        $importModel = $form->getData();

        return $this->importModelManager->saveEntity($importModel);
    }

    /**
     * @param FormInterface $form
     * @return bool
     */
    public function validate(FormInterface $form): bool
    {
        $importModel = $form->getData();
        if (!$importModel instanceof ImportModel) {
            return false;
        }

//        if (!empty($this->importLogRepository->getByImportModel($importModel))) {
//            return false;
//        }
        return true;
    }

    /**
     * @param FormInterface $form
     * @return bool
     */
    public function isEditable(FormInterface $form)
    {
        $importModel = $form->getData();

        if (!empty($this->importLogRepository->getByImportModel($importModel))) {
            return false;
        }

        return true;
    }
}
