-- MySQL dump 10.13  Distrib 5.5.42, for Linux (x86_64)
--
-- Host: localhost    Database: leaf_avail
-- ------------------------------------------------------
-- Server version	5.5.42-cll-lve

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `av_emoticons`
--

DROP TABLE IF EXISTS `av_emoticons`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `av_emoticons` (
  `id` int(9) NOT NULL AUTO_INCREMENT,
  `image` varchar(255) NOT NULL DEFAULT '',
  `text` varchar(255) NOT NULL DEFAULT '',
  `clickable` tinyint(1) NOT NULL DEFAULT '1',
  KEY `id` (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=27 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `av_forums`
--

DROP TABLE IF EXISTS `av_forums`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `av_forums` (
  `id` smallint(5) NOT NULL AUTO_INCREMENT,
  `title` varchar(128) NOT NULL DEFAULT '',
  `description` text NOT NULL,
  `parent` smallint(5) NOT NULL DEFAULT '0',
  `topic_count` int(5) NOT NULL DEFAULT '0',
  `post_count` int(5) NOT NULL DEFAULT '0',
  `lp_id` int(9) NOT NULL DEFAULT '0',
  `lp_title` varchar(255) NOT NULL DEFAULT '',
  `lp_aid` int(9) NOT NULL DEFAULT '0',
  `lp_author` varchar(255) NOT NULL DEFAULT '',
  `lp_date` int(9) NOT NULL DEFAULT '0',
  `sort` int(2) NOT NULL DEFAULT '0',
  KEY `id` (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=12 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `av_groups`
--

DROP TABLE IF EXISTS `av_groups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `av_groups` (
  `id` int(9) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `av_n_category`
--

DROP TABLE IF EXISTS `av_n_category`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `av_n_category` (
  `id` int(8) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL DEFAULT '',
  `image` varchar(255) NOT NULL DEFAULT '',
  `elements` int(8) NOT NULL DEFAULT '0',
  KEY `id` (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `av_n_comments`
--

DROP TABLE IF EXISTS `av_n_comments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `av_n_comments` (
  `id` int(9) NOT NULL AUTO_INCREMENT,
  `parent` int(9) NOT NULL DEFAULT '0',
  `aid` int(9) NOT NULL DEFAULT '0',
  `author` varchar(255) NOT NULL DEFAULT '',
  `date` int(9) NOT NULL DEFAULT '0',
  `message` text NOT NULL,
  `ip` varchar(16) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=53 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `av_news`
--

DROP TABLE IF EXISTS `av_news`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `av_news` (
  `id` int(9) NOT NULL AUTO_INCREMENT,
  `cat` int(9) NOT NULL DEFAULT '0',
  `date` int(9) NOT NULL DEFAULT '0',
  `author_id` int(9) NOT NULL DEFAULT '0',
  `author` varchar(255) NOT NULL DEFAULT '',
  `title` varchar(255) NOT NULL DEFAULT '',
  `text` text NOT NULL,
  `comments` int(9) NOT NULL DEFAULT '0',
  `iname` varchar(255) NOT NULL DEFAULT '',
  KEY `id` (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `av_poll`
--

DROP TABLE IF EXISTS `av_poll`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `av_poll` (
  `id` int(9) NOT NULL AUTO_INCREMENT,
  `question` varchar(255) NOT NULL DEFAULT '',
  `choices` text NOT NULL,
  `answers` text NOT NULL,
  `ip` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `av_posts`
--

DROP TABLE IF EXISTS `av_posts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `av_posts` (
  `id` int(9) NOT NULL AUTO_INCREMENT,
  `title` varchar(128) NOT NULL DEFAULT '',
  `post` text NOT NULL,
  `date` int(10) NOT NULL DEFAULT '0',
  `aid` int(9) NOT NULL DEFAULT '0',
  `newtopic` tinyint(1) NOT NULL DEFAULT '0',
  `parent` int(9) NOT NULL DEFAULT '0',
  `ip` varchar(16) NOT NULL DEFAULT '',
  `edit_date` int(9) NOT NULL DEFAULT '0',
  KEY `id` (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=6129 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `av_privmsg`
--

DROP TABLE IF EXISTS `av_privmsg`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `av_privmsg` (
  `id` int(9) NOT NULL AUTO_INCREMENT,
  `date` int(9) NOT NULL DEFAULT '0',
  `a_name` varchar(255) NOT NULL DEFAULT '',
  `r_name` varchar(255) NOT NULL DEFAULT '',
  `a_id` int(9) NOT NULL DEFAULT '0',
  `r_id` int(9) NOT NULL DEFAULT '0',
  `subject` varchar(255) NOT NULL DEFAULT '',
  `text` text NOT NULL,
  `unread` smallint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `av_rpgchar`
--

DROP TABLE IF EXISTS `av_rpgchar`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `av_rpgchar` (
  `cid` int(9) NOT NULL AUTO_INCREMENT,
  `uid` int(9) NOT NULL DEFAULT '0',
  `level` int(9) NOT NULL DEFAULT '1',
  `exp` int(9) NOT NULL DEFAULT '0',
  `mexp` int(9) NOT NULL DEFAULT '0',
  `hp` int(9) NOT NULL DEFAULT '28',
  `mhp` int(9) NOT NULL DEFAULT '28',
  `mp` int(9) NOT NULL DEFAULT '16',
  `mmp` int(9) NOT NULL DEFAULT '16',
  `str` float NOT NULL DEFAULT '5',
  `dex` float NOT NULL DEFAULT '5',
  `int` float NOT NULL DEFAULT '5',
  `vit` float NOT NULL DEFAULT '5',
  `agi` float NOT NULL DEFAULT '5',
  `spr` float NOT NULL DEFAULT '5',
  KEY `id` (`cid`)
) ENGINE=MyISAM AUTO_INCREMENT=1830 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `av_rpgitem_consume`
--

DROP TABLE IF EXISTS `av_rpgitem_consume`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `av_rpgitem_consume` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL DEFAULT '',
  `description` varchar(255) NOT NULL DEFAULT '',
  `img` varchar(255) NOT NULL DEFAULT '',
  `stack` int(1) NOT NULL DEFAULT '0',
  `battle` int(1) NOT NULL DEFAULT '0',
  `mod_a` varchar(255) NOT NULL DEFAULT '',
  `mod_b` varchar(255) NOT NULL DEFAULT '',
  `mod_c` varchar(255) NOT NULL DEFAULT '',
  `mod_d` varchar(255) NOT NULL DEFAULT '',
  `mod_e` varchar(255) NOT NULL DEFAULT '',
  `mod_f` varchar(255) NOT NULL DEFAULT '',
  `script` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `av_rpgjobs`
--

DROP TABLE IF EXISTS `av_rpgjobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `av_rpgjobs` (
  `jid` int(9) NOT NULL AUTO_INCREMENT,
  `jname` varchar(255) NOT NULL DEFAULT '',
  `file` varchar(255) NOT NULL DEFAULT '',
  `parent` int(9) NOT NULL DEFAULT '0',
  `m_str` float NOT NULL DEFAULT '0',
  `m_dex` float NOT NULL DEFAULT '0',
  `m_int` float NOT NULL DEFAULT '0',
  `m_vit` float NOT NULL DEFAULT '0',
  `m_agi` float NOT NULL DEFAULT '0',
  `m_spr` float NOT NULL DEFAULT '0',
  KEY `id` (`jid`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `av_sessions`
--

DROP TABLE IF EXISTS `av_sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `av_sessions` (
  `ip` varchar(32) NOT NULL DEFAULT '',
  `time` int(9) NOT NULL DEFAULT '0',
  PRIMARY KEY (`ip`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `av_topics`
--

DROP TABLE IF EXISTS `av_topics`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `av_topics` (
  `id` int(9) NOT NULL AUTO_INCREMENT,
  `parent` int(9) NOT NULL DEFAULT '0',
  `title` varchar(128) NOT NULL DEFAULT '',
  `description` varchar(255) NOT NULL DEFAULT '',
  `views` int(9) NOT NULL DEFAULT '0',
  `replies` int(9) NOT NULL DEFAULT '0',
  `lastp` int(9) NOT NULL DEFAULT '0',
  `lastp_name` varchar(255) NOT NULL DEFAULT '',
  `lastp_date` int(8) NOT NULL DEFAULT '0',
  `authorid` int(9) NOT NULL DEFAULT '0',
  `date` int(8) NOT NULL DEFAULT '0',
  `status` smallint(2) NOT NULL DEFAULT '0',
  `type` smallint(2) NOT NULL DEFAULT '0',
  KEY `id` (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=4322 DEFAULT CHARSET=latin1 COMMENT='status: 0 nothing, 1 stuck, 2 closed, 3 invisible';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `av_users`
--

DROP TABLE IF EXISTS `av_users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `av_users` (
  `id` int(9) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL DEFAULT '',
  `password` varchar(255) NOT NULL DEFAULT '',
  `gid` int(9) NOT NULL DEFAULT '2',
  `sex` varchar(255) NOT NULL DEFAULT 'male',
  `jointime` int(9) NOT NULL DEFAULT '0',
  `logtime` int(10) NOT NULL DEFAULT '0',
  `ip` varchar(16) NOT NULL DEFAULT '',
  `title` varchar(64) NOT NULL DEFAULT '',
  `email` varchar(64) NOT NULL DEFAULT '',
  `template` varchar(32) NOT NULL DEFAULT '',
  `browser` varchar(64) NOT NULL DEFAULT '',
  `avatar` varchar(64) NOT NULL DEFAULT '',
  `av_x` int(5) NOT NULL DEFAULT '0',
  `av_y` int(5) NOT NULL DEFAULT '0',
  `signature` text NOT NULL,
  `postcount` int(9) NOT NULL DEFAULT '0',
  `money` int(9) NOT NULL DEFAULT '0',
  `job` int(9) NOT NULL DEFAULT '0',
  `dob_year` int(5) NOT NULL DEFAULT '0',
  `dob_month` int(2) NOT NULL DEFAULT '0',
  `dob_day` int(2) NOT NULL DEFAULT '0',
  `prof_website` varchar(255) NOT NULL DEFAULT '',
  `prof_interests` varchar(255) NOT NULL DEFAULT '',
  `prof_location` varchar(255) NOT NULL DEFAULT '',
  `prof_msn` varchar(255) NOT NULL DEFAULT '',
  `prof_aim` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1838 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `leaf_music`
--

DROP TABLE IF EXISTS `leaf_music`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `leaf_music` (
  `id` int(9) NOT NULL AUTO_INCREMENT,
  `user` varchar(255) NOT NULL DEFAULT '',
  `date` int(9) NOT NULL DEFAULT '0',
  `title` varchar(255) NOT NULL DEFAULT '',
  `name` varchar(255) NOT NULL DEFAULT '',
  `artist` varchar(255) NOT NULL DEFAULT '',
  `album` varchar(255) NOT NULL DEFAULT '',
  `year` int(5) NOT NULL DEFAULT '0',
  `fname` varchar(255) NOT NULL DEFAULT '',
  `ext` varchar(255) NOT NULL DEFAULT '',
  `length` varchar(255) NOT NULL DEFAULT '',
  `mins` int(5) NOT NULL DEFAULT '0',
  `secs` int(5) NOT NULL DEFAULT '0',
  `bitrate` varchar(255) NOT NULL DEFAULT '',
  `freq` varchar(255) NOT NULL DEFAULT '',
  `type` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=14659 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2015-07-04 19:07:16
