<?php


namespace App\Services\Insee;

use App\Repository\CityRepository;

/**
 * Class ExportInsee
 * @package App\Services\Insee
 */
class RegionalPerimeterExportService
{
    const HEADERS = [
        'Region',
        'departement_nom',
        'departement_numero',
        'arrondissement_nom',
        'arrondissement_numero',
        'EPCI_nom',
        'EPCI_INSEE',
        'EPCI_SIREN',
        'commune_nom',
        'commune_INSEE',
        'commune_SIREN',
        'nouvelle_commune_nom',
        'nouvelle_commune_INSEE',
        'nouvelle_commune_SIREN'
    ];

    /**
     * @var CityRepository
     */
    private $cityRepository;

    /**
     * RegionalPerimeterExport constructor.
     * @param CityRepository $cityRepository
     */
    public function __construct(
        CityRepository $cityRepository
    ) {
        $this->cityRepository = $cityRepository;
    }

    /**
     * @param array $filters
     * @return string
     */
    public function export(array $filters)
    {
        $year = $filters['year'];
        $data = $this->getData($year);

        return $this->writeCsv($data);
    }

    /**
     * @param array $data
     * @return string
     */
    private function writeCsv(array $data)
    {
        ob_start();
        $output = fopen('php://output', 'w');
        fputs($output, $bom = (chr(0xEF) . chr(0xBB) . chr(0xBF)));

        fputcsv($output, self::HEADERS);
        foreach ($data as $datum) {
            fputcsv($output, $datum);
        }

        return ob_get_clean();
    }

    /**
     * @param int $year
     * @return mixed
     */
    private function getData(int $year)
    {
        return $this->cityRepository->getByYear($year);
    }
}
