<?php

    // korisnik nije prijavljen - vrati ga na početnu stranicu
    if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] == false)
    {
        header("Location: ../index.php");
    }

?>