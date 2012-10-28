-- phpMyAdmin SQL Dump
-- version 3.5.2.2
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Oct 28, 2012 at 03:50 AM
-- Server version: 5.5.24-0ubuntu0.12.04.1-log
-- PHP Version: 5.3.10-1ubuntu3.4

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

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
  `data` text NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `item_id` (`service`,`item_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

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
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

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
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
