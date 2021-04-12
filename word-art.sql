
CREATE DATABASE `word-art`
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_general_ci;

/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;

--
-- Set SQL mode
--
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;

--
-- Set character set the client will use to send SQL statements to the server
--
SET NAMES 'utf8';

--
-- Set default database
--
USE `word-art`;

--
-- Drop table `rates`
--
DROP TABLE IF EXISTS rates;

--
-- Drop table `films`
--
DROP TABLE IF EXISTS films;

--
-- Drop table `film_types`
--
DROP TABLE IF EXISTS film_types;

--
-- Drop table `loads`
--
DROP TABLE IF EXISTS loads;

--
-- Set default database
--
USE `word-art`;

--
-- Create table `loads`
--
CREATE TABLE loads (
                       id int(11) NOT NULL AUTO_INCREMENT,
                       dt datetime DEFAULT NULL,
                       info text DEFAULT NULL,
                       PRIMARY KEY (id)
)
    ENGINE = INNODB,
    AUTO_INCREMENT = 71,
    AVG_ROW_LENGTH = 4096,
    CHARACTER SET utf8mb4,
    COLLATE utf8mb4_general_ci;

--
-- Create table `film_types`
--
CREATE TABLE film_types (
                            id int(11) NOT NULL AUTO_INCREMENT,
                            url varchar(255) DEFAULT NULL,
                            name varchar(255) DEFAULT NULL,
                            PRIMARY KEY (id)
)
    ENGINE = INNODB,
    AUTO_INCREMENT = 7,
    AVG_ROW_LENGTH = 3276,
    CHARACTER SET utf8mb4,
    COLLATE utf8mb4_general_ci;

--
-- Create table `films`
--
CREATE TABLE films (
                       id int(11) NOT NULL AUTO_INCREMENT,
                       name text DEFAULT NULL,
                       year int(4) DEFAULT NULL,
                       description text DEFAULT NULL,
                       cover varchar(255) DEFAULT NULL,
                       word_art_id int(11) DEFAULT NULL,
                       PRIMARY KEY (id)
)
    ENGINE = INNODB,
    AUTO_INCREMENT = 304,
    AVG_ROW_LENGTH = 2293,
    CHARACTER SET utf8mb4,
    COLLATE utf8mb4_general_ci;

--
-- Create index `IDX_films_name` on table `films`
--
ALTER TABLE films
    ADD INDEX IDX_films_name (name (200));

--
-- Create index `IDX_films_year` on table `films`
--
ALTER TABLE films
    ADD INDEX IDX_films_year (year);

--
-- Create index `UK_films` on table `films`
--
ALTER TABLE films
    ADD UNIQUE INDEX UK_films (word_art_id);

--
-- Create table `rates`
--
CREATE TABLE rates (
                       id int(11) NOT NULL AUTO_INCREMENT,
                       load_id int(11) DEFAULT NULL,
                       film_id int(11) DEFAULT NULL,
                       film_type_id int(11) DEFAULT NULL,
                       `position` varchar(2) DEFAULT NULL,
                       calc_ball float DEFAULT NULL,
                       votes int(11) DEFAULT NULL,
                       avg_ball float DEFAULT NULL,
                       PRIMARY KEY (id)
)
    ENGINE = INNODB,
    AUTO_INCREMENT = 601,
    AVG_ROW_LENGTH = 81,
    CHARACTER SET utf8mb4,
    COLLATE utf8mb4_general_ci;

--
-- Create index `IDX_rates_avg_ball` on table `rates`
--
ALTER TABLE rates
    ADD INDEX IDX_rates_avg_ball (avg_ball);

--
-- Create index `IDX_rates_calc_ball` on table `rates`
--
ALTER TABLE rates
    ADD INDEX IDX_rates_calc_ball (calc_ball);

--
-- Create index `IDX_rates_film_id` on table `rates`
--
ALTER TABLE rates
    ADD INDEX IDX_rates_film_id (film_id);

--
-- Create index `IDX_rates_film_type_id` on table `rates`
--
ALTER TABLE rates
    ADD INDEX IDX_rates_film_type_id (film_type_id);

--
-- Create index `IDX_rates_load_id` on table `rates`
--
ALTER TABLE rates
    ADD INDEX IDX_rates_load_id (load_id);

--
-- Create index `IDX_rates_position` on table `rates`
--
ALTER TABLE rates
    ADD INDEX IDX_rates_position (`position`);

--
-- Create index `IDX_rates_votes` on table `rates`
--
ALTER TABLE rates
    ADD INDEX IDX_rates_votes (votes);

--
-- Create foreign key
--
ALTER TABLE rates
    ADD CONSTRAINT FK_rates_film_id FOREIGN KEY (film_id)
        REFERENCES films (id) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Create foreign key
--
ALTER TABLE rates
    ADD CONSTRAINT FK_rates_film_type_id FOREIGN KEY (film_type_id)
        REFERENCES film_types (id) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Create foreign key
--
ALTER TABLE rates
    ADD CONSTRAINT FK_rates_load_id FOREIGN KEY (load_id)
        REFERENCES loads (id) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Dumping data for table film_types
--
INSERT INTO film_types VALUES
(1, 'rating_top.php?limit_1=0&limit_2=%d', 'Рейтинг полнометражных фильмов'),
(3, 'rating_tv_top.php?public_list_anchor=1&limit_1=0&limit_2=%d', 'Рейтинг западных сериалов'),
(4, 'rating_tv_top.php?public_list_anchor=2&limit_1=0&limit_2=%d', 'Рейтинг японских дорам'),
(5, 'rating_tv_top.php?public_list_anchor=4&limit_1=0&limit_2=%d', 'Рейтинг корейских дорам'),
(6, 'rating_tv_top.php?public_list_anchor=3&limit_1=0&limit_2=%d', 'Рейтинг российских сериалов');

--
-- Restore previous SQL mode
--
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;

--
-- Enable foreign keys
--
/*!40014 SET FOREIGN_KEY_CHECKS = @OLD_FOREIGN_KEY_CHECKS */;