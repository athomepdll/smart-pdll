<?php


namespace App\Factory\Query;

/**
 * Class MetaDataQueryFactory
 * @package App\Factory\Query
 */
class MetaDataQueryFactory extends AbstractSqlFactory
{
    const SELECT = [
        'data_view.year as "Année"',
        'import_metadata.data_date as "Date de la donnée"',
        'import_metadata.data_provider as "Fournisseur de la donnée"',
        'import_metadata.service_emitter as "Service émetteur"',
        'import_metadata.emitter_last_name as "Nom émetteur"',
        'import_metadata.emitter_first_name as "Prénom émetteur"',
        'import_metadata.emitter_mail as "Mail émetteur"',
        'import_metadata.emitter_phone as "Téléphone émetteur"',
    ];

    const GROUP_BY = [
        'data_view.year',
        'import_metadata.data_date',
        'import_metadata.data_provider',
        'import_metadata.service_emitter',
        'import_metadata.emitter_last_name',
        'import_metadata.emitter_first_name',
        'import_metadata.emitter_mail',
        'import_metadata.emitter_phone',
    ];

    const ORDER_BY = [
        'data_view.year',
    ];

    /**
     * @param array $filters
     * @return string
     */
    public function create(array $filters)
    {
        $select = join(', ', self::SELECT);
        $sourceQuery = "SELECT {$select} FROM data_view ";
        $sourceQuery .= " JOIN import_log ON import_log.id = import_log_id";
        $sourceQuery .= " JOIN import_metadata ON import_metadata.id = import_log.import_metadata_id";
        $sourceQuery .= " WHERE ";

        $this->sqlQueryBuilderHelper
            ->addDataLevelFilter($filters, $sourceQuery)
            ->addImportModelId($filters, $sourceQuery, 'data_view')
            ->addYearStart($filters, $sourceQuery, 'data_view')
            ->addYearEnd($filters, $sourceQuery, 'data_view')
            ->addDepartmentFilter($filters, $sourceQuery, 'data_view')
            ->addDistrict($filters, $sourceQuery)
            ->addEpci($filters, $sourceQuery)
            ->addCity($filters, $sourceQuery)
            ->addSirenCarrierWithHistory($filters, $sourceQuery, 'data_view');

        return $sourceQuery . ' GROUP BY ' . join(', ', self::GROUP_BY) . ' ORDER BY ' . join(', ', self::ORDER_BY);
    }
}
