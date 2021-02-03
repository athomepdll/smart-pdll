<?php


namespace App\Controller;

use App\Repository\DepartmentRepository;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class DepartmentController
 * @package App\Controller
 *
 * @Rest\RouteResource("Department")
 */
class DepartmentController extends AbstractFOSRestController
{
    /**
     * @var DepartmentRepository
     */
    private $departmentRepository;

    /**
     * DepartmentController constructor.
     * @param DepartmentRepository $departmentRepository
     */
    public function __construct(
        DepartmentRepository $departmentRepository
    ) {
        $this->departmentRepository = $departmentRepository;
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function cgetAction(Request $request)
    {
        $filters = $request->request->all();

        $departments = $this->departmentRepository->getDepartments($filters);

        return new JsonResponse(['data' => $departments]);
    }
}
