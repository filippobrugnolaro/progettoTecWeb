-- phpMyAdmin SQL Dump
-- version 5.0.4
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Creato il: Gen 07, 2022 alle 14:28
-- Versione del server: 10.4.16-MariaDB
-- Versione PHP: 7.4.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `acavalie`
--

-- --------------------------------------------------------

--
-- Struttura della tabella `data_disponibile`
--

DROP TABLE IF EXISTS `data_disponibile`;
CREATE TABLE `data_disponibile` (
  `data` date NOT NULL,
  `posti` smallint(6) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dump dei dati per la tabella `data_disponibile`
--

INSERT INTO `data_disponibile` (`data`, `posti`) VALUES
('2022-01-10', 100),
('2022-01-11', 100);

-- --------------------------------------------------------

--
-- Struttura della tabella `ingressi_entrata`
--

DROP TABLE IF EXISTS `ingressi_entrata`;
CREATE TABLE `ingressi_entrata` (
  `codice` int(11) NOT NULL,
  `utente` varchar(20) NOT NULL,
  `data` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Struttura della tabella `ingressi_lezione`
--

DROP TABLE IF EXISTS `ingressi_lezione`;
CREATE TABLE `ingressi_lezione` (
  `codice` int(11) NOT NULL,
  `utente` varchar(20) DEFAULT NULL,
  `lezione` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Struttura della tabella `lezione`
--

DROP TABLE IF EXISTS `lezione`;
CREATE TABLE `lezione` (
  `id` int(11) NOT NULL,
  `data` date NOT NULL,
  `posti` smallint(6) NOT NULL,
  `descrizione` text NOT NULL,
  `corso` varchar(20) NOT NULL,
  `istruttore` varchar(20) NOT NULL,
  `pista` smallint(6) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Struttura della tabella `moto`
--

DROP TABLE IF EXISTS `moto`;
CREATE TABLE `moto` (
  `numero` smallint(6) NOT NULL,
  `cilindrata` smallint(6) NOT NULL,
  `marca` varchar(20) NOT NULL,
  `modello` varchar(20) NOT NULL,
  `anno` year(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Struttura della tabella `noleggio`
--

DROP TABLE IF EXISTS `noleggio`;
CREATE TABLE `noleggio` (
  `codice` int(11) NOT NULL,
  `data` date NOT NULL,
  `attrezzatura` tinyint(1) NOT NULL,
  `utente` varchar(20) NOT NULL,
  `moto` smallint(6) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Struttura della tabella `pista`
--

DROP TABLE IF EXISTS `pista`;
CREATE TABLE `pista` (
  `id` smallint(6) NOT NULL,
  `lunghezza` varchar(5) NOT NULL,
  `descrizione` text NOT NULL,
  `terreno` varchar(20) NOT NULL,
  `apertura` time NOT NULL,
  `chiusura` time NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Struttura della tabella `utente`
--

DROP TABLE IF EXISTS `utente`;
CREATE TABLE `utente` (
  `cf` char(16) NOT NULL,
  `cognome` varchar(25) NOT NULL,
  `nome` varchar(25) NOT NULL,
  `nascita` date NOT NULL,
  `telefono` varchar(10) NOT NULL,
  `foto` varchar(20) DEFAULT NULL,
  `email` varchar(35) NOT NULL,
  `password` varchar(256) NOT NULL,
  `ruolo` tinyint(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dump dei dati per la tabella `utente`
--

INSERT INTO `utente` (`cf`, `cognome`, `nome`, `nascita`, `telefono`, `foto`, `email`, `password`, `ruolo`) VALUES
('CVLLSN00A04A001A', 'Cavaliere', 'Alessandro', '2000-01-04', '3477625768', NULL, 'ac41husky@gmail.com', '$2y$10$QQ9mqqv1yuebeOomsbiHoetSU5mD8BDYPTe4hCbCNxbbD2f/riaXW', 2);

--
-- Indici per le tabelle scaricate
--

--
-- Indici per le tabelle `data_disponibile`
--
ALTER TABLE `data_disponibile`
  ADD PRIMARY KEY (`data`);

--
-- Indici per le tabelle `ingressi_entrata`
--
ALTER TABLE `ingressi_entrata`
  ADD PRIMARY KEY (`codice`),
  ADD KEY `fk_ingressi_entrata` (`utente`),
  ADD KEY `ingressi_entrata_ibfk_1` (`data`);

--
-- Indici per le tabelle `ingressi_lezione`
--
ALTER TABLE `ingressi_lezione`
  ADD PRIMARY KEY (`codice`),
  ADD KEY `fk_ingressi_lezione` (`utente`),
  ADD KEY `ingressi_lezione_ibfk_1` (`lezione`);

--
-- Indici per le tabelle `lezione`
--
ALTER TABLE `lezione`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pista` (`pista`);

--
-- Indici per le tabelle `moto`
--
ALTER TABLE `moto`
  ADD PRIMARY KEY (`numero`);

--
-- Indici per le tabelle `noleggio`
--
ALTER TABLE `noleggio`
  ADD PRIMARY KEY (`codice`),
  ADD KEY `fk_noleggio` (`utente`),
  ADD KEY `noleggio_ibfk_1` (`moto`),
  ADD KEY `fk_data` (`data`);

--
-- Indici per le tabelle `pista`
--
ALTER TABLE `pista`
  ADD PRIMARY KEY (`id`);

--
-- Indici per le tabelle `utente`
--
ALTER TABLE `utente`
  ADD PRIMARY KEY (`cf`);

--
-- AUTO_INCREMENT per le tabelle scaricate
--

--
-- AUTO_INCREMENT per la tabella `ingressi_entrata`
--
ALTER TABLE `ingressi_entrata`
  MODIFY `codice` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT per la tabella `ingressi_lezione`
--
ALTER TABLE `ingressi_lezione`
  MODIFY `codice` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT per la tabella `lezione`
--
ALTER TABLE `lezione`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT per la tabella `moto`
--
ALTER TABLE `moto`
  MODIFY `numero` smallint(6) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT per la tabella `noleggio`
--
ALTER TABLE `noleggio`
  MODIFY `codice` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT per la tabella `pista`
--
ALTER TABLE `pista`
  MODIFY `id` smallint(6) NOT NULL AUTO_INCREMENT;

--
-- Limiti per le tabelle scaricate
--

--
-- Limiti per la tabella `ingressi_entrata`
--
ALTER TABLE `ingressi_entrata`
  ADD CONSTRAINT `fk_ingressi_entrata` FOREIGN KEY (`utente`) REFERENCES `utente` (`cf`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `ingressi_entrata_ibfk_1` FOREIGN KEY (`data`) REFERENCES `data_disponibile` (`data`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Limiti per la tabella `ingressi_lezione`
--
ALTER TABLE `ingressi_lezione`
  ADD CONSTRAINT `fk_ingressi_lezione` FOREIGN KEY (`utente`) REFERENCES `utente` (`cf`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `ingressi_lezione_ibfk_1` FOREIGN KEY (`lezione`) REFERENCES `lezione` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Limiti per la tabella `lezione`
--
ALTER TABLE `lezione`
  ADD CONSTRAINT `lezione_ibfk_1` FOREIGN KEY (`pista`) REFERENCES `pista` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Limiti per la tabella `noleggio`
--
ALTER TABLE `noleggio`
  ADD CONSTRAINT `fk_data` FOREIGN KEY (`data`) REFERENCES `data_disponibile` (`data`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_noleggio` FOREIGN KEY (`utente`) REFERENCES `utente` (`cf`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `noleggio_ibfk_1` FOREIGN KEY (`moto`) REFERENCES `moto` (`numero`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
