<?php

    session_start();

    include (dirname(__DIR__)) . "/includes/session.php";

    $_SESSION['isAdmin'] = 1;

    header("Location: ../index.php");

?>