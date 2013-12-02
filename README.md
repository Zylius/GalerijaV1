Galerija
========================
Paveiksliukų bei albumų atvaizdavimo sistema

Funkcionalumas
----------------------------------
* Vartotojai
    * Paprasto vartotojo registracija
    * Prisijungimo forma
    * Dvi vartotojų rolės: administratorius ir eilinis vartotojas
    * Administratorius turi teisę atlikti visas įmanomas operacijas su bet kurio vartotojo objektais
    * Eilinis vartotojas gali
        * Trinti, redaguoti savo nuotraukas
        * Įkelti nuotraukas
        * Komentuoti bet kurią nuotrauką
        * Trinti savo bei savo nuotraukų komentarus
        * "Like'inti" bet kurias nuotraukas
        * Trinti, redaguoti albumus
        * Kurti albumus
        * Kurti tag'us
        * Nustatyti savo albumų titulinę nuotrauką

* Nuotraukos
    * Nuotraukų atvaizdavimui naudojamas [isotope](https://github.com/desandro/isotope)
    * Nuotraukų sąrašo puslapiavimui naudojamas [infinite scroll](https://github.com/paulirish/infinite-scroll)
    * Įkeliant nuotrauką rodomas įkėlimo progresas, nuotrauką galima priskirti daugeliui albumų, pasirinkti tag'us
    * Kiekvienai nuotraukai sukuriamas thumbnails'as
    * Užvedus ant nuotraukos thumbnails'o pelytę parodomos galimos operacijos (trinimas, redagavimas, titulinės nuotraukos nustatymas)
    * Trinant nuotrauką reikia pasirinkti ar ją norima ištrinti tik iš šio, ar iš visų albumų
    * Paspaudus ant nuotraukos thumbnails'o nuotrauka atvaizduojama [fancybox](https://github.com/fancyapps/fancyBox) lange, ją galima komentuoti, like'inti, peržiūrėti tag'us
    * Nuotraukas galima filtruoti pagal tag'us, filtruojant rodomas autocompletion
    * Nuotraukų trinimas, komentavimas, like'inimas, titulinės nuotraukos nustatymas, atvaizdavimas fancybox realizuoti ajax užklausomis

* Albumai
    * Albumų atvaizdavimui naudojamas [isotope](https://github.com/desandro/isotope)
    * Albumus galima trinti, redaguoti.

Reikalavimai
----------------------------------
PHP 5.3.3 ir naujesnė versija

MySQL 4.x ir 5.x

Composer
