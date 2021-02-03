create or replace view carrying_structure_view AS
select epci.siren, epci.name, ed.district_id, dist.department_id, epci.year,'epci' as type
from epci
join epci_district ed on ed.epci_id = epci.id
join district dist on dist.id = ed.district_id
where is_own_tax = true
or epci.siren IN (select siren_carrier from data_line join epci on epci.siren = data_line.siren_carrier)
group by epci.siren, epci.name, ed.district_id, dist.department_id, type, epci.year
union
select city.siren, city.name, district.id, district.department_id, city.year, 'city' as type
from city
join district on district.id = city.district_id
group by city.siren, city.name, district.id, district.department_id, city.year
