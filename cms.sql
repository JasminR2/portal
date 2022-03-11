CREATE TABLE korisnici
                    (id int NOT NULL AUTO_INCREMENT PRIMARY KEY,
                    email varchar(64) NOT NULL,
                    lozinka varchar(255) NOT NULL,
                    isAdmin int NOT NULL DEFAULT "0");

CREATE TABLE clanci
                    (id int NOT NULL AUTO_INCREMENT PRIMARY KEY,
                    naslov varchar(255) NOT NULL,
                    sazetak varchar(192) NOT NULL,
                    sadrzaj text NOT NULL,
                    datumObjavljivanja date NOT NULL,
                    thumbnailNaziv varchar(32),
                    FULLTEXT(naslov, sazetak, sadrzaj));