<?php


namespace App\Handler\ImportModel;

use App\Entity\ImportLog;
use App\Entity\ImportModel;
use App\Handler\AbstractHandler;
use App\Manager\ImportLogManager;
use App\Manager\ImportModelManager;
use App\Manager\NotificationManager;
use App\Repository\ImportLogRepository;
use App\Repository\NotificationRepository;
use AthomeSolution\ImportBundle\Entity\Config;
use AthomeSolution\ImportBundle\Manager\ConfigManager;
use Symfony\Component\Form\FormInterface;

/**
 * Class DeleteImportModelHandler
 * @package App\Handler
 */
class DeleteImportModelHandler extends AbstractHandler
{
    /**
     * @var ConfigManager
     */
    private $configManager;
    /**
     * @var ImportModelManager
     */
    private $importModelManager;
    /**
     * @var ImportLogRepository
     */
    private $importLogRepository;
    /**
     * @var ImportLogManager
     */
    private $importLogManager;
    /**
     * @var NotificationRepository
     */
    private $notificationRepository;
    /**
     * @var NotificationManager
     */
    private $notificationManager;

    /**
     * DeleteImportModelHandler constructor.
     * @param ConfigManager $configManager
     * @param ImportLogRepository $importLogRepository
     * @param ImportLogManager $importLogManager
     * @param ImportModelManager $importModelManager
     * @param NotificationRepository $notificationRepository
     * @param NotificationManager $notificationManager
     */
    public function __construct(
        ConfigManager $configManager,
        ImportLogRepository $importLogRepository,
        ImportLogManager $importLogManager,
        ImportModelManager $importModelManager,
        NotificationRepository $notificationRepository,
        NotificationManager $notificationManager
    ) {
        $this->configManager = $configManager;
        $this->importLogRepository = $importLogRepository;
        $this->importLogManager = $importLogManager;
        $this->importModelManager = $importModelManager;
        $this->notificationRepository = $notificationRepository;
        $this->notificationManager = $notificationManager;
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
        $disabledImportLogs =  $this->importLogRepository->getByImportModel($importModel, true);
        /** @var ImportLog $disabledImportLog */
        foreach ($disabledImportLogs as $disabledImportLog) {
            $notifications = $this->notificationRepository->findBy(['importLog' => $disabledImportLog]);
            foreach ($notifications as $notification) {
                $this->notificationManager->removeEntity($notification);
            }
            $this->importLogManager->removeEntity($disabledImportLog);
        }
        $this->importModelManager->removeEntity($importModel);

        return true;
    }

    /**
     * @param FormInterface $form
     * @return bool
     */
    public function validate(FormInterface $form): bool
    {
        /** @var ImportModel $importModel */
        $importModel = $form->getData();
        if (!$importModel instanceof ImportModel) {
            return false;
        }

        if (!empty($this->importLogRepository->getByImportModel($importModel))) {
            return false;
        }

        return true;
    }
}
