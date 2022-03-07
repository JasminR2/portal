<?php

    session_start();
    
    // ukoliko je korisnik prijavljen - obriši sesiju
    if(isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true);
    {
        session_destroy();
    }

    header("Location: ../index.php");

?>