<?php

    $sql = "SELECT article_id, article_naslov FROM clanci LIMIT 5";

    $result = mysqli_query($connection, $sql);
    $_footer_articles = array();
    while($row = mysqli_fetch_assoc($result))
    {
        $_footer_articles[] = $row;
    }

?>


<section id="footer">

    <div class="container">

        <div class="column">

            <h5>Najnovije</h5>

            <?php

                foreach($_footer_articles as $article)
                {
                    echo '<a href="./admin/clanak.php?id=' . $article['article_id'] . '" target="_blank">' . $article['article_naslov'] . '</a>';
                }

            ?>
        
        </div>


        <div class="column">

            <?php if(isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true) { ?>           

                <h5>Administracija</h5>

                <?php

                    if(isset($_SESSION['isAdmin']) && $_SESSION['isAdmin'] == 0)
                    {
                        
                        echo '<a href="./admin/dodijeli_admina.php">Dodijeli admin status</a>';

                    }

                    else
                    {
                        echo '<a href="./admin/dodijeli_admina.php">Ukloni admin status</a>';
                    }

                ?>
            
            <?php } ?>

        </div>

    </div>
        
</section>