<?php

    // uspostavljanje konekcije sa bazom podataka
    require_once "includes/db.php";

    session_start();

    // id stranice nije postavljen? postavi id na 1
    if(!isset($_GET['stranica']))
    {
        $_page = 1;
    } // id stranice postavljen, dodijeli vrijednost varijabli
    else { $_page = $_GET['stranica']; }

    // broj članaka po stranici
    $_articles_per_page = 15;
    // id prvog članka na stranici
    $_firstid_on_page = ($_page-1) * $_articles_per_page;

    // prebroji članke u bazi
    $sql = "SELECT COUNT(*) FROM clanci";
    $result = mysqli_query($connection, $sql);
    $_number_of_articles = mysqli_fetch_array($result)[0];
    // postavi odgovarajući broj stranica (npr. 35 članaka/10 članaka po stranici = 4 stranice)
    $_number_of_pages = ceil($_number_of_articles / $_articles_per_page);

    $sql = "SELECT * FROM clanci LIMIT $_firstid_on_page, $_articles_per_page";
    $result = mysqli_query($connection, $sql);
    $_article_data = array();
    while($row = mysqli_fetch_assoc($result))
    {
        $_article_data[] = $row;
    }

    if(isset($_SESSION['successful_create']) && $_SESSION['successful_create'] == true) {
        echo '<script>alert("Uspiješno ste kreirali novi članak.");</script>';
        unset($_SESSION['successful_create']); }

    // obavijest za uređivanje članka
    if(isset($_SESSION['successful_edit']) && $_SESSION['successful_edit'] == true) {
        echo '<script>alert("Uspiješno ste uredili članak.");</script>';
        unset($_SESSION['successful_edit']); }

    // obavijest za brisanje članka
    if(isset($_SESSION['successful_delete']) && $_SESSION['successful_delete'] == true) {
        echo '<script>alert("Uspiješno ste obrisali članak.");</script>';
        unset($_SESSION['successful_delete']); }

?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="css/styles.css">    
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Lato:wght@400&family=Poppins&family=Raleway:wght@300;500&family=Roboto&family=Nunito&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="https://maxst.icons8.com/vue-static/landings/line-awesome/line-awesome/1.3.0/css/line-awesome.min.css">
        <title>Početna - Portal</title>
    </head>
    <body>

        <?php include "includes/header.php"; ?>

        <section id="articles">

            <div class="container">

                <?php foreach($_article_data as $article) { 
                    $_publish_date = new DateTime($article['article_datumObjavljivanja']);
                    $result = $_publish_date->format("d.m.Y");
                    ?>
                    

                    <div class="article-wrapper">

                        <?php
                            // thumbnail slika članka
                            if(!empty($article['article_thumbnailName']))
                            { 
                                echo '<img class="article-thumbnail" src="images/uploads/' . $article['article_thumbnailName'] . '" />';
                            }
                            else
                            {
                                echo '<img class="article-thumbnail" src="images/placeholder.png" />';
                            }
                            echo '<div class="article-data"><a class="title" href="admin/clanak.php?id=' . $article['article_id'] . '" target="_blank">' . $article['article_naslov'] . '</a>';
                            // sažetak članka
                            if(!empty($article['article_sazetak']))
                            {
                                echo '<p class="summary">' . htmlspecialchars($article['article_sazetak']) . '</p>'; 
                            }
                            else // ukoliko nema sažetka - izvuci prvih 150 karaktera iz sadržaja
                            {
                                // ukloni HTML tagove ukoliko postoj iz sadržaja
                                $article['article_tekst'] = strip_tags($article['article_tekst']);
                                // provjeri da li je dužina sadržaja veća od 150 karaktera
                                if(strlen($article['article_tekst']) > 150)
                                {
                                    // skrati sadržaj na prvih 150 karaktera
                                    $_cutoff_string = substr($article['article_tekst'], 0, 150);
                                    $_endpoint = strrpos($_cutoff_string, ' ');

                                    $article['article_tekst'] = $_endpoint ? substr($_cutoff_string, 0, $_endpoint) : substr($_cutoff_string, 0);
                                    $article['article_tekst'] .= '... <a href="admin/clanak.php?id=' . $article['article_id'] . '" style="font-size: .875rem;" target="_blank">Pročitaj više</a>';
                                }
                                echo '<p class="summary">' . $article['article_tekst'] . '</p>';
                            }

                            echo '<p class="article-publishdate">Objavljeno: ' . $result . '</p></div>'; 
                        
                            if(isset($_SESSION['isAdmin']) && $_SESSION['isAdmin'] == 1)
                            {
                        ?>

                            <div class="article-options">

                                <i class="las la-cog fa-fw"></i>

                                <div class="dropdown">
                                    <a href="admin/izmijeni_clanak.php?id=<?php echo $article['article_id']; ?>"><i class="las la-edit fa-fw"></i> Uredi članak</a>
                                    <a href="admin/obrisi_clanak.php?id=<?php echo $article['article_id']; ?>"><i class="las la-trash fa-fw"></i> Obriši članak</a>
                                </div>
                                
                            </div>

                        <?php } ?>
                        

                    </div>

                <?php } ?>

            </div>

            <div class="pagination">

                <?php
                    // kreiraj linkove za paginaciju
                    for($i = 1; $i <= $_number_of_pages; $i++)
                    {
                        echo '<span class="pagination-page"><a href="index.php?stranica=' . $i . '">' . $i . '</a></span>';
                    }
                ?>

            </div>

        </section>

        <?php include (dirname(__FILE__)) . "/includes/footer.php"; ?>

    </body>

    <footer>

        <script type="text/javascript">

            const articleOptions = document.querySelectorAll(".article-options i");

            articleOptions.forEach(function(el) {
                el.addEventListener("click", (event) => { 
                    if(el.nextElementSibling.style.display === "block") { el.nextElementSibling.style.display = "none"; }
                    else { el.nextElementSibling.style.display = "block"; }
                });
            });

        </script>

    </footer>
</html>