-- phpMyAdmin SQL Dump
-- version 2.11.11.3
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generato il: 10 Gen, 2012 at 02:13 PM
-- Versione MySQL: 5.1.58
-- Versione PHP: 4.4.9

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `my_evolutionbg`
--

--
-- Dump dei dati per la tabella `s1_ally`
--


--
-- Dump dei dati per la tabella `s1_ally_pacts`
--


--
-- Dump dei dati per la tabella `s1_ally_permissions`
--


--
-- Dump dei dati per la tabella `s1_building`
--


--
-- Dump dei dati per la tabella `s1_civ`
--

INSERT INTO `s1_civ` (`civ_id`, `civ_name`, `civ_adjective`, `civ_ally`, `civ_age`, `des_civ`, `civ_pop`, `quest`, `state`, `read_quest`, `master`, `ev_ready`) VALUES
(0, '-', '-', 0, 0, '', 2, 1, 0, 0, 1, 0);

--
-- Dump dei dati per la tabella `s1_events`
--


--
-- Dump dei dati per la tabella `s1_map`
--


--
-- Dump dei dati per la tabella `s1_mess`
--


--
-- Dump dei dati per la tabella `s1_mess_read`
--


--
-- Dump dei dati per la tabella `s1_offer`
--


--
-- Dump dei dati per la tabella `s1_option`
--


--
-- Dump dei dati per la tabella `s1_params`
--


--
-- Dump dei dati per la tabella `s1_quest`
--


--
-- Dump dei dati per la tabella `s1_report`
--


--
-- Dump dei dati per la tabella `s1_report_read`
--


--
-- Dump dei dati per la tabella `s1_research`
--


--
-- Dump dei dati per la tabella `s1_troopers`
--


--
-- Dump dei dati per la tabella `site_alerts`
--


--
-- Dump dei dati per la tabella `site_alerts_read`
--


--
-- Dump dei dati per la tabella `site_clean`
--


--
-- Dump dei dati per la tabella `site_config`
--


--
-- Dump dei dati per la tabella `site_faq`
--


--
-- Dump dei dati per la tabella `site_option`
--


--
-- Dump dei dati per la tabella `site_report`
--


--
-- Dump dei dati per la tabella `site_role`
--

INSERT INTO `site_role` (`user_id`, `role`) VALUES
(2, 'admin');

--
-- Dump dei dati per la tabella `site_sessions`
--


--
-- Dump dei dati per la tabella `site_track`
--


--
-- Dump dei dati per la tabella `site_track_assoc_tag`
--


--
-- Dump dei dati per la tabella `site_track_cat`
--


--
-- Dump dei dati per la tabella `site_track_tag`
--


--
-- Dump dei dati per la tabella `site_users`
--

INSERT INTO `site_users` (`ID`, `username`, `user_pass`, `user_mail`, `user_active`, `user_code`, `des_user`) VALUES
(1, 'anonimo', '', '', 0, '', ''),
(2, 'pagliaccio', '9b2a77c727e2856b757d015ba494762f', 'mangheriniroberto@alice.it', 1, '', 'Admin e sviluppatore del gioco');

--
-- Dump dei dati per la tabella `site_user_civ`
--


--
-- Dump dei dati per la tabella `temp`
--

