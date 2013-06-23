-- phpMyAdmin SQL Dump
-- version 3.4.10.1deb1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generato il: Gen 27, 2013 alle 18:22
-- Versione del server: 5.5.29
-- Versione PHP: 5.3.10-1ubuntu3.5

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `my_evolutionbg`
--

--
-- Dump dei dati per la tabella `s1_civ`
--

INSERT INTO `s1_civ` (`civ_id`, `civ_name`, `civ_adjective`, `civ_ally`, `civ_age`, `des_civ`, `civ_pop`, `quest`, `state`, `read_quest`, `master`, `ev_ready`) VALUES
(0, '-', '-', 0, 0, '', 2, 1, 0, 0, 1, 0);

--
-- Dump dei dati per la tabella `site_track_cat`
--

INSERT INTO `site_track_cat` (`id`, `name`, `description`) VALUES
(1, 'GENERIC', 'GENERIC_DES');

--
-- Dump dei dati per la tabella `site_users`
--

INSERT INTO `site_users` (`ID`, `username`, `password`, `email`, `active`, `code`, `description`, `code_time`) VALUES
(1, 'anonimo', '', '', 0, '', '', 0);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
