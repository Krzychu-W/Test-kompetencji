-- phpMyAdmin SQL Dump
-- version 5.0.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Czas generowania: 30 Sty 2022, 13:11
-- Wersja serwera: 5.7.33-36
-- Wersja PHP: 7.4.19

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Baza danych: `03841624_test`
--

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `group`
--

CREATE TABLE `group` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(128) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `parent_id` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `deep` tinyint(4) NOT NULL DEFAULT '0',
  `hierarchy` varchar(255) CHARACTER SET ascii NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Zrzut danych tabeli `group`
--

INSERT INTO `group` (`id`, `name`, `parent_id`, `deep`, `hierarchy`) VALUES
(1, 'A1', 2, 2, '00002-00001'),
(2, 'A2', 0, 1, '00002'),
(3, 'A1a', 0, 1, '00001'),
(4, 'Materiały eksploatacyjne', 3, 2, '00001-00001'),
(5, 'Oryginalne', 4, 3, '00001-00001-00002'),
(6, 'Alternatywne', 4, 3, '00001-00001-00001'),
(7, 'Folie do faksów', 5, 4, '00001-00001-00002-00001'),
(8, 'Folie do faksów', 6, 4, '00001-00001-00001-00001'),
(9, 'Taśmy do drukarek igłowych', 6, 4, '00001-00001-00001-00002'),
(10, 'Taśmy do drukarek igłowych', 5, 4, '00001-00001-00002-00002'),
(11, 'Tusze i tonery do faksów', 6, 4, '00001-00001-00001-00003'),
(12, 'Tusze i tonery do faksów', 5, 4, '00001-00001-00002-00003'),
(13, 'Meble biurowe', 2, 2, '00002-00002'),
(14, 'Krzesła i fotele biurowe', 13, 3, '00002-00002-00002'),
(15, 'Biurka, szafy, kontenery', 13, 3, '00002-00002-00001'),
(16, 'Meble metalowe', 13, 3, '00002-00002-00003');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `product`
--

CREATE TABLE `product` (
  `id` int(10) UNSIGNED NOT NULL,
  `indeks` varchar(24) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `name` varchar(128) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `unit_id` int(10) UNSIGNED NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Zrzut danych tabeli `product`
--

INSERT INTO `product` (`id`, `indeks`, `name`, `unit_id`) VALUES
(2, 'K1', 'Płaskownik 40mm', 18),
(3, '77856036', 'Produkt przykładowy 86169899', 18),
(4, '59760887', 'Produkt przykładowy 59219682', 18),
(5, '87355239', 'Produkt przykładowy 78916210', 18),
(6, '06428297', 'Produkt przykładowy 56082258', 20),
(7, '14639739', 'Produkt przykładowy 26054995', 20),
(8, 'F5439', 'Obcęgi duże', 18),
(9, 'R003', 'Rozpuszczalnik', 23);

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `unit`
--

CREATE TABLE `unit` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(24) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `short` varchar(4) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Zrzut danych tabeli `unit`
--

INSERT INTO `unit` (`id`, `name`, `short`) VALUES
(2, 'komplet', 'kpl.'),
(9, 'metr bieżący', 'mb'),
(18, 'sztuka', 'szt.'),
(20, 'opakowanie', 'opak'),
(23, 'butelka', 'but.');

--
-- Indeksy dla zrzutów tabel
--

--
-- Indeksy dla tabeli `group`
--
ALTER TABLE `group`
  ADD PRIMARY KEY (`id`),
  ADD KEY `name` (`name`),
  ADD KEY `unit_id` (`parent_id`),
  ADD KEY `hierarchy` (`hierarchy`);

--
-- Indeksy dla tabeli `product`
--
ALTER TABLE `product`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `indeks` (`indeks`),
  ADD KEY `name` (`name`),
  ADD KEY `unit_id` (`unit_id`);

--
-- Indeksy dla tabeli `unit`
--
ALTER TABLE `unit`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- AUTO_INCREMENT dla zrzuconych tabel
--

--
-- AUTO_INCREMENT dla tabeli `group`
--
ALTER TABLE `group`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT dla tabeli `product`
--
ALTER TABLE `product`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT dla tabeli `unit`
--
ALTER TABLE `unit`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
