Instaliacija
----------------------------------
1. Išarchivuojame failus į reikiamą direktoriją
1. Nustatome tinkamus duomenų bazės nustatymus faile
`<instaliacijos_direktorija>\app\config`
1. Naudodamiesi [composer](https://github.com/composer/composer) įrankiu įrašome reikalingus dependencies
`php composer.phar install`
1. Atnaujiname duomenų bazės schema naudodamiesi komanda
`php app/console doctrine:schema:create`
1. Sukuriame pagr. administratoriaus account'ą naudodamiesi komanda
`php app/console fos:user:create <nik'as> <paštas> <slaptažodis> --super-admin`
