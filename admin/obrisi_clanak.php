<?php

    require_once (dirname(__DIR__)) . "/includes/db.php";

    session_start();

    include (dirname(__DIR__)) . "/includes/session.php";

    // korisnik nije admin? vrati ga na početnu stranu
    if(!isset($_SESSION['isAdmin']) || $_SESSION['isAdmin'] == 0)
    {
        header("Location: ../index.php");
    }

    if(isset($_GET['id']))
    {
        $sql = "DELETE FROM clanci WHERE article_id = ?";
        $stmt = mysqli_prepare($connection, $sql);
        
        $temp_id = $_GET['id'];
        mysqli_stmt_bind_param($stmt, "i", $temp_id);

        if(mysqli_stmt_execute($stmt))
        {
            header("Location: ../index.php");
            $_SESSION['successful_delete'] = true;
        }

    }
    else
    {
        header("Location: ../index.php");
    }
?>