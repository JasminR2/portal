<section id="footer">

    <?php if(isset($_SESSION['loggedin']) && $_SESSION['loggedin'] && $_SESSION['isAdmin'] == 0) { ?>

        <div class="container">

                <a href="/portal/admin/dodijeli_admina.php">Dodijeli admina</a>
        
        </div>

    <?php } ?>

</section>