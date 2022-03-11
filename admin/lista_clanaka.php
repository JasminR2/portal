<?php

    // uspostavljanje konekcije sa bazom podataka
    require_once (dirname(__DIR__)) . "/includes/db.php";

    session_start();

    // provjera sesije
    include (dirname(__DIR__)) . "/includes/session.php";

    // korisnik nije admin? vrati ga na početnu stranu
    if(!isset($_SESSION['isAdmin']) || $_SESSION['isAdmin'] == 0)
    {
        header("Location: ../index.php");
    }

    if(isset($_GET['query']))
    {
        $_searchTerms = explode(" ", $_GET['query']);
        $_against = '';

        for($i = 0; $i < count($_searchTerms); $i++)
        {
            $_against .= ' +' . $_searchTerms[$i];
        }

        $sql = "SELECT id, naslov, datumObjavljivanja FROM clanci WHERE MATCH (naslov, sadrzaj, sazetak) AGAINST (? IN BOOLEAN MODE)";
        if($stmt = mysqli_prepare($connection, $sql))
        {
            mysqli_stmt_bind_param($stmt, "s", $_against);
            if(mysqli_stmt_execute($stmt))
            {
                $result = mysqli_stmt_get_result($stmt);
                while($row = mysqli_fetch_assoc($result))
                {
                    $_data_arr[] = $row;
                }
            }
        }

    }

    else
    {
        $sql = "SELECT id, naslov, datumObjavljivanja FROM clanci ORDER BY datumObjavljivanja DESC";

        $result = mysqli_query($connection, $sql);
        while($row = mysqli_fetch_assoc($result))
        {
            $_data_arr[] = $row;
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
        <title>Lista članaka - Portal</title>
    </head>

    <body>

        <?php include (dirname(__DIR__)) . "/includes/header.php"; ?>

            <section id="articles_list">

                <div class="articles_list_header">
                    
                    <h4>Lista članaka</h4>

                    <form action="/portal/admin/lista_clanaka.php" method="GET">

                        <input type="text" placeholder="Pretraga" name="query">

                    </form>

                </div>

                <div class="articles_table">

                    <div class="table_header">

                        <div class="article_title">

                            <p style="text-transform: uppercase; color: #a8c6df;">Naslov članka</p>

                        </div>

                        <div class="article_publishDate">

                            <p style="text-transform: uppercase; color: #a8c6df;">Datum objave</p>

                        </div>

                        <div class="article_options">

                            <p style="text-transform: uppercase; color: #a8c6df;">Opcije</p>

                        </div>

                    </div>

                    <?php foreach($_data_arr as $article) { ?>

                    <div class="table_body" style="padding: .5rem 0;">

                        <div class="article_title">

                            <a href="/portal/admin/clanak.php?id=<?php echo $article['id']; ?>" style="color: #333; font-size: 1.125rem;"><?php echo $article['naslov']; ?></a>

                        </div>

                        <div class="article_publishDate">

                            <p style="font-size: 1.125rem;"><?php echo date("d.m.Y", strtotime($article['datumObjavljivanja'])); ?></p>

                        </div>

                        <div class="article_options" style="display: flex; justify-content: flex-end; column-gap: 1rem;">

                            <a href="admin/izmijeni_clanak.php?id=<?php echo $article['id']; ?>" style="color: #2d9cdb; font-size: 1.125rem" title="Uredi članak"><i class="las la-edit fa-fw"></i></a>
                            <a href="admin/obrisi_clanak.php?id=<?php echo $article['id']; ?>" style="color: #2d9cdb; font-size: 1.125rem" title="Obriši članak"><i class="las la-trash fa-fw"></i></a>

                        </div>

                    </div>

                    <?php } ?>

                </div>

            </section>
                    

        <?php include (dirname(__DIR__)) . "/includes/footer.php"; ?>
        
    </body>
</html>