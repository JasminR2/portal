<?php

    session_start();

    require_once (dirname(__DIR__)) . "/includes/db.php";

    if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] == true)
    {
        header("Location: ../index.php");
    }

    if($_SERVER['REQUEST_METHOD'] == "POST")
    {
        $_emailerror = $_lozinkaerror = '';

        if(empty($_POST['email'])) // e-mail nije unešen
        {
            $_emailerror = '<p style="color: red;">* obavezno</p>';
        }
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
                    if(mysqli_stmt_num_rows($stmt) > 0) { $_emailerror = '<p style="color: red;">* već postoji račun sa tim e-mailom</p>'; } // račun već postoji
                }
            }
        }

        if(empty($_POST['password'])) { $_lozinkaerror = '<p style="color: red;">* obavezno</p>'; } // lozinka nije unešena

        if(empty($_emailerror) && empty($_lozinkaerror))
        {
            $_email = trim($_POST['email']);
            $_lozinka = password_hash($_POST['password'], PASSWORD_BCRYPT, ["cost" => 11]);

            $sql = "INSERT INTO korisnici (email, lozinka) VALUES (?, ?)";
            if($stmt = mysqli_prepare($connection, $sql))
            {
                mysqli_stmt_bind_param($stmt, "ss", $_email, $_lozinka);
                if(mysqli_stmt_execute($stmt))
                {
                    header("Location: ../index.php");
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
        <title>Registracija - Portal</title>
    </head>

    <body>

        <?php include dirname(__DIR__) . "/includes/header.php"; ?>
        
        <section id="login_page">

            <form action="registracija.php" id="login" method="POST">
                
                <div class="inputwrapper">
                    <label for="email">E-Mail <span style="color: red;">*</span></label>
                    <input type="email" id="email" name="email" autocomplete="off">
                    <span><?php if(isset($_emailerror)) { echo $_emailerror; } ?></span>
                </div>

                <div class="inputwrapper">
                    <label for="password">Lozinka <span style="color: red;">*</span></label>
                    <input type="password" id="password" name="password">
                    <span><?php if(isset($_lozinkaerror)) { echo $_lozinkaerror; } ?></span>
                </div>

                <input type="submit" value="Registracija" class="logreg">

            </form>

        </section>

    </body>

</html>