Instaliacija
----------------------------------
1. Išarchivuojame failus į reikiamą direktoriją

1. Naudodamiesi [composer](https://github.com/composer/composer) įrankiu įrašome reikalingus dependencies

`composer update`

šiam procesui pasibaigus jums reikės įvesti teisingą duom. bazės informaciją.

1. Sukuriame duom. bazę ir atnaujiname duomenų bazės schema naudodamiesi komandomis

`php app/console doctrine:database:create`

`php app/console doctrine:schema:create`

1. Sukuriame pagr. administratoriaus account'ą naudodamiesi komanda

`php app/console fos:user:create <nik'as> <paštas> <slaptažodis> --super-admin`

Jei produkcinėje aplinkoje nekraunami stiliaus (.css) failai, atnaujiname juos šiomis komandomis

`php app/console assetic:dump --env=prod`

`php app/console assets:install`