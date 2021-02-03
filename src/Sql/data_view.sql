create or replace view data_view as
select data.id                                                              as data_id,
       import_model.id                                                      as import_model_id,
       import_model.color                                                   as import_model_color,
       data_line.id                                                         as data_line_id,
       data_line.siren_root_id                                              as city_id,
       data_city.siren                                                      as city_siren,
       data_city.actual_city_id                                             as actual_city_id,
       data_line.siren_carrier                                              as siren_carrier,
       data_city.insee                                                      as city_insee,
       data.name                                                            as column_name,
       data.value                                                           as value,
       COALESCE(data_city.name, 'Intercommunal')                            as city_name,
       import_model.name                                                    as import_model_name,
       enumeration.name                                                     as data_level,
       import_log.year                                                      as year,
       import_log.id                                                        as import_log_id,
       import_type.name                                                     as import_type,
       data.id                                                              as data_view_id,
       department.id                                                        as department_id,
       COALESCE(district.id, csv.district_id)                               as district_id,
       csv.name                                                             as carrier_name,
       actual_city.siren                                                    as actual_city_siren,
       (SELECT COUNT(*) FROM city WHERE city.actual_city_id = data_city.id) as has_city_changed
from data_line
         join data on data.data_line_id = data_line.id
         join enumeration on enumeration.id = data.data_level_id
         join import_log on import_log.id = data_line.import_log_id
         join import_model on import_model.id = import_log.import_model_id
         join import_type on import_type.id = import_model.import_type_id
         join department on department.id = import_log.department_id
         left join city data_city on data_city.id = data_line.siren_root_id
         left join district on district.id = data_city.district_id
         left join city actual_city on actual_city.id = data_city.actual_city_id
         left join carrying_structure_view csv on csv.siren = data_line.siren_carrier
                                                and csv.year = import_log.year
                                                and department.id = csv.department_id
                                                and csv.district_id = district.id
group by data.id,
         import_model.id,
         data_line.id,
         data_line.siren_root_id,
         data_city.siren,
         data_city.actual_city_id,
         data_line.siren_carrier,
         data_city.insee,
         data_city.name,
         data.name,
         data.value,
         import_model.name,
         enumeration.name,
         import_log.year,
         import_log.id,
         import_type.name,
         data.id,
         department.id,
         csv.name,
         district.id,
         csv.district_id,
         data_city.id,
         actual_city.siren

