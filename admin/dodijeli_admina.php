<?php

    session_start();

    include (dirname(__DIR__)) . "/includes/session.php";

    if(isset($_SESSION['isAdmin']) && $_SESSION['isAdmin'] == 0) { $_SESSION['isAdmin'] = 1; }
    else { $_SESSION['isAdmin'] = 0; }

    header("Location: ../index.php");

?>