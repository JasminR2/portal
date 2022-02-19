<?php

    if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] == false)
    {
        header("Location: ../index.php");
    }

?>