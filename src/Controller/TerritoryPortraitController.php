<?php


namespace App\Controller;

use App\Form\TerritoryType;
use App\Handler\Data\DataMapHandler;
use App\Handler\TerritoryPortrait\TerritoryPortraitHandler;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class TerritoryPortraitController
 * @package App\Controller
 */
class TerritoryPortraitController extends AbstractController
{
    /**
     * @var TerritoryPortraitHandler
     */
    private $territoryPortraitHandler;

    /**
     * TerritoryPortraitController constructor.
     * @param TerritoryPortraitHandler $territoryPortraitHandler
     */
    public function __construct(
        TerritoryPortraitHandler $territoryPortraitHandler
    ) {
        $this->territoryPortraitHandler = $territoryPortraitHandler;
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Doctrine\DBAL\DBALException
     */
    public function getTerritoryPortrait(Request $request)
    {
        $data = [];
        $form = $this->createForm(TerritoryType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $this->territoryPortraitHandler->handle($form);
        }

        return $this->render('territoryPortrait/index.html.twig', [
            'data' => $data,
            'filters' => $form->getData()
        ]);
    }
}
