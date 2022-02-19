<?php

    define("DataBase_server", "localhost");
    define("DataBase_username", "root");
    define("DataBase_password", "");
    define("DataBase_name", "cms");

    $tables = file_get_contents((dirname(__DIR__)) . "/cms.sql");

    $connection = mysqli_connect(DataBase_server, DataBase_username, DataBase_password, DataBase_name);

    mysqli_multi_query($connection, $tables);

?>