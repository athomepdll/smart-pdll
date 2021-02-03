Documentation technique
==========================
http://tech.athome-solution.fr/tech/architecture


````
[Place]1-1[District],[Place]1-1[Department],[Place]1-1[City],[Place]1-1[Group],[City]0..*-1[City_Epci],[City_Epci]1-0..*[Epci],[District]0..*-1[District_Epci],[District_Epci]1-0..*[Epci],[Epci]0..*-[Epci],[Department]1-0..*[District],[District]1-0..*[City],[Epci]1-1[Siren_Insee],[DataType]1-0..*[Data],[DataLine]0..*-1[ImportLog],[Column]0..*-1[DataType],[Column]0..*-1[Config],[Config]1-1[ImportModel],[ImportType]1-0..*[ImportModel],[Domain]1-*[ImportModel_Domain],[ImportModel_Domain]*-1[ImportModel],[ImportModel]1-0..*[ImportLog],[Notification]1-1[ImportLog],[ImportLog]1-1[ImportMetadata],[DataLine]1-1..*[Data],[User]1-0..*[ImportLog],[ImportLog]0..*-1[Department],[DataLine]0..*-1[City]
````

![Uml](http://yuml.me/diagram/plain/class/[Place]1-1[District],[Place]1-1[Department],[Place]1-1[City],[Place]1-1[Group],[City]0..*-1[City_Epci],[City_Epci]1-0..*[Epci],[District]0..*-1[District_Epci],[District_Epci]1-0..*[Epci],[Epci]0..*-[Epci],[Department]1-0..*[District],[District]1-0..*[City],[Epci]1-1[Siren_Insee],[DataType]1-0..*[Data],[DataLine]0..*-1[ImportLog],[Column]0..*-1[DataType],[Column]0..*-1[Config],[Config]1-1[ImportModel],[ImportType]1-0..*[ImportModel],[Domain]1-*[ImportModel_Domain],[ImportModel_Domain]*-1[ImportModel],[ImportModel]1-0..*[ImportLog],[Notification]1-1[ImportLog],[ImportLog]1-1[ImportMetadata],[DataLine]1-1..*[Data],[User]1-0..*[ImportLog],[ImportLog]0..*-1[Department],[DataLine]0..*-1[City])

Clone docker environment
-------------------------
````
git clone git@git.athome-solution.fr:athome/docker.git

git checkout sgar/smart

git clone git@git.athome-solution.fr:sgar/smart.git ./sources

make && make start && make shell
````
In make shell :
composer install
````
php bin/console d:s:u -f
exit
````
````
cd ./sources
make yarn && make watch
````
in .env : 
````
APP_ENV=dev
APP_HOST=localhost
APP_HTTP=http
APP_SECRET=2edbf108f20df22835c1b44ebb9fb5ed

````

PostgreSQL 
--------------

La base de données PostgreSQL needs the folowing extensions :
    - PostGIS
    - tablefunc

tablefunc : 

````
CREATE EXTENSION tablefunc;
````

Because of median's aggregation necessity copy paste the following instructions in your database :

````
CREATE OR REPLACE FUNCTION _final_median(NUMERIC[])
   RETURNS NUMERIC AS
$$
   SELECT AVG(val)
   FROM (
     SELECT val
     FROM unnest($1) val
     ORDER BY 1
     LIMIT  2 - MOD(array_upper($1, 1), 2)
     OFFSET CEIL(array_upper($1, 1) / 2.0) - 1
   ) sub;
$$
LANGUAGE 'sql' IMMUTABLE;
 
CREATE AGGREGATE median(NUMERIC) (
  SFUNC=array_append,
  STYPE=NUMERIC[],
  FINALFUNC=_final_median,
  INITCOND='{}'
);
````
