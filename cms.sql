CREATE TABLE korisnici
                    (user_id int NOT NULL AUTO_INCREMENT PRIMARY KEY,
                    email varchar(64) NOT NULL,
                    lozinka varchar(255) NOT NULL,
                    isAdmin int NOT NULL DEFAULT "0");

CREATE TABLE clanci
                    (article_id int NOT NULL AUTO_INCREMENT PRIMARY KEY,
                    article_naslov varchar(255) NOT NULL,
                    article_sazetak varchar(192) NOT NULL,
                    article_tekst text NOT NULL,
                    article_datumObjavljivanja date NOT NULL,
                    article_thumbnailName varchar(32));