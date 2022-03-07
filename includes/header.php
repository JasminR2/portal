<section id="header">

    <div class="container">

        <a href="/portal/index.php" class="header-logo">Portal</a>

        <span>

            <?php if(!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] == false) { ?>

                <a href="/portal/admin/prijava.php"><button class="header-button" type="button">Prijava</button></a>            
                <a href="/portal/admin/registracija.php"><button class="header-button" type="button">Registracija</button></a>

            <?php } 
                else if(isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true) {
                    if(isset($_SESSION['isAdmin']) && $_SESSION['isAdmin'] == 1) { ?>
                        <a href="/portal/admin/spremi_clanak.php"><button class="header-button new-article" type="button"><i class="las la-pen fa-fw"></i> Novi članak</button></a>
                    <?php } ?>
                    <a href="/portal/admin/odjava.php"><button class="header-button" type="button">Odjava</button></a>
            <?php } ?>

        </span>

    </div>

</section>