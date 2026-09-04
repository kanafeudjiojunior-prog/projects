-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le : ven. 04 sep. 2026 à 19:04
-- Version du serveur : 8.4.7
-- Version de PHP : 8.3.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `school_1`
--

-- --------------------------------------------------------

--
-- Structure de la table `deparment`
--

DROP TABLE IF EXISTS `deparment`;
CREATE TABLE IF NOT EXISTS `deparment` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `deparment`
--

INSERT INTO `deparment` (`id`, `name`, `description`) VALUES
(15, 'Education', 'description '),
(16, 'Computer Engineering', 'description'),
(17, 'Communication', 'description'),
(18, 'Management', 'description'),
(19, 'Business and Finance', 'description'),
(20, 'Civil Engineering', 'description'),
(21, 'Agricultural and Foo', 'description'),
(22, 'Arts and cultural', 'description'),
(23, 'Legal Department', 'description ');

-- --------------------------------------------------------

--
-- Structure de la table `enrollment`
--

DROP TABLE IF EXISTS `enrollment`;
CREATE TABLE IF NOT EXISTS `enrollment` (
  `id` int NOT NULL AUTO_INCREMENT,
  `academic_year` varchar(15) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '2025-2026',
  `student_id` int NOT NULL,
  `speciality_id` int NOT NULL,
  `level_id` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `level_id` (`level_id`),
  KEY `speciality_id` (`speciality_id`),
  KEY `student_id` (`student_id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `enrollment`
--

INSERT INTO `enrollment` (`id`, `academic_year`, `student_id`, `speciality_id`, `level_id`) VALUES
(3, '2025-2026', 1, 14, 1),
(4, '2025-2026', 2, 16, 2),
(10, '2025-2026', 5, 15, 4),
(11, '2025-2026', 9, 14, 4);

-- --------------------------------------------------------

--
-- Structure de la table `level`
--

DROP TABLE IF EXISTS `level`;
CREATE TABLE IF NOT EXISTS `level` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `level`
--

INSERT INTO `level` (`id`, `name`) VALUES
(1, 'level 1'),
(2, 'level 2'),
(3, 'level 3'),
(4, 'level 4');

-- --------------------------------------------------------

--
-- Structure de la table `speciality`
--

DROP TABLE IF EXISTS `speciality`;
CREATE TABLE IF NOT EXISTS `speciality` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `departement_id` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `departement_id` (`departement_id`)
) ENGINE=InnoDB AUTO_INCREMENT=32 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `speciality`
--

INSERT INTO `speciality` (`id`, `name`, `description`, `departement_id`) VALUES
(14, 'software engineering', 'description ', 16),
(15, 'E-Commerce and Digit', 'description', 16),
(16, 'Teaching', 'description', 15),
(17, 'Education Management', 'description', 15),
(18, 'journalism', 'description', 17),
(19, 'Media photography', 'description', 17),
(20, 'assistant manager', 'description', 18),
(21, 'management', 'description', 18),
(22, 'Accouting', 'description', 19),
(23, 'Banking and finance', 'description', 19),
(24, 'urban planning', 'description', 20),
(25, 'public works', 'description', 20),
(26, 'food technology', 'description', 21),
(27, 'animal production te', 'description', 21),
(28, 'cinematography', 'description', 22),
(29, 'graphic design', 'description', 22),
(30, 'legal assistant', 'description', 23),
(31, 'business law', 'description', 23);

-- --------------------------------------------------------

--
-- Structure de la table `student`
--

DROP TABLE IF EXISTS `student`;
CREATE TABLE IF NOT EXISTS `student` (
  `id` int NOT NULL AUTO_INCREMENT,
  `firstName` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `lastName` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `birth` date NOT NULL,
  `media` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `student`
--

INSERT INTO `student` (`id`, `firstName`, `lastName`, `birth`, `media`) VALUES
(1, 'kana', 'junior', '2020-07-20', 'media/1787733521_img1.jpg'),
(2, 'loveline', 'indi', '2016-07-21', 'media/1787733505_img2.jpg'),
(5, 'veran', 'michel', '5002-02-05', 'media/1787733477_img7.jpg'),
(8, 'Kana', 'junior', '0001-01-01', 'media/1787738641_img6.jpg'),
(9, 'KFJ', '237', '0001-01-01', 'media/1788520927_LOGO-KFJ237.png');

-- --------------------------------------------------------

--
-- Structure de la table `user`
--

DROP TABLE IF EXISTS `user`;
CREATE TABLE IF NOT EXISTS `user` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `pass` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `user`
--

INSERT INTO `user` (`id`, `name`, `pass`) VALUES
(1, 'admin', 'admin'),
(2, 'kana', '1234');

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `enrollment`
--
ALTER TABLE `enrollment`
  ADD CONSTRAINT `enrollment_ibfk_1` FOREIGN KEY (`level_id`) REFERENCES `level` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `enrollment_ibfk_2` FOREIGN KEY (`speciality_id`) REFERENCES `speciality` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `enrollment_ibfk_3` FOREIGN KEY (`student_id`) REFERENCES `student` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `speciality`
--
ALTER TABLE `speciality`
  ADD CONSTRAINT `speciality_ibfk_1` FOREIGN KEY (`departement_id`) REFERENCES `deparment` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
