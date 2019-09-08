-- phpMyAdmin SQL Dump
-- version 4.5.4.1deb2ubuntu2.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Creato il: Giu 07, 2019 alle 17:11
-- Versione del server: 5.7.26-0ubuntu0.16.04.1
-- Versione PHP: 7.0.33-0ubuntu0.16.04.5

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `s261072`
--

-- --------------------------------------------------------

--
-- Struttura della tabella `members`
--

DROP TABLE IF EXISTS `members`;
CREATE TABLE `members` (
  `user` varchar(50) NOT NULL,
  `pass` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dump dei dati per la tabella `members`
--

INSERT INTO `members` (`user`, `pass`) VALUES
('u1@p.it', '$2y$10$ZuHvp24AkroIcynkzjOziu7yYbWpgrAjk0kjxfuBn0xWne1vLGhki'),
('u2@p.it', '$2y$10$hpOQefpkIw1yJmMIGGVEb.DIzX.cZSN91jncAs7ce8cjrOmDlmWZC');

-- --------------------------------------------------------

--
-- Struttura della tabella `reservations`
--

DROP TABLE IF EXISTS `reservations`;
CREATE TABLE `reservations` (
  `Row_letter` varchar(2) NOT NULL,
  `Column_number` int(11) NOT NULL,
  `Status` varchar(30) DEFAULT NULL,
  `UserInvolved` varchar(30) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dump dei dati per la tabella `reservations`
--

INSERT INTO `reservations` (`Row_letter`, `Column_number`, `Status`, `UserInvolved`) VALUES
('A', 4, 'Reserved', 'u1@p.it'),
('B', 2, 'Purchased', 'u2@p.it'),
('B', 3, 'Purchased', 'u2@p.it'),
('B', 4, 'Purchased', 'u2@p.it'),
('D', 4, 'Reserved', 'u1@p.it'),
('F', 4, 'Reserved', 'u2@p.it');

--
-- Indici per le tabelle scaricate
--

--
-- Indici per le tabelle `members`
--
ALTER TABLE `members`
  ADD PRIMARY KEY (`user`);

--
-- Indici per le tabelle `reservations`
--
ALTER TABLE `reservations`
  ADD PRIMARY KEY (`Row_letter`,`Column_number`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
