-- phpMyAdmin SQL Dump
-- version 3.4.10.1deb1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generato il: Gen 27, 2013 alle 18:21
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

-- --------------------------------------------------------

--
-- Struttura della tabella `s1_ally`
--

DROP TABLE IF EXISTS `s1_ally`;
CREATE TABLE `s1_ally` (
  `id` int(5) NOT NULL AUTO_INCREMENT,
  `name` varchar(30) NOT NULL,
  `description` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struttura della tabella `s1_ally_pacts`
--

DROP TABLE IF EXISTS `s1_ally_pacts`;
CREATE TABLE `s1_ally_pacts` (
  `id_ally1` int(5) NOT NULL,
  `id_ally2` int(5) NOT NULL,
  `type` int(1) NOT NULL,
  `status` int(1) NOT NULL,
  PRIMARY KEY (`id_ally1`,`id_ally2`,`type`),
  KEY `id_ally2` (`id_ally2`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struttura della tabella `s1_ally_permissions`
--

DROP TABLE IF EXISTS `s1_ally_permissions`;
CREATE TABLE `s1_ally_permissions` (
  `civ_id` int(5) NOT NULL,
  `name` varchar(10) NOT NULL,
  `value` int(1) NOT NULL,
  PRIMARY KEY (`civ_id`,`name`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struttura della tabella `s1_building`
--

DROP TABLE IF EXISTS `s1_building`;
CREATE TABLE `s1_building` (
  `village_id` int(5) NOT NULL,
  `type` int(2) NOT NULL,
  `liv` int(2) NOT NULL,
  `pos` int(2) NOT NULL,
  `pop` int(9) NOT NULL,
  PRIMARY KEY (`village_id`,`pos`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struttura della tabella `s1_civ`
--

DROP TABLE IF EXISTS `s1_civ`;
CREATE TABLE `s1_civ` (
  `civ_id` int(5) NOT NULL AUTO_INCREMENT,
  `civ_name` varchar(30) NOT NULL,
  `civ_adjective` varchar(30) NOT NULL,
  `civ_ally` int(5) NOT NULL DEFAULT '0',
  `civ_age` int(1) NOT NULL DEFAULT '0',
  `des_civ` text NOT NULL,
  `civ_pop` int(6) NOT NULL DEFAULT '2',
  `quest` int(2) NOT NULL DEFAULT '1',
  `state` int(1) NOT NULL DEFAULT '0',
  `read_quest` int(1) NOT NULL DEFAULT '0',
  `master` int(1) NOT NULL DEFAULT '1',
  `ev_ready` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`civ_id`),
  KEY `ally` (`civ_ally`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struttura della tabella `s1_events`
--

DROP TABLE IF EXISTS `s1_events`;
CREATE TABLE `s1_events` (
  `id` int(5) NOT NULL AUTO_INCREMENT,
  `type` int(2) NOT NULL,
  `time` int(30) NOT NULL,
  `params` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struttura della tabella `s1_map`
--

DROP TABLE IF EXISTS `s1_map`;
CREATE TABLE `s1_map` (
  `id` int(5) NOT NULL AUTO_INCREMENT,
  `civ_id` int(5) NOT NULL DEFAULT '0',
  `name` varchar(30) NOT NULL,
  `capital` int(1) NOT NULL,
  `type` int(1) NOT NULL DEFAULT '0',
  `pop` float NOT NULL,
  `busy_pop` float NOT NULL,
  `resource_1` float NOT NULL,
  `resource_2` float NOT NULL,
  `resource_3` float NOT NULL,
  `production_1` int(9) NOT NULL,
  `production_2` int(9) NOT NULL,
  `production_3` int(9) NOT NULL,
  `agg` int(30) NOT NULL,
  `aggPop` int(30) NOT NULL,
  `x` int(4) NOT NULL DEFAULT '0',
  `y` int(4) NOT NULL DEFAULT '0',
  `order_n` int(3) NOT NULL DEFAULT '0',
  `zone` int(1) NOT NULL DEFAULT '0',
  `defence` int(3) NOT NULL DEFAULT '100',
  `prod1_bonus` int(3) NOT NULL DEFAULT '100',
  `prod2_bonus` int(3) NOT NULL DEFAULT '100',
  `prod3_bonus` int(3) NOT NULL DEFAULT '100',
  PRIMARY KEY (`id`),
  UNIQUE KEY `x` (`x`,`y`),
  KEY `name` (`name`),
  KEY `civ_id` (`civ_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struttura della tabella `s1_mess`
--

DROP TABLE IF EXISTS `s1_mess`;
CREATE TABLE `s1_mess` (
  `id` int(5) NOT NULL AUTO_INCREMENT,
  `oggetto` varchar(40) NOT NULL DEFAULT '',
  `messaggio` text NOT NULL,
  `ora` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `destinatario` int(5) NOT NULL,
  `mittente` int(5) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `destinatario` (`destinatario`),
  KEY `mittente` (`mittente`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struttura della tabella `s1_mess_read`
--

DROP TABLE IF EXISTS `s1_mess_read`;
CREATE TABLE `s1_mess_read` (
  `id` int(5) NOT NULL,
  `user` int(5) NOT NULL,
  PRIMARY KEY (`id`,`user`),
  KEY `user` (`user`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struttura della tabella `s1_offer`
--

DROP TABLE IF EXISTS `s1_offer`;
CREATE TABLE `s1_offer` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `civ_id` int(5) NOT NULL,
  `resource` int(9) NOT NULL,
  `type` int(1) NOT NULL,
  `rapport` float NOT NULL,
  `vid` int(5) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `civ_id` (`civ_id`),
  KEY `vid` (`vid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struttura della tabella `s1_option`
--

DROP TABLE IF EXISTS `s1_option`;
CREATE TABLE `s1_option` (
  `civ_id` int(5) NOT NULL,
  `option` varchar(10) NOT NULL,
  `value` text NOT NULL,
  PRIMARY KEY (`civ_id`,`option`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struttura della tabella `s1_params`
--

DROP TABLE IF EXISTS `s1_params`;
CREATE TABLE `s1_params` (
  `name` varchar(20) NOT NULL,
  `value` text NOT NULL,
  PRIMARY KEY (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struttura della tabella `s1_quest`
--

DROP TABLE IF EXISTS `s1_quest`;
CREATE TABLE `s1_quest` (
  `age` int(1) NOT NULL,
  `n` int(2) NOT NULL,
  `title` varchar(50) NOT NULL,
  `text` text NOT NULL,
  `master` int(11) NOT NULL,
  `back` text NOT NULL,
  `condiction` text NOT NULL,
  `reward` text NOT NULL,
  PRIMARY KEY (`age`,`n`,`master`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struttura della tabella `s1_report`
--

DROP TABLE IF EXISTS `s1_report`;
CREATE TABLE `s1_report` (
  `civ` int(5) NOT NULL,
  `data` text NOT NULL,
  `time` int(40) NOT NULL,
  `id` int(5) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`),
  KEY `civ` (`civ`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struttura della tabella `s1_report_read`
--

DROP TABLE IF EXISTS `s1_report_read`;
CREATE TABLE `s1_report_read` (
  `user` int(5) NOT NULL,
  `id` int(5) NOT NULL,
  PRIMARY KEY (`user`,`id`),
  KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struttura della tabella `s1_research`
--

DROP TABLE IF EXISTS `s1_research`;
CREATE TABLE `s1_research` (
  `civ_id` int(5) NOT NULL,
  `rid` int(5) NOT NULL,
  `liv` int(2) NOT NULL DEFAULT '0',
  `enable` enum('0','1') NOT NULL DEFAULT '0',
  PRIMARY KEY (`civ_id`,`rid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struttura della tabella `s1_troopers`
--

DROP TABLE IF EXISTS `s1_troopers`;
CREATE TABLE `s1_troopers` (
  `trooper_id` int(5) NOT NULL,
  `civ_id` int(5) NOT NULL,
  `numbers` int(9) NOT NULL,
  `village_now` int(5) NOT NULL,
  `village_prev` int(5) NOT NULL,
  KEY `civ_id` (`civ_id`),
  KEY `village_now` (`village_now`),
  KEY `village_prev` (`village_prev`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struttura della tabella `site_alerts`
--

DROP TABLE IF EXISTS `site_alerts`;
CREATE TABLE `site_alerts` (
  `aid` int(5) NOT NULL AUTO_INCREMENT,
  `title` varchar(40) NOT NULL,
  `text` text NOT NULL,
  PRIMARY KEY (`aid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struttura della tabella `site_alerts_read`
--

DROP TABLE IF EXISTS `site_alerts_read`;
CREATE TABLE `site_alerts_read` (
  `id` int(5) NOT NULL,
  `user` int(5) NOT NULL,
  PRIMARY KEY (`id`,`user`),
  KEY `user` (`user`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struttura della tabella `site_clean`
--

DROP TABLE IF EXISTS `site_clean`;
CREATE TABLE `site_clean` (
  `id` int(1) NOT NULL DEFAULT '1',
  `time` int(30) NOT NULL,
  `server` varchar(3) NOT NULL,
  PRIMARY KEY (`id`,`server`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struttura della tabella `site_config`
--

DROP TABLE IF EXISTS `site_config`;
CREATE TABLE `site_config` (
  `option` varchar(10) NOT NULL DEFAULT '0',
  `value` text,
  PRIMARY KEY (`option`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struttura della tabella `site_faq`
--

DROP TABLE IF EXISTS `site_faq`;
CREATE TABLE `site_faq` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `question` varchar(100) NOT NULL,
  `reply` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struttura della tabella `site_option`
--

DROP TABLE IF EXISTS `site_option`;
CREATE TABLE `site_option` (
  `user_id` int(5) NOT NULL,
  `option` varchar(10) NOT NULL,
  `value` text NOT NULL,
  PRIMARY KEY (`user_id`,`option`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struttura della tabella `site_role`
--

DROP TABLE IF EXISTS `site_role`;
CREATE TABLE `site_role` (
  `user_id` int(5) NOT NULL DEFAULT '1',
  `role` varchar(40) NOT NULL,
  PRIMARY KEY (`user_id`,`role`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struttura della tabella `site_sessions`
--

DROP TABLE IF EXISTS `site_sessions`;
CREATE TABLE `site_sessions` (
  `ID` text NOT NULL,
  `var_name` text NOT NULL,
  `var_value` longtext NOT NULL,
  `rand_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `create` int(20) NOT NULL,
  `last_activity` int(20) NOT NULL,
  `validate` int(1) NOT NULL DEFAULT '1',
  `user_id` int(5) NOT NULL,
  PRIMARY KEY (`rand_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struttura della tabella `site_track`
--

DROP TABLE IF EXISTS `site_track`;
CREATE TABLE `site_track` (
  `id` int(5) NOT NULL AUTO_INCREMENT,
  `uid` int(5) NOT NULL,
  `status` enum('new','reply','close') NOT NULL DEFAULT 'new',
  `type` enum('bug','idea','notlike') NOT NULL DEFAULT 'bug',
  `category` int(5) NOT NULL,
  `description` text NOT NULL,
  `screen` varchar(60) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`),
  KEY `category` (`category`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struttura della tabella `site_track_assoc_tag`
--

DROP TABLE IF EXISTS `site_track_assoc_tag`;
CREATE TABLE `site_track_assoc_tag` (
  `id` int(5) NOT NULL,
  `tid` int(5) NOT NULL,
  PRIMARY KEY (`id`,`tid`),
  KEY `tid` (`tid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struttura della tabella `site_track_cat`
--

DROP TABLE IF EXISTS `site_track_cat`;
CREATE TABLE `site_track_cat` (
  `id` int(5) NOT NULL AUTO_INCREMENT,
  `name` varchar(30) NOT NULL,
  `description` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struttura della tabella `site_track_tag`
--

DROP TABLE IF EXISTS `site_track_tag`;
CREATE TABLE `site_track_tag` (
  `id` int(5) NOT NULL AUTO_INCREMENT,
  `name` varchar(30) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struttura della tabella `site_users`
--

DROP TABLE IF EXISTS `site_users`;
CREATE TABLE `site_users` (
  `ID` int(5) NOT NULL AUTO_INCREMENT,
  `username` varchar(30) NOT NULL,
  `password` varchar(32) NOT NULL,
  `email` varchar(30) NOT NULL,
  `active` int(1) NOT NULL,
  `code` varchar(256) NOT NULL,
  `description` text NOT NULL,
  `code_time` int(20) NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struttura della tabella `site_user_civ`
--

DROP TABLE IF EXISTS `site_user_civ`;
CREATE TABLE `site_user_civ` (
  `user_id` int(5) NOT NULL,
  `civ_id` int(5) NOT NULL,
  `server` varchar(5) NOT NULL DEFAULT 's1',
  `status` int(1) NOT NULL DEFAULT '0',
  `current_village` int(5) NOT NULL,
  PRIMARY KEY (`user_id`,`server`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struttura della tabella `temp`
--

DROP TABLE IF EXISTS `temp`;
CREATE TABLE `temp` (
  `x` int(4) NOT NULL,
  `y` int(4) NOT NULL,
  `zone` int(1) NOT NULL,
  `bonus1` int(3) NOT NULL DEFAULT '100',
  `bonus2` int(3) NOT NULL DEFAULT '100',
  `bonus3` int(3) NOT NULL DEFAULT '100',
  PRIMARY KEY (`x`,`y`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Limiti per le tabelle scaricate
--

--
-- Limiti per la tabella `s1_ally_pacts`
--
ALTER TABLE `s1_ally_pacts`
  ADD CONSTRAINT `s1_ally_pacts_ibfk_2` FOREIGN KEY (`id_ally2`) REFERENCES `s1_ally` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `s1_ally_pacts_ibfk_1` FOREIGN KEY (`id_ally1`) REFERENCES `s1_ally` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Limiti per la tabella `s1_ally_permissions`
--
ALTER TABLE `s1_ally_permissions`
  ADD CONSTRAINT `s1_ally_permissions_ibfk_1` FOREIGN KEY (`civ_id`) REFERENCES `s1_civ` (`civ_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Limiti per la tabella `s1_building`
--
ALTER TABLE `s1_building`
  ADD CONSTRAINT `s1_building_ibfk_1` FOREIGN KEY (`village_id`) REFERENCES `s1_map` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Limiti per la tabella `s1_map`
--
ALTER TABLE `s1_map`
  ADD CONSTRAINT `s1_map_ibfk_1` FOREIGN KEY (`civ_id`) REFERENCES `s1_civ` (`civ_id`) ON DELETE NO ACTION ON UPDATE CASCADE;

--
-- Limiti per la tabella `s1_mess`
--
ALTER TABLE `s1_mess`
  ADD CONSTRAINT `s1_mess_ibfk_2` FOREIGN KEY (`mittente`) REFERENCES `site_users` (`ID`) ON DELETE NO ACTION ON UPDATE CASCADE,
  ADD CONSTRAINT `s1_mess_ibfk_1` FOREIGN KEY (`destinatario`) REFERENCES `s1_civ` (`civ_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Limiti per la tabella `s1_mess_read`
--
ALTER TABLE `s1_mess_read`
  ADD CONSTRAINT `s1_mess_read_ibfk_2` FOREIGN KEY (`user`) REFERENCES `site_users` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `s1_mess_read_ibfk_1` FOREIGN KEY (`id`) REFERENCES `s1_mess` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Limiti per la tabella `s1_offer`
--
ALTER TABLE `s1_offer`
  ADD CONSTRAINT `s1_offer_ibfk_2` FOREIGN KEY (`vid`) REFERENCES `s1_map` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `s1_offer_ibfk_1` FOREIGN KEY (`civ_id`) REFERENCES `s1_civ` (`civ_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Limiti per la tabella `s1_option`
--
ALTER TABLE `s1_option`
  ADD CONSTRAINT `s1_option_ibfk_1` FOREIGN KEY (`civ_id`) REFERENCES `s1_civ` (`civ_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Limiti per la tabella `s1_report`
--
ALTER TABLE `s1_report`
  ADD CONSTRAINT `s1_report_ibfk_1` FOREIGN KEY (`civ`) REFERENCES `s1_civ` (`civ_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Limiti per la tabella `s1_report_read`
--
ALTER TABLE `s1_report_read`
  ADD CONSTRAINT `s1_report_read_ibfk_2` FOREIGN KEY (`id`) REFERENCES `s1_report` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `s1_report_read_ibfk_1` FOREIGN KEY (`user`) REFERENCES `site_users` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Limiti per la tabella `s1_research`
--
ALTER TABLE `s1_research`
  ADD CONSTRAINT `s1_research_ibfk_1` FOREIGN KEY (`civ_id`) REFERENCES `s1_civ` (`civ_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Limiti per la tabella `s1_troopers`
--
ALTER TABLE `s1_troopers`
  ADD CONSTRAINT `s1_troopers_ibfk_3` FOREIGN KEY (`village_prev`) REFERENCES `s1_map` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `s1_troopers_ibfk_1` FOREIGN KEY (`civ_id`) REFERENCES `s1_civ` (`civ_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `s1_troopers_ibfk_2` FOREIGN KEY (`village_now`) REFERENCES `s1_map` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Limiti per la tabella `site_alerts_read`
--
ALTER TABLE `site_alerts_read`
  ADD CONSTRAINT `site_alerts_read_ibfk_2` FOREIGN KEY (`user`) REFERENCES `site_users` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `site_alerts_read_ibfk_1` FOREIGN KEY (`id`) REFERENCES `site_alerts` (`aid`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Limiti per la tabella `site_option`
--
ALTER TABLE `site_option`
  ADD CONSTRAINT `site_option_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `site_users` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Limiti per la tabella `site_role`
--
ALTER TABLE `site_role`
  ADD CONSTRAINT `site_role_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `site_users` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Limiti per la tabella `site_sessions`
--
ALTER TABLE `site_sessions`
  ADD CONSTRAINT `site_sessions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `site_users` (`ID`) ON DELETE NO ACTION ON UPDATE CASCADE;

--
-- Limiti per la tabella `site_track`
--
ALTER TABLE `site_track`
  ADD CONSTRAINT `site_track_ibfk_2` FOREIGN KEY (`category`) REFERENCES `site_track_cat` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `site_track_ibfk_1` FOREIGN KEY (`uid`) REFERENCES `site_users` (`ID`) ON DELETE NO ACTION ON UPDATE CASCADE;

--
-- Limiti per la tabella `site_track_assoc_tag`
--
ALTER TABLE `site_track_assoc_tag`
  ADD CONSTRAINT `site_track_assoc_tag_ibfk_2` FOREIGN KEY (`tid`) REFERENCES `site_track_tag` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `site_track_assoc_tag_ibfk_1` FOREIGN KEY (`id`) REFERENCES `site_track` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Limiti per la tabella `site_user_civ`
--
ALTER TABLE `site_user_civ`
  ADD CONSTRAINT `site_user_civ_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `site_users` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
