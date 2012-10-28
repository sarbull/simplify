-- phpMyAdmin SQL Dump
-- version 3.5.2.2
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Oct 28, 2012 at 10:28 AM
-- Server version: 5.5.24-0ubuntu0.12.04.1-log
-- PHP Version: 5.3.10-1ubuntu3.4

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `dev_simplify`
--

-- --------------------------------------------------------

--
-- Table structure for table `feed_items`
--

CREATE TABLE IF NOT EXISTS `feed_items` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `item_id` varchar(50) NOT NULL,
  `service` varchar(20) NOT NULL,
  `feed_id` int(10) unsigned NOT NULL,
  `timestamp` datetime NOT NULL,
  `author` varchar(50) NOT NULL,
  `author_id` varchar(50) NOT NULL,
  `author_data` text NOT NULL,
  `title` varchar(200) NOT NULL,
  `content` text NOT NULL,
  `link` varchar(150) NOT NULL,
  `data` text NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `item_id` (`service`,`item_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=26 ;

--
-- Dumping data for table `feed_items`
--

INSERT INTO `feed_items` (`id`, `item_id`, `service`, `feed_id`, `timestamp`, `author`, `author_id`, `author_data`, `title`, `content`, `link`, `data`) VALUES
(1, '100000244346961_500406559977484', 'facebook', 2, '2012-10-28 10:23:06', 'Andreea Stanescu', '100000244346961', 'a:1:{s:6:"avatar";s:49:"http://graph.facebook.com/100000244346961/picture";}', '', '', '', 'a:0:{}'),
(2, '100000820499185_366236526801426', 'facebook', 2, '2012-10-28 09:47:04', 'Sîrbu Nicolae-Cezar', '100000820499185', 'a:1:{s:6:"avatar";s:49:"http://graph.facebook.com/100000820499185/picture";}', '', 'Inca 3 ore 13 minute si 13 secunde de coding', 'http://www.facebook.com/100000820499185/posts/366236526801426', 'a:0:{}'),
(3, '1780647567_3268690254366', 'facebook', 2, '2012-10-28 10:13:17', 'Gabriel Georgescu', '1780647567', 'a:1:{s:6:"avatar";s:44:"http://graph.facebook.com/1780647567/picture";}', '', 'Neata lume ;;)', 'http://www.facebook.com/1780647567/posts/3268690254366', 'a:0:{}'),
(4, '100001193610735_435184719864645', 'facebook', 2, '2012-10-28 10:19:28', 'Grigorescu Ciprian', '100001193610735', 'a:1:{s:6:"avatar";s:49:"http://graph.facebook.com/100001193610735/picture";}', '', 'http://cipriangrigorescu.blogspot.ro/2012/10/mirror-ii.html', 'http://www.facebook.com/100001193610735/posts/435184719864645', 'a:0:{}'),
(5, '1613567892_400286370042105', 'facebook', 2, '2012-10-28 05:41:36', 'Nestor Dragusin', '1613567892', 'a:1:{s:6:"avatar";s:44:"http://graph.facebook.com/1613567892/picture";}', '', 'Nestor Dragusin shared a link.', 'http://www.facebook.com/1613567892/posts/400286370042105', 'a:0:{}'),
(6, '290539813359_10151216300913360', 'facebook', 2, '2012-10-28 02:05:32', 'Trust Me, I''m an "Engineer"', '290539813359', 'a:1:{s:6:"avatar";s:46:"http://graph.facebook.com/290539813359/picture";}', '', 'Trust Me, I''m an "Engineer" added 3 new photos to the album Engineers.', 'http://www.facebook.com/290539813359/posts/10151216300913360', 'a:0:{}'),
(7, '1351387129_4142484724374', 'facebook', 2, '2012-10-28 04:32:38', 'Cristian Condurache', '1351387129', 'a:1:{s:6:"avatar";s:44:"http://graph.facebook.com/1351387129/picture";}', '', 'Hack&Slash', 'http://www.facebook.com/1351387129/posts/4142484724374', 'a:0:{}'),
(8, '100001709796153_433167906750199', 'facebook', 2, '2012-10-28 12:23:31', 'Toader Ionut-Tudor', '100001709796153', 'a:1:{s:6:"avatar";s:49:"http://graph.facebook.com/100001709796153/picture";}', '', 'Toader Ionut-Tudor was tagged in Motoroi Andrei''s photo.', 'http://www.facebook.com/100001709796153/posts/433167906750199', 'a:0:{}'),
(9, '100001738471679_449326728446279', 'facebook', 2, '2012-10-27 11:42:46', 'Alexandru Gabriel Tudor', '100001738471679', 'a:1:{s:6:"avatar";s:49:"http://graph.facebook.com/100001738471679/picture";}', '', 'Alexandru Gabriel Tudor commented on a photo.', '', 'a:0:{}'),
(10, '360172907404376_101064300058216', 'facebook', 2, '2012-10-27 11:34:46', 'Retele Locale', '360172907404376', 'a:1:{s:6:"avatar";s:49:"http://graph.facebook.com/360172907404376/picture";}', '', 'Vino la cursul 5 de RL!\nhttp://www.youtube.com/watch?v=iE_AbM8ZykI', 'http://www.facebook.com/360172907404376/posts/101064300058216', 'a:0:{}'),
(11, '100001193610735_434965396553244', 'facebook', 2, '2012-10-28 12:18:47', 'Grigorescu Ciprian', '100001193610735', 'a:1:{s:6:"avatar";s:49:"http://graph.facebook.com/100001193610735/picture";}', '', 'http://cipriangrigorescu.blogspot.ro/2012/10/mirror.html', 'http://www.facebook.com/100001193610735/posts/434965396553244', 'a:0:{}'),
(12, '100001666335789_426523570746517', 'facebook', 2, '2012-10-27 11:07:22', 'Vladimir Ungureanu', '100001666335789', 'a:1:{s:6:"avatar";s:49:"http://graph.facebook.com/100001666335789/picture";}', '', 'gaseste-ti  un loc in viata asta. Cucu', 'http://www.facebook.com/100001666335789/posts/426523570746517', 'a:0:{}'),
(13, '100001666335789_426515320747342', 'facebook', 2, '2012-10-28 07:19:44', 'Vladimir Ungureanu', '100001666335789', 'a:1:{s:6:"avatar";s:49:"http://graph.facebook.com/100001666335789/picture";}', '', 'am vaszat bautura pe masa, am primit shoturi, covrigi, bomboane, Emi cel mai de treaba ospatar !', 'http://www.facebook.com/100001666335789/posts/426515320747342', 'a:0:{}'),
(14, '100004364912668_447674965273858', 'facebook', 2, '2012-10-27 10:29:06', 'Alecs Calinescu', '100004364912668', 'a:1:{s:6:"avatar";s:49:"http://graph.facebook.com/100004364912668/picture";}', '', 'Alecs Calinescu shared a link.', 'http://www.facebook.com/100004364912668/posts/447674965273858', 'a:0:{}'),
(15, '0_447674965273858', 'facebook', 2, '2012-10-27 10:29:06', 'Alecs Calinescu', '100004364912668', 'a:1:{s:6:"avatar";s:49:"http://graph.facebook.com/100004364912668/picture";}', '', 'Alecs Calinescu shared a link.', '', 'a:0:{}'),
(16, '100000608494296_399980463404973', 'facebook', 2, '2012-10-27 10:28:50', 'Alexandra Iliesco', '100000608494296', 'a:1:{s:6:"avatar";s:49:"http://graph.facebook.com/100000608494296/picture";}', '', 'Alexandra Iliesco shared a link.', 'http://www.facebook.com/100000608494296/posts/399980463404973', 'a:0:{}'),
(17, '1780647567_3267036133014', 'facebook', 2, '2012-10-27 10:39:25', 'Gabriel Georgescu', '1780647567', 'a:1:{s:6:"avatar";s:44:"http://graph.facebook.com/1780647567/picture";}', '', 'Does not believe in man that tells you everything!!!!!', 'http://www.facebook.com/1780647567/posts/3267036133014', 'a:0:{}'),
(18, '731329390_10151263707479391', 'facebook', 2, '2012-10-27 10:24:01', 'Cristina Ţintă', '731329390', 'a:1:{s:6:"avatar";s:43:"http://graph.facebook.com/731329390/picture";}', '', 'Moment cu rufe și copii', 'http://www.facebook.com/731329390/posts/10151263707479391', 'a:0:{}'),
(19, '100000209613475_10151092147056711', 'facebook', 2, '2012-10-27 10:19:32', 'Andrei Bălan', '100000209613475', 'a:1:{s:6:"avatar";s:49:"http://graph.facebook.com/100000209613475/picture";}', '', 'Andrei Bălan commented on a photo.', '', 'a:0:{}'),
(20, '100001709796153_384695611610415', 'facebook', 2, '2012-10-27 10:07:31', 'Toader Ionut-Tudor', '100001709796153', 'a:1:{s:6:"avatar";s:49:"http://graph.facebook.com/100001709796153/picture";}', '', 'Toader Ionut-Tudor was tagged at Piano Bar.', '', 'a:0:{}'),
(21, '100001666335789_426502024082005', 'facebook', 2, '2012-10-27 10:36:14', 'Vladimir Ungureanu', '100001666335789', 'a:1:{s:6:"avatar";s:49:"http://graph.facebook.com/100001666335789/picture";}', '', 'daca nu ti se pare un lucru a fi corect si nu il iei in seama aia e calea lasului. Cucu', 'http://www.facebook.com/100001666335789/posts/426502024082005', 'a:0:{}'),
(22, '1780647567_3265097964561', 'facebook', 2, '2012-10-27 09:55:06', 'Gabriel Georgescu', '1780647567', 'a:1:{s:6:"avatar";s:44:"http://graph.facebook.com/1780647567/picture";}', '', 'Gabriel Georgescu was tagged in Gabriel Georgescu Photography''s album Landscape.', '', 'a:0:{}'),
(23, '731329390_10151263659869391', 'facebook', 2, '2012-10-27 09:53:41', 'Cristina Ţintă', '731329390', 'a:1:{s:6:"avatar";s:43:"http://graph.facebook.com/731329390/picture";}', '', 'Cristina Ţintă added a new photo.', 'http://www.facebook.com/731329390/posts/10151263659869391', 'a:0:{}'),
(24, '100000050022177_385516524858590', 'facebook', 2, '2012-10-27 09:44:36', 'Vlad Paunescu', '100000050022177', 'a:1:{s:6:"avatar";s:49:"http://graph.facebook.com/100000050022177/picture";}', '', 'Vlad Paunescu shared a link.', 'http://www.facebook.com/100000050022177/posts/385516524858590', 'a:0:{}'),
(25, '643744372_10151206856984373', 'facebook', 2, '2012-10-27 09:33:06', 'Sergiu Poenaru', '643744372', 'a:1:{s:6:"avatar";s:43:"http://graph.facebook.com/643744372/picture";}', '', 'Primul proiect pe rolancer.com, un grasshoper dar sa fie facut pana in 700usd. np!', 'http://www.facebook.com/643744372/posts/10151206856984373', 'a:0:{}');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `email` varchar(50) NOT NULL,
  `password` varchar(50) NOT NULL,
  `created` datetime NOT NULL,
  `last_login` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `created`, `last_login`) VALUES
(1, 'Stancu Florin', 'niflostancu@gmail.com', '53936ce6eef01d89d066ae900e9a1de5', '2012-10-01 00:00:00', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `user_feeds`
--

CREATE TABLE IF NOT EXISTS `user_feeds` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `service` varchar(20) NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `auth_data` text NOT NULL,
  `last_update` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `user_feeds`
--

INSERT INTO `user_feeds` (`id`, `service`, `user_id`, `auth_data`, `last_update`) VALUES
(2, 'facebook', 1, 's:216:"AQBA9QMsrxfYz6i2UCanYCg9yLZrNJHBMOOD6cle-5OSRASI23dOHzKhJKM6857Hk7xlX4jivbizYb_EjBDR49YuowedkOCynVrvZOzC30r7XrJoHu1ow_Wr6fQThHZbHobo-Xw1ISYjterJr9JXCxtUS7Ea7S3IMTnZhqM2gV6Qpo98cKN1pscOKn035YGmoX9gan04Z1SfRNlcknYMGLgK";', '2012-10-28 10:23:24');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;