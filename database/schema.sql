-- Datenbankschema des Tippspiels (aus einem phpMyAdmin-Export, personenbezogene Daten entfernt).
-- Enthaelt Tabellenstruktur, Indizes, AUTO_INCREMENT, die Turnierstammdaten (Gruppen, Teams,
-- Spielplan WM 2026 aus der livescore-api) sowie die Grundeinstellungen (set_setting) mit Platzhaltern.

-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Server-Version: 10.4.32-MariaDB
-- PHP-Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Datenbank: `tippspiel_demo` (Beispielname, siehe .env)
--

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `com_comment`
--

CREATE TABLE `com_comment` (
  `com_index` int(11) NOT NULL,
  `com_usr_index` int(11) NOT NULL,
  `com_timestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  `com_text` text NOT NULL,
  `com_color` int(11) NOT NULL,
  `com_media` varchar(255) NOT NULL,
  `com_com_index` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;


-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `crl_cronlog`
--

CREATE TABLE `crl_cronlog` (
  `crl_index` int(11) NOT NULL,
  `crl_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `crl_level` varchar(10) NOT NULL DEFAULT 'INFO',
  `crl_message` varchar(1000) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `grp_group`
--

CREATE TABLE `grp_group` (
  `grp_index` int(11) NOT NULL,
  `grp_name` varchar(255) NOT NULL,
  `grp_short` varchar(20) NOT NULL,
  `grp_vorrunde` int(11) NOT NULL,
  `grp_anzahlteams` int(11) NOT NULL,
  `grp_order` int(11) NOT NULL,
  `grp_active` int(2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Daten für Tabelle `grp_group`
--

INSERT INTO `grp_group` (`grp_index`, `grp_name`, `grp_short`, `grp_vorrunde`, `grp_anzahlteams`, `grp_order`, `grp_active`) VALUES
(1, 'Gruppe A', 'A', 1, 0, 1, 1),
(2, 'Gruppe B', 'B', 1, 0, 2, 1),
(3, 'Gruppe C', 'C', 1, 0, 3, 1),
(4, 'Gruppe D', 'D', 1, 0, 4, 1),
(5, 'Gruppe E', 'E', 1, 0, 5, 1),
(6, 'Gruppe F', 'F', 1, 0, 6, 1),
(7, 'Gruppe G', 'G', 1, 0, 7, 1),
(8, 'Gruppe H', 'H', 1, 0, 8, 1),
(9, 'Gruppe I', 'I', 1, 0, 9, 1),
(10, 'Gruppe J', 'J', 1, 0, 10, 1),
(11, 'Gruppe K', 'K', 1, 0, 11, 1),
(12, 'Gruppe L', 'L', 1, 0, 12, 1),
(13, 'Sechzentelfinale', 'SF', 0, 32, 13, 1),
(14, 'Achtelfinale', 'AF', 0, 16, 14, 1),
(15, 'Viertelfinale', 'VF', 0, 8, 15, 1),
(16, 'Halbfinale', 'HF', 0, 4, 16, 1),
(17, 'Spiel um Platz 3', 'S3', 0, 0, 17, 1),
(18, 'Finale', 'F', 0, 2, 18, 1);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `gti_grouptipp`
--

CREATE TABLE `gti_grouptipp` (
  `gti_index` int(11) NOT NULL,
  `gti_usr_index` int(11) NOT NULL,
  `gti_grp_index` int(11) NOT NULL,
  `gti_tea_index` int(11) NOT NULL,
  `gti_points` int(11) NOT NULL DEFAULT 0,
  `gti_evaluated` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `mat_match`
--

CREATE TABLE `mat_match` (
  `mat_index` int(11) NOT NULL,
  `mat_grp_index` int(11) NOT NULL DEFAULT 0,
  `mat_tea_index_home` int(11) NOT NULL DEFAULT 0,
  `mat_tea_index_away` int(11) NOT NULL DEFAULT 0,
  `mat_result_home` int(11) NOT NULL DEFAULT 0,
  `mat_result_away` int(11) NOT NULL DEFAULT 0,
  `mat_result_string` varchar(20) NOT NULL,
  `mat_starttime` datetime NOT NULL,
  `mat_status` int(11) NOT NULL DEFAULT 0,
  `mat_data_index` int(11) NOT NULL DEFAULT 0
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Daten für Tabelle `mat_match`
--

INSERT INTO `mat_match` (`mat_index`, `mat_grp_index`, `mat_tea_index_home`, `mat_tea_index_away`, `mat_result_home`, `mat_result_away`, `mat_result_string`, `mat_starttime`, `mat_status`, `mat_data_index`) VALUES
(1, 1, 2, 21, 0, 0, '', '2026-06-11 21:00:00', 1, 1825339),
(2, 1, 9, 37, 0, 0, '', '2026-06-12 04:00:00', 0, 1850052),
(3, 2, 1, 35, 0, 0, '', '2026-06-12 21:00:00', 0, 1850055),
(4, 4, 3, 30, 0, 0, '', '2026-06-13 03:00:00', 0, 1825340),
(5, 2, 10, 47, 0, 0, '', '2026-06-13 21:00:00', 0, 1825343),
(6, 3, 27, 19, 0, 0, '', '2026-06-14 00:00:00', 0, 1825341),
(7, 3, 24, 44, 0, 0, '', '2026-06-14 03:00:00', 0, 1825342),
(8, 4, 4, 48, 0, 0, '', '2026-06-14 06:00:00', 0, 1850058),
(9, 5, 40, 23, 0, 0, '', '2026-06-14 19:00:00', 0, 1825344),
(10, 6, 41, 7, 0, 0, '', '2026-06-14 22:00:00', 0, 1825346),
(11, 5, 16, 29, 0, 0, '', '2026-06-15 01:00:00', 0, 1825345),
(12, 6, 46, 22, 0, 0, '', '2026-06-15 04:00:00', 0, 1850061),
(13, 8, 45, 14, 0, 0, '', '2026-06-15 18:00:00', 0, 1825350),
(14, 7, 34, 17, 0, 0, '', '2026-06-15 21:00:00', 0, 1825347),
(15, 8, 11, 31, 0, 0, '', '2026-06-16 00:00:00', 0, 1825349),
(16, 7, 6, 32, 0, 0, '', '2026-06-16 03:00:00', 0, 1825348),
(17, 9, 39, 20, 0, 0, '', '2026-06-16 21:00:00', 0, 1825353),
(18, 9, 5, 42, 0, 0, '', '2026-06-17 00:00:00', 0, 1850064),
(19, 10, 26, 13, 0, 0, '', '2026-06-17 03:00:00', 0, 1825351),
(20, 10, 33, 8, 0, 0, '', '2026-06-17 06:00:00', 0, 1825352),
(21, 11, 43, 15, 0, 0, '', '2026-06-17 19:00:00', 0, 1850067),
(22, 12, 38, 36, 0, 0, '', '2026-06-17 22:00:00', 0, 1825354),
(23, 12, 18, 25, 0, 0, '', '2026-06-18 01:00:00', 0, 1825355),
(24, 11, 12, 28, 0, 0, '', '2026-06-18 04:00:00', 0, 1825356),
(25, 1, 37, 21, 0, 0, '', '2026-06-18 18:00:00', 0, 1850053),
(26, 2, 47, 35, 0, 0, '', '2026-06-18 21:00:00', 0, 1850056),
(27, 2, 1, 10, 0, 0, '', '2026-06-19 00:00:00', 0, 1825357),
(28, 1, 2, 9, 0, 0, '', '2026-06-19 03:00:00', 0, 1825358),
(29, 4, 3, 4, 0, 0, '', '2026-06-19 21:00:00', 0, 1825361),
(30, 3, 44, 19, 0, 0, '', '2026-06-20 00:00:00', 0, 1825360),
(31, 3, 27, 24, 0, 0, '', '2026-06-20 03:00:00', 0, 1825359),
(32, 4, 48, 30, 0, 0, '', '2026-06-20 06:00:00', 0, 1850059),
(33, 6, 41, 46, 0, 0, '', '2026-06-20 19:00:00', 0, 1850062),
(34, 5, 40, 16, 0, 0, '', '2026-06-20 22:00:00', 0, 1825363),
(35, 5, 29, 23, 0, 0, '', '2026-06-21 02:00:00', 0, 1825362),
(36, 6, 22, 7, 0, 0, '', '2026-06-21 06:00:00', 0, 1825364),
(37, 8, 45, 11, 0, 0, '', '2026-06-21 18:00:00', 0, 1825367),
(38, 7, 34, 6, 0, 0, '', '2026-06-21 21:00:00', 0, 1825365),
(39, 8, 31, 14, 0, 0, '', '2026-06-22 00:00:00', 0, 1825368),
(40, 7, 32, 17, 0, 0, '', '2026-06-22 03:00:00', 0, 1825366),
(41, 10, 26, 33, 0, 0, '', '2026-06-22 19:00:00', 0, 1825369),
(42, 9, 39, 5, 0, 0, '', '2026-06-22 23:00:00', 0, 1850065),
(43, 9, 42, 20, 0, 0, '', '2026-06-23 02:00:00', 0, 1825371),
(44, 10, 8, 13, 0, 0, '', '2026-06-23 05:00:00', 0, 1825370),
(45, 11, 43, 12, 0, 0, '', '2026-06-23 19:00:00', 0, 1850068),
(46, 12, 38, 18, 0, 0, '', '2026-06-23 22:00:00', 0, 1825372),
(47, 12, 25, 36, 0, 0, '', '2026-06-24 01:00:00', 0, 1825373),
(48, 11, 28, 15, 0, 0, '', '2026-06-24 04:00:00', 0, 1852069),
(49, 2, 47, 1, 0, 0, '', '2026-06-24 21:00:00', 0, 1825378),
(50, 2, 35, 10, 0, 0, '', '2026-06-24 21:00:00', 0, 1850057),
(51, 3, 44, 27, 0, 0, '', '2026-06-25 00:00:00', 0, 1825376),
(52, 3, 19, 24, 0, 0, '', '2026-06-25 00:00:00', 0, 1825375),
(53, 1, 37, 2, 0, 0, '', '2026-06-25 03:00:00', 0, 1850054),
(54, 1, 21, 9, 0, 0, '', '2026-06-25 03:00:00', 0, 1825377),
(55, 5, 23, 16, 0, 0, '', '2026-06-25 22:00:00', 0, 1825379),
(56, 5, 29, 40, 0, 0, '', '2026-06-25 22:00:00', 0, 1825380),
(57, 6, 7, 46, 0, 0, '', '2026-06-26 01:00:00', 0, 1850063),
(58, 6, 22, 41, 0, 0, '', '2026-06-26 01:00:00', 0, 1825382),
(59, 4, 48, 3, 0, 0, '', '2026-06-26 04:00:00', 0, 1850060),
(60, 4, 30, 4, 0, 0, '', '2026-06-26 04:00:00', 0, 1825381),
(61, 9, 42, 39, 0, 0, '', '2026-06-26 21:00:00', 0, 1825386),
(62, 9, 20, 5, 0, 0, '', '2026-06-26 21:00:00', 0, 1850066),
(63, 8, 14, 11, 0, 0, '', '2026-06-27 02:00:00', 0, 1825383),
(64, 8, 31, 45, 0, 0, '', '2026-06-27 02:00:00', 0, 1825387),
(65, 7, 17, 6, 0, 0, '', '2026-06-27 05:00:00', 0, 1825384),
(66, 7, 32, 34, 0, 0, '', '2026-06-27 05:00:00', 0, 1825385),
(67, 12, 25, 38, 0, 0, '', '2026-06-27 23:00:00', 0, 1825392),
(68, 12, 36, 18, 0, 0, '', '2026-06-27 23:00:00', 0, 1825390),
(69, 11, 28, 43, 0, 0, '', '2026-06-28 01:30:00', 0, 1850069),
(70, 11, 15, 12, 0, 0, '', '2026-06-28 01:30:00', 0, 0),
(71, 10, 13, 33, 0, 0, '', '2026-06-28 04:00:00', 0, 1825388),
(72, 10, 8, 26, 0, 0, '', '2026-06-28 04:00:00', 0, 1825391),
(73, 13, 4, 1, 0, 0, '', '2026-06-28 21:00:00', 0, 0);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `set_setting`
--

CREATE TABLE `set_setting` (
  `set_index` int(11) NOT NULL,
  `set_key` varchar(255) NOT NULL,
  `set_value` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Daten für Tabelle `set_setting`
--

INSERT INTO `set_setting` (`set_index`, `set_key`, `set_value`) VALUES
(2, 'email', 'admin@example.com'),
(5, 'anzahl_gruppen', '12'),
(6, 'pokal', 'Weltmeister'),
(7, 'punkte_richtiges_erg', '3'),
(8, 'punkte_richtige_ten', '2'),
(9, 'punkte_richtiges_erg_er', '3'),
(10, 'punkte_richtiger_gti', '2'),
(11, 'punkte_richtiger_sti', '5'),
(12, 'punkte_richtiger_aus', '1'),
(13, 'usr_vergessen', '0'),
(14, 'live_data_aktiv', '0'),
(15, 'api_key', 'IHR_LIVESCORE_API_KEY'),
(16, 'api_secret', 'IHR_LIVESCORE_API_SECRET'),
(17, 'impressum', '[Impressum des Betreibers - ueber Admin > Settings pflegen]'),
(19, 'datenschutz', '[Datenschutzerklaerung - ueber Admin > Settings pflegen]');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `sti_spezialtipp`
--

CREATE TABLE `sti_spezialtipp` (
  `sti_index` int(11) NOT NULL,
  `sti_usr_index` int(11) NOT NULL,
  `sti_meister` int(11) NOT NULL,
  `sti_koenig` varchar(255) NOT NULL,
  `sti_points` int(11) NOT NULL DEFAULT 0,
  `sti_evaluated` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;


-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `tea_team`
--

CREATE TABLE `tea_team` (
  `tea_index` int(11) NOT NULL,
  `tea_name` varchar(255) NOT NULL,
  `tea_short` varchar(6) NOT NULL,
  `tea_grp_index` int(11) NOT NULL,
  `tea_data_index` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Daten für Tabelle `tea_team`
--

INSERT INTO `tea_team` (`tea_index`, `tea_name`, `tea_short`, `tea_grp_index`, `tea_data_index`) VALUES
(1, 'Canada', 'can', 2, 1674),
(2, 'Mexico', 'mex', 1, 1450),
(3, 'USA', 'usa', 4, 1849),
(4, 'Australien', 'aus', 4, 1440),
(5, 'Iraq', 'irq', 9, 1715),
(6, 'Iran', 'irn', 7, 1436),
(7, 'Japan', 'jpn', 6, 1458),
(8, 'Jordan', 'jrd', 10, 1790),
(9, 'S&uuml;dkorea', 'sko', 1, 1452),
(10, 'Qatar', 'qat', 2, 1427),
(11, 'VerArabEmirate', 'uae', 8, 1432),
(12, 'Uzbekistan', 'uzb', 11, 1776),
(13, 'Algeria', 'alg', 10, 1528),
(14, 'KapVerde', 'cpv', 8, 1608),
(15, 'Kongo', 'cng', 11, 2752),
(16, 'Elefenbeink&uuml;ste', 'civ', 5, 1628),
(17, 'Egypt', 'egy', 7, 215),
(18, 'Ghana', 'gha', 12, 214),
(19, 'Marokko', 'mar', 3, 1435),
(20, 'Senegal', 'sen', 9, 1460),
(21, 'S&uuml;dAfrika', 'saf', 1, 2767),
(22, 'Tunisia', 'tun', 6, 1455),
(23, 'Curacao', 'cur', 5, 2732),
(24, 'Haiti', 'hai', 3, 2777),
(25, 'Panama', 'pan', 12, 1454),
(26, 'Argentinen', 'arg', 10, 1443),
(27, 'Brazilien', 'bra', 3, 1448),
(28, 'Kolumbien', 'kol', 11, 1457),
(29, 'Ecuador', 'ecu', 5, 1847),
(30, 'Paraguay', 'par', 4, 4040),
(31, 'Uruguay', 'ury', 8, 1434),
(32, 'Neuseeland', 'nzl', 7, 5345),
(33, '&Ouml;sterreich', 'aut', 10, 2684),
(34, 'Belgien', 'bel', 7, 1453),
(35, 'Bosnien', 'bos', 2, 2686),
(36, 'Kroatien', 'hrv', 12, 211),
(37, 'Tschechien', 'cze', 1, 1722),
(38, 'England', 'eng', 12, 1456),
(39, 'Frankreich', 'fra', 9, 1439),
(40, 'Deutschland', 'ger', 5, 1449),
(41, 'Niederlande', 'ned', 6, 1649),
(42, 'Norwegen', 'nor', 9, 2677),
(43, 'Portugal', 'por', 11, 1437),
(44, 'Schottland', 'sco', 3, 1741),
(45, 'Spanien', 'esp', 8, 1438),
(46, 'Schweden', 'swe', 6, 1451),
(47, 'Schweiz', 'sui', 2, 208),
(48, 'T&uuml;rkei', 'tur', 4, 1744);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `tim_timestamp`
--

CREATE TABLE `tim_timestamp` (
  `tim_index` int(11) NOT NULL,
  `tim_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `tim_info` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `tip_tipp`
--

CREATE TABLE `tip_tipp` (
  `tip_index` int(11) NOT NULL,
  `tip_mat_index` int(11) NOT NULL,
  `tip_usr_index` int(11) NOT NULL,
  `tip_result_home` int(11) NOT NULL,
  `tip_result_away` int(11) NOT NULL,
  `tip_timestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  `tip_points_erg` int(11) NOT NULL DEFAULT 0,
  `tip_points_ten` int(11) NOT NULL DEFAULT 0,
  `tip_points_aus` int(11) NOT NULL DEFAULT 0,
  `tip_ki` tinyint(4) NOT NULL DEFAULT 0,
  `tip_evaluated` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;


-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `tir_tipprunde`
--

CREATE TABLE `tir_tipprunde` (
  `tir_index` int(11) NOT NULL,
  `tir_name` varchar(255) NOT NULL,
  `tir_einsatz` varchar(255) NOT NULL,
  `tir_usr_index` int(11) NOT NULL,
  `tir_winnercount` int(11) NOT NULL DEFAULT 0,
  `tir_scndtbl` int(11) NOT NULL DEFAULT 0,
  `tir_scndtbl_winnercount` int(11) NOT NULL DEFAULT 0,
  `tir_astrtipp` int(11) NOT NULL DEFAULT 0,
  `tir_gewinntext` text NOT NULL,
  `tir_bezahlung` text NOT NULL,
  `tir_active` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;


-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `urm_usrtirmapping`
--

CREATE TABLE `urm_usrtirmapping` (
  `urm_index` int(11) NOT NULL,
  `urm_usr_index` int(11) NOT NULL,
  `urm_tir_index` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `usr_user`
--

CREATE TABLE `usr_user` (
  `usr_index` int(11) NOT NULL,
  `usr_name` varchar(50) NOT NULL,
  `usr_vornachname` varchar(255) NOT NULL,
  `usr_email` varchar(500) NOT NULL,
  `usr_password` varchar(50) NOT NULL,
  `usr_hash` varchar(1000) NOT NULL,
  `usr_locked` int(11) NOT NULL,
  `usr_loginfail` int(11) NOT NULL,
  `usr_tir_index__default` int(11) NOT NULL,
  `usr_bezahlt` int(11) NOT NULL DEFAULT 0,
  `usr_auto` int(11) NOT NULL DEFAULT 1
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;


--
-- Indizes der exportierten Tabellen
--

--
-- Indizes für die Tabelle `com_comment`
--
ALTER TABLE `com_comment`
  ADD PRIMARY KEY (`com_index`);

--
-- Indizes für die Tabelle `crl_cronlog`
--
ALTER TABLE `crl_cronlog`
  ADD PRIMARY KEY (`crl_index`),
  ADD KEY `idx_crl_date` (`crl_date`);

--
-- Indizes für die Tabelle `grp_group`
--
ALTER TABLE `grp_group`
  ADD PRIMARY KEY (`grp_index`);

--
-- Indizes für die Tabelle `gti_grouptipp`
--
ALTER TABLE `gti_grouptipp`
  ADD PRIMARY KEY (`gti_index`);

--
-- Indizes für die Tabelle `mat_match`
--
ALTER TABLE `mat_match`
  ADD PRIMARY KEY (`mat_index`);

--
-- Indizes für die Tabelle `set_setting`
--
ALTER TABLE `set_setting`
  ADD PRIMARY KEY (`set_index`);

--
-- Indizes für die Tabelle `sti_spezialtipp`
--
ALTER TABLE `sti_spezialtipp`
  ADD PRIMARY KEY (`sti_index`);

--
-- Indizes für die Tabelle `tea_team`
--
ALTER TABLE `tea_team`
  ADD PRIMARY KEY (`tea_index`);

--
-- Indizes für die Tabelle `tim_timestamp`
--
ALTER TABLE `tim_timestamp`
  ADD PRIMARY KEY (`tim_index`);

--
-- Indizes für die Tabelle `tip_tipp`
--
ALTER TABLE `tip_tipp`
  ADD PRIMARY KEY (`tip_index`);

--
-- Indizes für die Tabelle `tir_tipprunde`
--
ALTER TABLE `tir_tipprunde`
  ADD PRIMARY KEY (`tir_index`);

--
-- Indizes für die Tabelle `urm_usrtirmapping`
--
ALTER TABLE `urm_usrtirmapping`
  ADD PRIMARY KEY (`urm_index`),
  ADD UNIQUE KEY `uq_urm_usr_tir` (`urm_usr_index`,`urm_tir_index`);

--
-- Indizes für die Tabelle `usr_user`
--
ALTER TABLE `usr_user`
  ADD PRIMARY KEY (`usr_index`);

--
-- AUTO_INCREMENT für exportierte Tabellen
--

--
-- AUTO_INCREMENT für Tabelle `com_comment`
--
ALTER TABLE `com_comment`
  MODIFY `com_index` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT für Tabelle `crl_cronlog`
--
ALTER TABLE `crl_cronlog`
  MODIFY `crl_index` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT für Tabelle `grp_group`
--
ALTER TABLE `grp_group`
  MODIFY `grp_index` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT für Tabelle `gti_grouptipp`
--
ALTER TABLE `gti_grouptipp`
  MODIFY `gti_index` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `mat_match`
--
ALTER TABLE `mat_match`
  MODIFY `mat_index` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=74;

--
-- AUTO_INCREMENT für Tabelle `set_setting`
--
ALTER TABLE `set_setting`
  MODIFY `set_index` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT für Tabelle `sti_spezialtipp`
--
ALTER TABLE `sti_spezialtipp`
  MODIFY `sti_index` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT für Tabelle `tea_team`
--
ALTER TABLE `tea_team`
  MODIFY `tea_index` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=49;

--
-- AUTO_INCREMENT für Tabelle `tim_timestamp`
--
ALTER TABLE `tim_timestamp`
  MODIFY `tim_index` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `tip_tipp`
--
ALTER TABLE `tip_tipp`
  MODIFY `tip_index` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT für Tabelle `tir_tipprunde`
--
ALTER TABLE `tir_tipprunde`
  MODIFY `tir_index` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT für Tabelle `urm_usrtirmapping`
--
ALTER TABLE `urm_usrtirmapping`
  MODIFY `urm_index` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT für Tabelle `usr_user`
--
ALTER TABLE `usr_user`
  MODIFY `usr_index` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
