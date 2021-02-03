<?php


namespace App\Handler\Files;

use App\Handler\AbstractHandler;
use App\Services\DownloadFileService;
use Symfony\Component\Form\FormInterface;

/**
 * Class FilesDownloadHandler
 * @package App\Handler\Files
 */
class FilesDownloadHandler extends AbstractHandler
{
    /**
     * @var DownloadFileService
     */
    private $downloadFileService;

    /**
     * FilesDownloadHandler constructor.
     * @param DownloadFileService $downloadFileService
     */
    public function __construct(
        DownloadFileService $downloadFileService
    ) {
        $this->downloadFileService = $downloadFileService;
    }

    /**
     * @param FormInterface $form
     * @return mixed
     */
    public function handle(FormInterface $form)
    {
        if (!$this->validate($form)) {
            return false;
        }

        $data = $form->getData();

        $fullPath = $this->downloadFileService->getFileToDownload($data['filename']);

        return $fullPath;
    }

    /**
     * @param FormInterface $form
     * @return bool
     */
    public function validate(FormInterface $form): bool
    {
        if (!$form->has('filename') || $form->get('filename') === null) {
            return false;
        }

        return true;
    }
}
