<?php

    session_start();

    include (dirname(__DIR__)) . "/includes/session.php";

    if(isset($_SESSION['isAdmin']) && $_SESSION['isAdmin'] == 0) { $_SESSION['isAdmin'] = 1; } // korisnik nije Administrator? postavi ga kao Admina
    else { $_SESSION['isAdmin'] = 0; } // ukoliko jest, ukloni ga kao Admina

    header("Location: ../index.php");

?>