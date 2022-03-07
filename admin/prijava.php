<?php

    // uspostavljanje konekcije sa bazom podataka
    require_once (dirname(__DIR__)) . "/includes/db.php";

    session_start();

    // forma poslana
    if($_SERVER['REQUEST_METHOD'] == "POST")
    {
        $_emailerror = $_lozinkaerror = ''; // isprazni varijable

        if(empty($_POST['email'])) // email nije unešen
        {
            $_emailerror = '<p style="color: red;">* obavezno</p>';
        }
        // ukoliko je email unešen - provjeri da li postoji račun sa tim emailom
        else
        {
            $sql = "SELECT * FROM korisnici WHERE email = ?";
            if($stmt = mysqli_prepare($connection, $sql))
            {
                mysqli_stmt_bind_param($stmt, "s", $temp_email);
                $temp_email = trim($_POST['email']);
                if(mysqli_stmt_execute($stmt))
                {
                    mysqli_stmt_store_result($stmt);
                    if(mysqli_stmt_num_rows($stmt) == 0) { $_emailerror = '<p style="color: red;">* ne postoji račun sa tim e-mailom</p>'; } // račun ne postoji
                }
            }
        }

        if(empty($_POST['password'])) { $_lozinkaerror = '<p style="color: red;">* obavezno</p>'; } // lozinka nije unešena

        // ukoliko su i email i lozinka unešeni - nastavi sa prijavom
        if(empty($_emailerror) && empty($_lozinkaerror))
        {
            $_email = trim($_POST['email']);
            $_lozinka = trim($_POST['password']);

            $sql = "SELECT user_id, email, lozinka, isAdmin FROM korisnici WHERE email = ?";

            if($stmt = mysqli_prepare($connection, $sql))
            {
                mysqli_stmt_bind_param($stmt, "s", $_email);
                if(mysqli_stmt_execute($stmt))
                {
                    mysqli_stmt_store_result($stmt);
                    if(mysqli_stmt_num_rows($stmt) == 1)
                    {
                        mysqli_stmt_bind_result($stmt, $user_id, $_email, $hashed_password, $isAdmin);
                        if(mysqli_stmt_fetch($stmt))
                        {
                            // lozinka odgovara hashu - prijava uspiješna
                            if(password_verify($_lozinka, $hashed_password))
                            {
                                $_SESSION['user_id'] = $user_id;
                                $_SESSION['loggedin'] = true;
                                $_SESSION['isAdmin'] = $isAdmin;
                                header("Location: ../index.php");
                            }
                            else
                            {
                                $_lozinkaerror = '<p style="color: red;">* pogrešna lozinka</p>';
                            }
                        }
                    }
                }
            }
        }

    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/styles.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Lato:wght@400&family=Poppins&family=Raleway:wght@300&family=Roboto&family=Nunito&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://maxst.icons8.com/vue-static/landings/line-awesome/line-awesome/1.3.0/css/line-awesome.min.css">
    <title>Prijava - Portal</title>
</head>
<body>
    
    <?php include dirname(__DIR__) . "/includes/header.php"; ?>

    <section id="login_page">

        <form action="prijava.php" id="login" method="POST">
            
            <div class="inputwrapper">
                <label for="email">E-Mail</label>
                <input type="email" id="email" name="email" autocomplete="off">
                <span><?php if(isset($_emailerror)) { echo $_emailerror; } ?></span>
            </div>

            <div class="inputwrapper">
                <label for="password">Lozinka</label>
                <input type="password" id="password" name="password">
                <span><?php if(isset($_lozinkaerror)) { echo $_lozinkaerror; } ?></span>
            </div>

            <input type="submit" value="Prijava" class="logreg">


        </form>

    </section>

</body>
</html>