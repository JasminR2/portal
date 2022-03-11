<?php

    $sql = "SELECT id, naslov FROM clanci WHERE DATE(datumObjavljivanja) <= CURDATE() LIMIT 5";

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
                    echo '<a href="./admin/clanak.php?id=' . $article['id'] . '" target="_blank">' . $article['naslov'] . '</a>';
                }

            ?>
        
        </div>


        <div class="column">

            <?php if(isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true) { ?>           

                <h5>Administracija</h5>

                <?php

                    if(isset($_SESSION['isAdmin']) && $_SESSION['isAdmin'] == 0)
                    {
                        
                        echo '<a href="/portal/admin/dodijeli_admina.php">Dodijeli admin status</a>';

                    }

                    else
                    {
                        echo '<a href="/portal/admin/dodijeli_admina.php">Ukloni admin status</a>';
                        echo '<a href="/portal/admin/lista_clanaka.php">Lista članaka</a>';
                    }

                ?>
            
            <?php } ?>

        </div>

    </div>
        
</section>