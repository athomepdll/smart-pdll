<?php


namespace App\Services;

use App\Entity\ImportLog;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * Class DownloadService
 * @package App\Services
 */
class DownloadService
{
    /**
     * @var KernelInterface
     */
    protected $kernel;

    /**
     * DownloadService constructor.
     * @param KernelInterface $kernel
     */
    public function __construct(
        KernelInterface $kernel
    ) {
        $this->kernel = $kernel;
    }

    /**
     * @param Request        $request
     * @param                $parameter
     * @param null           $em
     * @param ImportLog|null $importLog
     * @return BinaryFileResponse
     */
    public function download(Request $request, $parameter, $em = null, ImportLog $importLog = null)
    {
        if ($importLog === null && $em !== null) {
            $idImportLog = $request->query->get('id');
            $importLog = $em->getRepository(ImportLog::class)->find($idImportLog);
        }
        $realFilePath = $this->kernel->getProjectDir() . $parameter . $importLog->getFilePath();
        $response = new BinaryFileResponse($realFilePath);
        $response->headers->set('Content-Type', 'text/csv');
        $response->setContentDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            $importLog->getFilePath()
        );

        return $response;
    }
}
