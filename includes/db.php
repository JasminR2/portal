<?php

    define("DataBase_server", "localhost");
    define("DataBase_username", "root");
    define("DataBase_password", "");
    define("DataBase_name", "cms");

    // izvuci sadržaj iz cms.sql fajla
    $tables = file_get_contents((dirname(__DIR__)) . "/cms.sql");

    // uspostavi konekciju sa bazom podataka
    $connection = mysqli_connect(DataBase_server, DataBase_username, DataBase_password, DataBase_name);

    // pošalji više upita(sadržaj iz cms.sql) bazi podataka
    mysqli_multi_query($connection, $tables);

?>