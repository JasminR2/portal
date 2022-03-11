<?php

    // uspostavljanje konekcije sa bazom podataka
    require_once (dirname(__DIR__)) . "/includes/db.php";

    session_start();

    // provjera sesije
    include (dirname(__DIR__)) . "/includes/session.php";

    if(isset($_SESSION['isAdmin']) && $_SESSION['isAdmin'] == 0) // korisnik nije Administrator? postavi ga kao Admina
    {
        $_SESSION['isAdmin'] = 1;
        $sql = "UPDATE korisnici SET isAdmin = 1 WHERE id = " . $_SESSION['user_id'];
        mysqli_query($connection, $sql);
    }
    else // ukoliko jest, ukloni ga kao Admina
    {
        $_SESSION['isAdmin'] = 0;
        $sql = "UPDATE korisnici SET isAdmin = 0 WHERE id = " . $_SESSION['user_id'];
        mysqli_query($connection, $sql);
    } 

    header("Location: ../index.php");

?>