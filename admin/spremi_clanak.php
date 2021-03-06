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

    // forma poslana
    if($_SERVER['REQUEST_METHOD'] == "POST")
    {
        $_nasloverror = $_sadrzajerror = $_slikaerror = $_datumerror = ''; // isprazni varijable

        if(empty($_POST['title'])) { $_nasloverror = '<p style="color: red;">* obavezno</p>'; } // nije unešen naslov
        if(empty($_POST['content'])) { $_sadrzajerror = '<p style="color: red;">* obavezno</p>'; } // nije unešen sadržaj

        if($_POST['publish_date'] < date("Y-m-d")) { $_datumerror = '<p style="color: red;">* nevažeći datum</p>'; } // unešen je datum manji od današnjeg

        // ukoliko je uploadan thumbnail - provjeri format
        if($_FILES['thumbnail']['error'] != 4)
        {
            $_allowed = array("image/png", "image/jpg", "image/jpeg");
            $_uploaded = $_FILES['thumbnail']['type'];

            if(!in_array($_uploaded, $_allowed)) { $_slikaerror = '<p style="color: red;">* nedozvoljen format slike</p>'; }
        }

        // ako nema grešaka za naslov, sadržaj i sliku
        if(empty($_nasloverror) && empty($_sadrzajerror) && empty($_slikaerror) && empty($_datumerror))
        {
            $_naslov = $_POST['title'];
            $_sadrzaj = $_POST['content'];
            $_sazetak = $_POST['summary'];
            $_datum = $_POST['publish_date'];

            // ukoliko je uploadovan thumbnail - spremi ga na server pod novim imenom
            if($_FILES['thumbnail']['error'] != 4)
            {
                $_upload_directory = "/images/uploads/";
                $_temp = explode(".", $_FILES['thumbnail']['name']);
                $_filename = bin2hex(str_shuffle($_FILES['thumbnail']['tmp_name']));
                $_filename = (strlen($_filename) < 24) ? str_pad($_filename, 24, random_bytes(8)) : substr($_filename, 0, 24);
                $_filename = $_filename . '.' . end($_temp);
                move_uploaded_file($_FILES['thumbnail']['tmp_name'], dirname(__DIR__) . $_upload_directory . $_filename);
            }

            $sql = "INSERT INTO clanci (naslov, sazetak, sadrzaj, datumObjavljivanja, thumbnailNaziv) VALUES (?, ?, ?, ?, ?)";
            if($stmt = mysqli_prepare($connection, $sql))
            {
                mysqli_stmt_bind_param($stmt, "sssss", $_naslov, $_sazetak, $_sadrzaj, $_datum, $_filename);
                if(mysqli_stmt_execute($stmt))
                {
                    header("Location: ../index.php");
                    $_SESSION['successful_create'] = true;
                }
                else
                {
                    echo mysqli_error($connection);
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
        <title>Novi članak - Portal</title>
    </head>
    <body>

        <?php include (dirname(__DIR__)) . "/includes/header.php"; ?>

        <section id="newarticle_page">

            <h4>Novi članak</h4>

            <form action="spremi_clanak.php" id="new_article" method="POST" enctype="multipart/form-data">

                <div class="column">

                    <div class="inputwrapper">
                        <label for="title">Naslov članka <span style="color: red;">*</span></label>
                        <input type="text" id="title" name="title">
                        <span><?php if(isset($_nasloverror)) { echo $_nasloverror; } ?></span>
                    </div>

                    <div class="inputwrapper">
                        <label for="content">Sadržaj <span style="color: red;">*</span></label>
                        <textarea id="content" name="content"></textarea>
                        <span><?php if(isset($_sadrzajerror)) { echo $_sadrzajerror; } ?></span>
                    </div>

                    <div class="inputwrapper">
                        <label for="summary">Sažetak</label>
                        <textarea id="summary" name="summary"></textarea>
                    </div>

                </div>

                <div class="column">

                    <div class="inputwrapper">
                        <label for="publish_date">Datum objavljivanja <span style="color: red;">*</span></label>
                        <input type="date" id="publish_date" name="publish_date" value="<?php echo date("Y-m-d"); ?>" min="<?php echo date("Y-m-d"); ?>">
                        <span><?php if(isset($_datumerror)) { echo $_datumerror; } ?></span>
                    </div>

                    <div class="inputwrapper">
                        <label>Istaknuta fotografija</label>
                        <input type="file" accept="image/*" name="thumbnail" id="thumbnail" style="display: none;">
                        <img src="../images/article_placeholder.png" id="thumbnail-preview" alt="Placeholder" />
                        <div class="wrapper">
                            <label for="thumbnail" class="file-upload">Odaberite sliku</label>
                            <label class="delete-file-upload">Obrišite sliku</label>
                        </div>
                        <span><?php if(isset($_slikaerror)) { echo $_slikaerror; } ?></span>
                    </div>

                    <div class="buttonwrapper">

                        <button type="button" class="header-button">Odustani</button>

                        <button type="submit" class="header-button new-article"><i class="las la-pen fa-fw"></i> Objavi članak</button>

                    </div>
                    
                </div>
                
            </form>

        </section>
        
    </body>

    <footer>

        <script type="text/javascript">

            const fileUpload = document.getElementById("thumbnail");
            const fileUploadLabel = document.querySelector(".delete-file-upload");
            const thumbPreview = document.querySelector("#thumbnail-preview");

            fileUpload.addEventListener("change", (event) => {
                var file = fileUpload.files[0];
                var src = URL.createObjectURL(file);
                thumbPreview.src = src;
                fileUploadLabel.style.display = "block";
            });

            fileUploadLabel.addEventListener("click", (event) => {
                fileUpload.val = "";
                thumbPreview.src = "../images/article_placeholder.png";
                fileUploadLabel.style.display = "none";
            });

        </script>

    </footer>
</html>