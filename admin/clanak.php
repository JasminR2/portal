<?php

    // uspostavljanje konekcije sa bazom podataka
    require_once (dirname(__DIR__)) . "/includes/db.php";

    session_start();
    
    // provjera sesije
    require_once (dirname(__DIR__)) . "/includes/session.php";

    // izvuci podatke iz baze ukoliko je postavljen ID članka
    if(isset($_GET['id']))
    {
        $sql = "SELECT * FROM clanci WHERE id = ?";
        if($stmt = mysqli_prepare($connection, $sql))
        {
            $temp_id = $_GET['id'];
            mysqli_stmt_bind_param($stmt, "i", $temp_id);
            if(mysqli_stmt_execute($stmt))
            {
                $_article_data = array();
                $result = mysqli_stmt_get_result($stmt);
                while($row = mysqli_fetch_assoc($result))
                {
                    $_article_data = $row;
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
        <link href="https://fonts.googleapis.com/css2?family=Lato:wght@400&family=Poppins&family=Raleway:wght@300;500&family=Roboto&family=Nunito&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="https://maxst.icons8.com/vue-static/landings/line-awesome/line-awesome/1.3.0/css/line-awesome.min.css">
        <title><?php echo $_article_data['naslov']; ?> - Portal</title>
    </head>

    <body>

        <?php include (dirname(__DIR__)) . "/includes/header.php"; ?>

        <section id="article_body">

            <div class="article_header">

                <h4><?php echo $_article_data['naslov']; ?></h4>

            </div>

            <img src="../images/uploads/<?=$_article_data['thumbnailNaziv'];?>" class="article_photo" />

            <p class="article_content"><?=nl2br($_article_data['sadrzaj']);?></p>

        </section>
        
        <?php include (dirname(__DIR__)) . "/includes/footer.php"; ?>

    </body>

</html>