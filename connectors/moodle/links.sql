-- phpMyAdmin SQL Dump
-- version 2.6.3-pl1
-- http://www.phpmyadmin.net
-- 
-- Host: localhost
-- Generation Time: Dec 19, 2006 at 05:00 PM
-- Server version: 4.1.14
-- PHP Version: 4.4.2
-- 
-- Database: `achapin_segue-moodle`
-- 

-- --------------------------------------------------------

-- 
-- Table structure for table `authentication`
-- 

CREATE TABLE `authentication` (
  `auth_id` int(10) unsigned NOT NULL auto_increment,
  `system` varchar(128) NOT NULL default '',
  `username` varchar(255) NOT NULL default '',
  `firstname` varchar(255) NOT NULL default '',
  `lastname` varchar(255) NOT NULL default '',
  `email` varchar(255) NOT NULL default '',
  `user_id` int(10) NOT NULL default '0',
  `auth_token` varchar(128) NOT NULL default '',
  `auth_time` timestamp NULL default NULL,
  `referer` text,
  PRIMARY KEY  (`auth_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=54 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `logs`
-- 

CREATE TABLE `logs` (
  `log_id` int(10) unsigned NOT NULL auto_increment,
  `FK_auth_id` int(10) default '0',
  `FK_site_link` int(11) default NULL,
  `category` varchar(255) NOT NULL default 'event',
  `description` text,
  `log_time` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`log_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=11 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `segue_moodle`
-- 

CREATE TABLE `segue_moodle` (
  `site_link_id` int(10) NOT NULL auto_increment,
  `FK_segue_site_id` int(10) NOT NULL default '0',
  `FK_moodle_site_id` int(10) NOT NULL default '0',
  `site_title` varchar(255) default NULL,
  `site_slot` varchar(128) NOT NULL default '',
  `site_owner_id` varchar(255) default NULL,
  `site_theme` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`site_link_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=8 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `user_link`
-- 

CREATE TABLE `user_link` (
  `FK_auth_id` int(10) NOT NULL default '0',
  `system` varchar(128) NOT NULL default '',
  `user_id` int(10) NOT NULL default '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
