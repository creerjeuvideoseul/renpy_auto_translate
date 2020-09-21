-- phpMyAdmin SQL Dump
-- version 4.5.4.1
-- http://www.phpmyadmin.net

-- Version de PHP :  7.0.3

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données :  `renpy_translate`
--

-- --------------------------------------------------------

--
-- Structure de la table `translate_cache`
--

CREATE TABLE `translate_cache` (
  `id_tc` int(11) NOT NULL,
  `tc_source` text COLLATE utf8_unicode_ci NOT NULL,
  `tc_translate` text COLLATE utf8_unicode_ci NOT NULL,
  `tc_langage_src` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `tc_langage_target` varchar(255) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `translation_ask`
--

CREATE TABLE `translation_ask` (
  `id_ta` int(11) NOT NULL,
  `ta_mail` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `ta_list_of_file` text COLLATE utf8_unicode_ci NOT NULL,
  `ta_date` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `translation_request`
--

CREATE TABLE `translation_request` (
  `id_tr` int(11) NOT NULL,
  `tt_id` int(11) NOT NULL,
  `tr_send` text COLLATE utf8_unicode_ci NOT NULL,
  `tr_response` text COLLATE utf8_unicode_ci NOT NULL,
  `tr_error` text COLLATE utf8_unicode_ci NOT NULL,
  `tr_date` datetime NOT NULL,
  `ta_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `translation_text`
--

CREATE TABLE `translation_text` (
  `id_tt` int(11) NOT NULL,
  `tt_file` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `tt_line` int(11) NOT NULL,
  `tt_data` text COLLATE utf8_unicode_ci NOT NULL,
  `tt_translate` text COLLATE utf8_unicode_ci NOT NULL,
  `tt_etat` int(11) NOT NULL,
  `tt_case` int(11) NOT NULL,
  `tt_langue` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `ta_id` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Index pour les tables exportées
--

--
-- Index pour la table `translate_cache`
--
ALTER TABLE `translate_cache`
  ADD PRIMARY KEY (`id_tc`);

--
-- Index pour la table `translation_ask`
--
ALTER TABLE `translation_ask`
  ADD PRIMARY KEY (`id_ta`);

--
-- Index pour la table `translation_request`
--
ALTER TABLE `translation_request`
  ADD PRIMARY KEY (`id_tr`);

--
-- Index pour la table `translation_text`
--
ALTER TABLE `translation_text`
  ADD PRIMARY KEY (`id_tt`);

--
-- AUTO_INCREMENT pour les tables exportées
--

--
-- AUTO_INCREMENT pour la table `translate_cache`
--
ALTER TABLE `translate_cache`
  MODIFY `id_tc` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15275;
--
-- AUTO_INCREMENT pour la table `translation_ask`
--
ALTER TABLE `translation_ask`
  MODIFY `id_ta` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT pour la table `translation_request`
--
ALTER TABLE `translation_request`
  MODIFY `id_tr` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15317;
--
-- AUTO_INCREMENT pour la table `translation_text`
--
ALTER TABLE `translation_text`
  MODIFY `id_tt` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=359367;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
