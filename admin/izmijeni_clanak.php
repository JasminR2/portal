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

    // id postavljen - izvuci podatke iz baze
    if(isset($_GET['id']))
    {
        $sql = "SELECT * FROM clanci WHERE article_id = ?";
        if($stmt = mysqli_prepare($connection, $sql))
        {
            $temp_id = $_GET['id'];
            mysqli_stmt_bind_param($stmt, "i", $temp_id);
            if(mysqli_stmt_execute($stmt))
            {
                $result = mysqli_stmt_get_result($stmt);
                while($row = mysqli_fetch_assoc($result))
                {
                    $_article_data = $row;
                }
            }
        }
    }

    // forma poslana
    if($_SERVER['REQUEST_METHOD'] == "POST")
    {
        $_nasloverror = $_sadrzajerror = $_slikaerror = ''; // isprazni varijable

        if(empty($_POST['title'])) { $_nasloverror = '<p style="color: red;">* obavezno</p>'; } // nije unešen naslov
        if(empty($_POST['content'])) { $_sadrzajerror = '<p style="color: red;">* obavezno</p>'; } // nije unešen sadržaj

        // ako array za upload nije prazan i ako je uploadan novi thumbnail - provjeri ekstenziju uploadanog fajla
        if($_FILES['thumbnail']['error'] != 4 && $_POST['new_thumbnail'] == "1")
        {
            $_allowed = array("image/png", "image/jpg", "image/jpeg");
            $_uploaded = $_FILES['thumbnail']['type'];

            if(!in_array($_uploaded, $_allowed)) { $_slikaerror = '<p style="color: red;">* nedozvoljen format slike</p>'; }
        }

        // ako nema grešaka za naslov, sadržaj i sliku
        if(empty($_nasloverror) && empty($_sadrzajerror) && empty($_slikaerror))
        {
            $_id_clanka = $_GET['id'];
            $_naslov = $_POST['title'];
            $_sadrzaj = $_POST['content'];
            $_sazetak = $_POST['summary'];
            $_datum = date("Y-m-d", strtotime($_POST['publish_date']));

            // ako array za upload nije prazan i ako je uploadan novi thumbnail - spremi sliku na server
            if($_FILES['thumbnail']['error'] != 4 && $_POST['new_thumbnail'] == "1")
            {
                $_upload_directory = "/images/uploads/";
                $_temp = explode(".", $_FILES['thumbnail']['name']);
                $_filename = bin2hex(str_shuffle($_FILES['thumbnail']['tmp_name']));
                $_filename = (strlen($_filename) < 24) ? str_pad($_filename, 24, random_bytes(8)) : substr($_filename, 0, 24);
                $_filename = $_filename . '.' . end($_temp);
                move_uploaded_file($_FILES['thumbnail']['tmp_name'], dirname(__DIR__) . $_upload_directory . $_filename);
            }
            //ako je array za upload prazan i ako je obrisan stari thumbnail - obriši sliku sa servera
            else
            {
                $_filename = null;
                
                $sql = "SELECT article_thumbnailName FROM clanci WHERE article_id = ?";

                if($stmt = mysqli_prepare($connection, $sql))
                {
                    mysqli_stmt_bind_param($stmt, "i", $_id_clanka);

                    if(mysqli_stmt_execute($stmt))
                    {
                        $result = mysqli_stmt_get_result($stmt);
                        while($row = mysqli_fetch_assoc($result))
                        {
                            $_filepath = dirname(__DIR__) . "/images/uploads/" . $row['article_thumbnailName'];
                            if(file_exists($_filepath))
                            {
                                unlink($_filepath);
                            }
                        }
                    }

                }
            }

            $sql = "UPDATE clanci SET article_naslov = ?, article_sazetak = ?, article_tekst = ?, article_datumObjavljivanja = ?, article_thumbnailName = ? WHERE article_id = ? LIMIT 1";
            if($stmt = mysqli_prepare($connection, $sql))
            {
                mysqli_stmt_bind_param($stmt, "sssssi", $_naslov, $_sazetak, $_sadrzaj, $_datum, $_filename, $_id_clanka);
                if(mysqli_stmt_execute($stmt))
                {
                    header("Location: ../index.php");
                    $_SESSION['successful_edit'] = true;
                }
                else { echo mysqli_error($connection); }
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
        <title>Uređivanje članka - Portal</title>
    </head>
    <body>

        <?php
        
            include (dirname(__DIR__)) . "/includes/header.php";
            
            //ako nije postavljen id stranice, pokaži 404 grešku
            if(!isset($_GET['id']))
            {
                include (dirname(__DIR__)) . "/includes/error404.html";
            }
            else
            {
        ?>

            <section id="newarticle_page">

                <h4>Uređivanje članka</h4>

                <form action="izmijeni_clanak.php?id=<?=$_GET['id'];?>" id="new_article" method="POST" enctype="multipart/form-data">

                    <div class="column">

                        <div class="inputwrapper">
                            <label for="title">Naslov članka</label>
                            <!-- dodijeli vrijednost za naslov izvučen iz baze !-->
                            <input type="text" id="title" name="title" value="<?php echo $_article_data['article_naslov']; ?>" />
                            <!-- prikaži grešku za naslov ukoliko postoji !-->
                            <span><?php if(isset($_nasloverror)) { echo $_nasloverror; } ?></span>
                        </div>

                        <div class="inputwrapper">
                            <label for="content">Sadržaj</label>
                            <!-- dodijeli vrijednost za sadržaj izvučen iz baze !-->
                            <textarea id="content" name="content"><?php echo $_article_data['article_tekst']; ?></textarea>
                            <!-- prikaži grešku za sadržaj ukoliko postoji !-->
                            <span><?php if(isset($_sadrzajerror)) { echo $_sadrzajerror; } ?></span>
                        </div>

                        <div class="inputwrapper">
                            <label for="summary">Sažetak</label>
                            <!-- dodijeli vrijednost za sažetak izvučen iz baze !-->
                            <textarea id="summary" name="summary"><?php echo $_article_data['article_sazetak']; ?></textarea>
                        </div>

                    </div>

                    <div class="column">

                        <div class="inputwrapper">
                            <label for="publish_date">Datum uređivanja</label>
                            <!-- postavi datum uređivanja na trenutni datum !-->
                            <input type="text" id="publish_date" name="publish_date" readonly value="<?php echo date("d.m.Y"); ?>">
                        </div>

                        <div class="inputwrapper">
                            <label>Istaknuta fotografija</label>
                            <input type="file" accept="image/*" name="thumbnail" id="thumbnail" style="display: none;">

                            <!-- da li artikal već ima sliku? ako nema - postavi placeholder, ako ima - prikaži ju !-->
                            <?php
                                if(empty($_article_data['article_thumbnailName']))
                                { ?>
                                <img src="../images/article_placeholder.png" id="thumbnail-preview" alt="Placeholder" />
                            <?php } else {?>
                                <img src="../images/uploads/<?=$_article_data['article_thumbnailName'];?>" id="thumbnail-preview" alt="Placeholder" />
                            <?php } ?>

                            <div class="wrapper">
                                <label for="thumbnail" class="file-upload">Odaberite sliku</label>
                                <label class="delete-file-upload">Obrišite sliku</label>
                            </div>
                            <!-- prikaži grešku za sliku ukoliko postoji !-->
                            <span><?php if(isset($_slikaerror)) { echo $_slikaerror; } ?></span>
                        </div>

                        <input id="newthumbnail_uploaded" type="hidden" name="new_thumbnail" value="">

                        <div class="buttonwrapper">

                            <a href="../index.php"><button type="button" class="header-button">Odustani</button></a>

                            <button type="submit" class="header-button new-article"><i class="las la-pen fa-fw"></i> Uredi članak</button>

                            <a class="delete-article" href="obrisi_clanak.php?id=<?=$_GET['id'];?>"><button type="button" class="header-button delete-article"><i class="las la-trash"></i> Obriši članak</button></a>

                        </div>
                        
                    </div>
                    
                </form>

            </section>

        <?php } ?>
        
    </body>

    <footer>

        <script type="text/javascript">

            const fileUpload = document.getElementById("thumbnail");
            const fileUploadLabel = document.querySelector(".delete-file-upload");
            const thumbPreview = document.querySelector("#thumbnail-preview");
            const newthumbnail = document.querySelector("#newthumbnail_uploaded");

            fileUpload.addEventListener("change", (event) => {
                var file = fileUpload.files[0];
                var src = URL.createObjectURL(file);
                thumbPreview.src = src;
                fileUploadLabel.style.display = "block";
                newthumbnail.value = "1"; // 1 = novi thumbnail, 2 = obrisan thumbnail
            });

            fileUploadLabel.addEventListener("click", (event) => {
                fileUpload.val = "";
                thumbPreview.src = "../images/article_placeholder.png";
                fileUploadLabel.style.display = "none";
                newthumbnail.value = "2"; // 1 = novi thumbnail, 2 = obrisan thumbnail
            });

            <?php if(!empty($_article_data['article_thumbnailName'])) { ?>                    
                fileUploadLabel.style.display = "block";
            <?php } ?>

        </script>

    </footer>

</html>