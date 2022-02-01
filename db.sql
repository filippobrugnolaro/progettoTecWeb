-- phpMyAdmin SQL Dump
-- version 4.9.5deb2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Creato il: Feb 01, 2022 alle 18:51
-- Versione del server: 10.3.32-MariaDB-0ubuntu0.20.04.1
-- Versione PHP: 7.4.3

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
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

CREATE TABLE `data_disponibile` (
  `data` date NOT NULL,
  `posti` smallint(6) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dump dei dati per la tabella `data_disponibile`
--

INSERT INTO `data_disponibile` (`data`, `posti`) VALUES
('2022-01-09', 70),
('2022-01-10', 110),
('2022-01-11', 100),
('2022-01-14', 200),
('2022-01-16', 100),
('2022-01-31', 2),
('2022-02-16', 50),
('2022-02-19', 100),
('2022-02-20', 100),
('2022-02-22', 50),
('2022-02-24', 50),
('2022-02-26', 150),
('2022-02-27', 150);

-- --------------------------------------------------------

--
-- Struttura della tabella `ingressi_entrata`
--

CREATE TABLE `ingressi_entrata` (
  `codice` int(11) NOT NULL,
  `utente` varchar(20) NOT NULL,
  `data` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dump dei dati per la tabella `ingressi_entrata`
--

INSERT INTO `ingressi_entrata` (`codice`, `utente`, `data`) VALUES
(55, 'BRGFPP00B26C111Y', '2022-02-22'),
(54, 'MMMGGG00A55G222V', '2022-02-16');

-- --------------------------------------------------------

--
-- Struttura della tabella `ingressi_lezione`
--

CREATE TABLE `ingressi_lezione` (
  `codice` int(11) NOT NULL,
  `utente` varchar(20) DEFAULT NULL,
  `lezione` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dump dei dati per la tabella `ingressi_lezione`
--

INSERT INTO `ingressi_lezione` (`codice`, `utente`, `lezione`) VALUES
(38, 'BRGFPP00B26C111Y', 11),
(39, 'BRGFPP00B26C111Y', 15),
(40, 'LDFDRE00P22A874G', 15);

-- --------------------------------------------------------

--
-- Struttura della tabella `lezione`
--

CREATE TABLE `lezione` (
  `id` int(11) NOT NULL,
  `data` date NOT NULL,
  `posti` smallint(6) NOT NULL,
  `descrizione` text NOT NULL,
  `istruttore` varchar(20) NOT NULL,
  `pista` smallint(6) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dump dei dati per la tabella `lezione`
--

INSERT INTO `lezione` (`id`, `data`, `posti`, `descrizione`, `istruttore`, `pista`) VALUES
(11, '2022-02-16', 5, 'Corso per neofiti. Si affronteranno i fondamentali della tecnica di guida: posizione sulla moto, staccata e tecnica in volo.', 'Filippo Brugnolaro', 2),
(12, '2022-02-22', 5, 'Corso per amatori. Si divider&agrave; il tracciato in settori, che verranno analizzati singolarmente per capire le traiettorie ideali da utilizzare. A fine corso si far&agrave; una piccola simulazione di gara.', 'Leonardo Gambirasio', 1),
(13, '2022-02-24', 4, 'Corso per professionisti. Si simuler&agrave; una giornata di gara nel suo complesso: prove libere, qualifiche, gara 1 e gara 2.', 'Riccardo Simionato', 1),
(15, '2022-02-27', 2, 'Corso di enduro. Focalizza l\'attenzione sulle traiettorie da usare e la posizione in sella.', 'Filippo Brugnolaro', 3),
(16, '2022-02-26', 5, 'Corso per neofiti, adatto a chi non &egrave; mai andato in moto.', 'Alessandro Cavaliere', 2);

-- --------------------------------------------------------

--
-- Struttura della tabella `messaggio`
--

CREATE TABLE `messaggio` (
  `id` int(11) NOT NULL,
  `nominativo` varchar(60) NOT NULL,
  `email` varchar(50) NOT NULL,
  `telefono` varchar(10) NOT NULL,
  `data` timestamp NOT NULL DEFAULT current_timestamp(),
  `oggetto` varchar(100) NOT NULL,
  `testo` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dump dei dati per la tabella `messaggio`
--

INSERT INTO `messaggio` (`id`, `nominativo`, `email`, `telefono`, `data`, `oggetto`, `testo`) VALUES
(7, 'Filippo Brugnolaro', 'filippo.brugnolaro.fb@gmail.com', '3459751230', '2022-02-01 17:16:26', 'Richiesta corso #15', 'Buongiorno, volevo chiedrvi se potevate per favore contattarmi per spiegarmi quale sia la difficolt&agrave; del corso in questione'),
(9, 'Igor Zawalewski', 'igor@zawalewski.com', '3455734583', '2022-02-01 17:21:44', 'Richiesta informazioni noleggio', 'Buongiorno, volevo sapere se esiste qualche tipo di copertura assicurativa durante il noleggio');

-- --------------------------------------------------------

--
-- Struttura della tabella `moto`
--

CREATE TABLE `moto` (
  `numero` smallint(6) NOT NULL,
  `cilindrata` smallint(6) NOT NULL,
  `marca` varchar(20) NOT NULL,
  `modello` varchar(20) NOT NULL,
  `anno` year(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dump dei dati per la tabella `moto`
--

INSERT INTO `moto` (`numero`, `cilindrata`, `marca`, `modello`, `anno`) VALUES
(13, 450, 'KTM', 'SXF 450', 2020),
(14, 450, 'KTM', 'SXF 450', 2020),
(16, 125, 'KTM', 'SX 125', 2020),
(17, 450, 'HONDA', 'CRF 450', 2021),
(18, 250, 'HONDA', 'CRF 250', 2022),
(19, 250, 'KAWASAKI', 'KXF 250', 2019),
(20, 450, 'KAWASAKI', 'KXF 450', 2021),
(21, 250, 'SUZUKI', 'RMZ 250', 2018),
(22, 125, 'YAMAHA', 'YZ 125', 2022),
(23, 250, 'YAMAHA', 'YZF 250', 2021),
(24, 250, 'HUSQVARNA', 'FC 250', 2022);

-- --------------------------------------------------------

--
-- Struttura della tabella `noleggio`
--

CREATE TABLE `noleggio` (
  `codice` int(11) NOT NULL,
  `data` date NOT NULL,
  `attrezzatura` tinyint(1) NOT NULL,
  `utente` varchar(20) NOT NULL,
  `moto` smallint(6) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dump dei dati per la tabella `noleggio`
--

INSERT INTO `noleggio` (`codice`, `data`, `attrezzatura`, `utente`, `moto`) VALUES
(44, '2022-02-22', 1, 'LDFDRE00P22A874G', 13),
(45, '2022-02-22', 1, 'BRGFPP00B26C111Y', 21),
(46, '2022-02-27', 0, 'BRGFPP00B26C111Y', 24),
(47, '2022-02-27', 1, 'LDFDRE00P22A874G', 20);

-- --------------------------------------------------------

--
-- Struttura della tabella `pista`
--

CREATE TABLE `pista` (
  `id` smallint(6) NOT NULL,
  `lunghezza` smallint(6) NOT NULL,
  `descrizione` text NOT NULL,
  `terreno` varchar(20) NOT NULL,
  `apertura` time NOT NULL,
  `chiusura` time NOT NULL,
  `foto` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dump dei dati per la tabella `pista`
--

INSERT INTO `pista` (`id`, `lunghezza`, `descrizione`, `terreno`, `apertura`, `chiusura`, `foto`) VALUES
(1, 1800, 'Tracciato pro, adatto solo ai piloti pi&ugrave; esperti. Si articola in lunghi panettoni, ampie curve paraboliche e salti doppi e tripli. Viene fresato raramente per simulare il pi&ugrave; possibile un tracciato di gara.', 'terra_morbida', '09:00:00', '17:00:00', '1.jpg'),
(2, 1500, 'Tracciato easy, adatto a piloti neofiti e amatoriali. Si articola in panettoni di piccola e media lunghezza e curve principalmente senza sponde. Sono presenti molti canali e curve in contropendenza, per aiutare i piloti nell\'apprendimento della tecnica di guida.', 'terra_battuta', '10:00:00', '17:00:00', '2.jpg'),
(3, 4200, 'Tracciato enduro. Adatto a piloti che non amano i salti o curve troppo impegnative. Si articola in lunghi rettilinei e ampie curve piatte.', 'terra_sassosa', '09:00:00', '15:00:00', '3.jpeg');

-- --------------------------------------------------------

--
-- Struttura della tabella `utente`
--

CREATE TABLE `utente` (
  `cf` char(16) NOT NULL,
  `username` varchar(20) NOT NULL,
  `cognome` varchar(25) NOT NULL,
  `nome` varchar(25) NOT NULL,
  `nascita` date NOT NULL,
  `telefono` varchar(10) NOT NULL,
  `email` varchar(50) NOT NULL,
  `password` varchar(256) NOT NULL,
  `ruolo` tinyint(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dump dei dati per la tabella `utente`
--

INSERT INTO `utente` (`cf`, `username`, `cognome`, `nome`, `nascita`, `telefono`, `email`, `password`, `ruolo`) VALUES
('BRGFPP00B26C111Y', 'user', 'User', 'User', '2000-02-26', '3459751230', 'user@user.com', '$2y$10$OvvZ25M6vSs7QR1muPMtIuBCP7wkvbQjR4qCQJVbPFcAHs/...W/u', 1),
('CVLLSN00A04A001A', 'admin', 'Admin', 'Admin', '2000-01-04', '3477625776', 'admin@amp.com', '$2y$10$iXw1Y0BTQYZ5dHZBinRNZ.Dr7AYohu/EIXLzGcQFmMVphGUqrTyTy', 2),
('LDFDRE00P22A874G', 'leo22', 'Leo', 'Rossi', '2000-01-01', '3333333333', 'leo@gmail.com', '$2y$10$gqwJv8zfrJdSABRek4Gn7.aW6L1xWHd19zJyvWBYZIDSGU8yCADUi', 1),
('MMMGGG00A55G222V', 'margre', 'Greggio', 'Marta', '2001-01-13', '3216571845', 'ciao@ciao.com', '$2y$10$w1AZTfSyZt2PJT6vHCwc9e72eBUUanUAVzQxiKtfWTbms/HUnS4T.', 1);

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
  ADD UNIQUE KEY `utente` (`utente`,`data`),
  ADD KEY `ingressi_entrata_ibfk_1` (`data`);

--
-- Indici per le tabelle `ingressi_lezione`
--
ALTER TABLE `ingressi_lezione`
  ADD PRIMARY KEY (`codice`),
  ADD UNIQUE KEY `utente` (`utente`,`lezione`),
  ADD KEY `ingressi_lezione_ibfk_1` (`lezione`);

--
-- Indici per le tabelle `lezione`
--
ALTER TABLE `lezione`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pista` (`pista`),
  ADD KEY `lezione_data` (`data`);

--
-- Indici per le tabelle `messaggio`
--
ALTER TABLE `messaggio`
  ADD PRIMARY KEY (`id`);

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
  ADD UNIQUE KEY `data` (`data`,`utente`),
  ADD UNIQUE KEY `data_2` (`data`,`utente`),
  ADD KEY `fk_noleggio` (`utente`),
  ADD KEY `noleggio_ibfk_1` (`moto`);

--
-- Indici per le tabelle `pista`
--
ALTER TABLE `pista`
  ADD PRIMARY KEY (`id`);

--
-- Indici per le tabelle `utente`
--
ALTER TABLE `utente`
  ADD PRIMARY KEY (`cf`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT per le tabelle scaricate
--

--
-- AUTO_INCREMENT per la tabella `ingressi_entrata`
--
ALTER TABLE `ingressi_entrata`
  MODIFY `codice` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=56;

--
-- AUTO_INCREMENT per la tabella `ingressi_lezione`
--
ALTER TABLE `ingressi_lezione`
  MODIFY `codice` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT per la tabella `lezione`
--
ALTER TABLE `lezione`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT per la tabella `messaggio`
--
ALTER TABLE `messaggio`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT per la tabella `moto`
--
ALTER TABLE `moto`
  MODIFY `numero` smallint(6) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT per la tabella `noleggio`
--
ALTER TABLE `noleggio`
  MODIFY `codice` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=48;

--
-- AUTO_INCREMENT per la tabella `pista`
--
ALTER TABLE `pista`
  MODIFY `id` smallint(6) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

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
  ADD CONSTRAINT `lezione_data` FOREIGN KEY (`data`) REFERENCES `data_disponibile` (`data`) ON DELETE CASCADE ON UPDATE CASCADE,
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
