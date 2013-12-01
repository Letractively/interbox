-- phpMyAdmin SQL Dump
-- version 3.1.3
-- http://www.phpmyadmin.net
--
-- 主机: localhost
-- 生成日期: 2011 年 01 月 23 日 04:25
-- 服务器版本: 5.1.32
-- PHP 版本: 5.2.9-1

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- 数据库: `ibc1test`
--

-- --------------------------------------------------------

--
-- 表的结构 `ibc1_clgcatalogtest_admin`
--

CREATE TABLE IF NOT EXISTS `ibc1_clgcatalogtest_admin` (
  `admID` int(10) NOT NULL AUTO_INCREMENT,
  `admCatalogID` int(10) NOT NULL,
  `admUID` varchar(255) NOT NULL,
  PRIMARY KEY (`admID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- 导出表中的数据 `ibc1_clgcatalogtest_admin`
--


-- --------------------------------------------------------

--
-- 表的结构 `ibc1_clgcatalogtest_catalog`
--

CREATE TABLE IF NOT EXISTS `ibc1_clgcatalogtest_catalog` (
  `clgID` int(10) NOT NULL AUTO_INCREMENT,
  `clgName` varchar(255) NOT NULL,
  `clgOrdinal` int(10) DEFAULT NULL,
  `clgUID` varchar(255) DEFAULT NULL,
  `clgParentID` int(10) NOT NULL,
  `clgGID` int(10) NOT NULL DEFAULT '0',
  `clgAdminGrade` int(10) NOT NULL,
  PRIMARY KEY (`clgID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- 导出表中的数据 `ibc1_clgcatalogtest_catalog`
--


-- --------------------------------------------------------

--
-- 表的结构 `ibc1_clgcatalogtest_content`
--

CREATE TABLE IF NOT EXISTS `ibc1_clgcatalogtest_content` (
  `cntID` int(10) NOT NULL AUTO_INCREMENT,
  `cntOrdinal` int(10) DEFAULT '0',
  `cntName` varchar(255) NOT NULL,
  `cntCatalogID` int(10) NOT NULL,
  `cntAuthor` varchar(255) DEFAULT NULL,
  `cntKeywords` varchar(256) DEFAULT NULL,
  `cntTimeCreated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `cntTimeUpdated` timestamp NULL DEFAULT NULL,
  `cntTimeVisited` timestamp NULL DEFAULT NULL,
  `cntPointValue` int(10) NOT NULL DEFAULT '0',
  `cntUID` varchar(255) NOT NULL,
  `cntVisitCount` int(10) NOT NULL DEFAULT '0',
  `cntAdminGrade` int(10) DEFAULT NULL,
  `cntVisitGrade` int(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`cntID`),
  KEY `parent` (`cntCatalogID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- 导出表中的数据 `ibc1_clgcatalogtest_content`
--


-- --------------------------------------------------------

--
-- 表的结构 `ibc1_resresourcedbtest_file`
--

CREATE TABLE IF NOT EXISTS `ibc1_resresourcedbtest_file` (
  `filID` int(10) NOT NULL AUTO_INCREMENT,
  `filName` varchar(63) NOT NULL,
  `filExtName` varchar(8) DEFAULT NULL,
  `filSize` int(8) NOT NULL DEFAULT '0',
  `filTime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `filType` varchar(63) DEFAULT NULL,
  `filUID` varchar(63) DEFAULT NULL,
  `filData` mediumblob,
  PRIMARY KEY (`filID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- 导出表中的数据 `ibc1_resresourcedbtest_file`
--


-- --------------------------------------------------------

--
-- 表的结构 `ibc1_resresourcefstest_file`
--

CREATE TABLE IF NOT EXISTS `ibc1_resresourcefstest_file` (
  `filID` int(10) NOT NULL AUTO_INCREMENT,
  `filName` varchar(63) NOT NULL,
  `filExtName` varchar(8) DEFAULT NULL,
  `filSize` int(8) NOT NULL DEFAULT '0',
  `filTime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `filType` varchar(63) DEFAULT NULL,
  `filUID` varchar(63) DEFAULT NULL,
  `filDir` int(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`filID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- 导出表中的数据 `ibc1_resresourcefstest_file`
--


-- --------------------------------------------------------

--
-- 表的结构 `ibc1_service`
--

CREATE TABLE IF NOT EXISTS `ibc1_service` (
  `ServiceName` varchar(64) NOT NULL,
  `ServiceType` varchar(5) NOT NULL,
  PRIMARY KEY (`ServiceName`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- 导出表中的数据 `ibc1_service`
--

INSERT INTO `ibc1_service` (`ServiceName`, `ServiceType`) VALUES
('usertest', 'usr'),
('catalogtest', 'clg'),
('settingtest', 'set'),
('resourcefstest', 'res'),
('resourcedbtest', 'res');

-- --------------------------------------------------------

--
-- 表的结构 `ibc1_service_resource`
--

CREATE TABLE IF NOT EXISTS `ibc1_service_resource` (
  `ServiceName` varchar(63) NOT NULL,
  `Root` varchar(255) NOT NULL,
  `FileTypeList` varchar(256) NOT NULL,
  `MaxFileSize` int(8) NOT NULL DEFAULT '1048576',
  `UserService` varchar(63) NOT NULL,
  PRIMARY KEY (`ServiceName`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- 导出表中的数据 `ibc1_service_resource`
--

INSERT INTO `ibc1_service_resource` (`ServiceName`, `Root`, `FileTypeList`, `MaxFileSize`, `UserService`) VALUES
('resourcefstest', 'd:/resroot/', 'txt htm html gif jpg png bmp rar zip', 1048576, 'usertest'),
('resourcedbtest', '', 'txt htm html gif jpg png bmp rar zip', 1048576, 'usertest');

-- --------------------------------------------------------

--
-- 表的结构 `ibc1_service_setting`
--

CREATE TABLE IF NOT EXISTS `ibc1_service_setting` (
  `ServiceName` varchar(63) NOT NULL,
  `MatchService` varchar(63) NOT NULL,
  `MatchTable` varchar(63) NOT NULL,
  `MatchField` varchar(63) NOT NULL,
  `MatchType` int(1) NOT NULL,
  `ValueType` int(1) NOT NULL,
  `ValueLength` int(5) NOT NULL,
  `TimeIncluded` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`ServiceName`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- 导出表中的数据 `ibc1_service_setting`
--

INSERT INTO `ibc1_service_setting` (`ServiceName`, `MatchService`, `MatchTable`, `MatchField`, `MatchType`, `ValueType`, `ValueLength`, `TimeIncluded`) VALUES
('settingtest', 'catalogtest', 'IBC1_clgcatalogtest_content', 'cntid', 0, 0, 0, 0);

-- --------------------------------------------------------

--
-- 表的结构 `ibc1_setsettingtest_settinglist`
--

CREATE TABLE IF NOT EXISTS `ibc1_setsettingtest_settinglist` (
  `setID` int(10) NOT NULL AUTO_INCREMENT,
  `setMatchValue` int(10) DEFAULT NULL,
  `setName` varchar(255) NOT NULL,
  `setValue` int(10) DEFAULT NULL,
  PRIMARY KEY (`setID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- 导出表中的数据 `ibc1_setsettingtest_settinglist`
--


-- --------------------------------------------------------

--
-- 表的结构 `ibc1_usrusertest_grade`
--

CREATE TABLE IF NOT EXISTS `ibc1_usrusertest_grade` (
  `grdGrade` int(2) NOT NULL,
  `grdName` varchar(255) NOT NULL,
  PRIMARY KEY (`grdGrade`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- 导出表中的数据 `ibc1_usrusertest_grade`
--

INSERT INTO `ibc1_usrusertest_grade` (`grdGrade`, `grdName`) VALUES
(1, 'normal user'),
(2, 'advanced user'),
(3, 'administrator');

-- --------------------------------------------------------

--
-- 表的结构 `ibc1_usrusertest_group`
--

CREATE TABLE IF NOT EXISTS `ibc1_usrusertest_group` (
  `grpID` int(10) NOT NULL AUTO_INCREMENT,
  `grpName` varchar(255) NOT NULL,
  `grpOwner` varchar(255) NOT NULL,
  `grpType` int(2) NOT NULL DEFAULT '0',
  PRIMARY KEY (`grpID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- 导出表中的数据 `ibc1_usrusertest_group`
--


-- --------------------------------------------------------

--
-- 表的结构 `ibc1_usrusertest_groupuser`
--

CREATE TABLE IF NOT EXISTS `ibc1_usrusertest_groupuser` (
  `gpuID` int(10) NOT NULL AUTO_INCREMENT,
  `gpuUID` varchar(255) NOT NULL,
  `gpuGID` int(10) NOT NULL,
  PRIMARY KEY (`gpuID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- 导出表中的数据 `ibc1_usrusertest_groupuser`
--


-- --------------------------------------------------------

--
-- 表的结构 `ibc1_usrusertest_user`
--

CREATE TABLE IF NOT EXISTS `ibc1_usrusertest_user` (
  `usrUID` varchar(255) NOT NULL,
  `usrPWD` varchar(255) NOT NULL,
  `usrFace` varchar(255) DEFAULT NULL,
  `usrNickName` varchar(255) DEFAULT NULL,
  `usrGrade` int(2) NOT NULL DEFAULT '1',
  `usrPoints` int(10) NOT NULL DEFAULT '0',
  `usrLoginCount` int(10) NOT NULL DEFAULT '0',
  `usrLoginIP` varchar(50) DEFAULT NULL,
  `usrLoginTime` timestamp NULL DEFAULT NULL,
  `usrVisitTime` timestamp NULL DEFAULT NULL,
  `usrRegisterTime` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `usrIsOnline` int(1) NOT NULL DEFAULT '0',
  `usrIsUserAdmin` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`usrUID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- 导出表中的数据 `ibc1_usrusertest_user`
--

INSERT INTO `ibc1_usrusertest_user` (`usrUID`, `usrPWD`, `usrFace`, `usrNickName`, `usrGrade`, `usrPoints`, `usrLoginCount`, `usrLoginIP`, `usrLoginTime`, `usrVisitTime`, `usrRegisterTime`, `usrIsOnline`, `usrIsUserAdmin`) VALUES
('guzhiji', 'd4aace6db39c0a6aab769744fd66b258d', NULL, NULL, 3, 0, 0, NULL, NULL, NULL, '0000-00-00 00:00:00', 0, 2);
