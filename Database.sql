-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server versie:                5.6.13 - MySQL Community Server (GPL)
-- Server OS:                    Win32
-- HeidiSQL Versie:              9.1.0.4867
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;

-- Databasestructuur van accountdb wordt geschreven
CREATE DATABASE IF NOT EXISTS `accountdb` /*!40100 DEFAULT CHARACTER SET latin1 */;
USE `accountdb`;


-- Structuur van  tabel accountdb.claims wordt geschreven
CREATE TABLE IF NOT EXISTS `claims` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `Claim` varchar(255) NOT NULL,
  PRIMARY KEY (`Id`),
  UNIQUE KEY `Claim` (`Claim`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Data exporteren was gedeselecteerd


-- Structuur van  tabel accountdb.roles wordt geschreven
CREATE TABLE IF NOT EXISTS `roles` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `Role` varchar(255) NOT NULL,
  PRIMARY KEY (`Id`),
  UNIQUE KEY `Role` (`Role`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Data exporteren was gedeselecteerd


-- Structuur van  tabel accountdb.users wordt geschreven
CREATE TABLE IF NOT EXISTS `users` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `Username` varchar(255) NOT NULL,
  `Email` varchar(255) NOT NULL,
  `EmailConfirmed` tinyint(1) NOT NULL DEFAULT '0',
  `PasswordHash` text NOT NULL,
  `RegisterDate` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `Deleted` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`Id`),
  UNIQUE KEY `Username` (`Username`),
  UNIQUE KEY `Email` (`Email`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ROW_FORMAT=COMPACT;

-- Data exporteren was gedeselecteerd


-- Structuur van  tabel accountdb.user_claims wordt geschreven
CREATE TABLE IF NOT EXISTS `user_claims` (
  `User_Id` int(11) NOT NULL,
  `Claim_Id` int(11) NOT NULL,
  `Claim_Value` text NOT NULL,
  PRIMARY KEY (`User_Id`,`Claim_Id`),
  KEY `FK_ClaimUserId` (`User_Id`),
  KEY `FK_ClaimId` (`Claim_Id`),
  CONSTRAINT `FK_ClaimId` FOREIGN KEY (`Claim_Id`) REFERENCES `claims` (`Id`),
  CONSTRAINT `FK_ClaimUserId` FOREIGN KEY (`User_Id`) REFERENCES `users` (`Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Data exporteren was gedeselecteerd


-- Structuur van  tabel accountdb.user_roles wordt geschreven
CREATE TABLE IF NOT EXISTS `user_roles` (
  `User_Id` int(11) NOT NULL,
  `Role_Id` int(11) NOT NULL,
  PRIMARY KEY (`User_Id`,`Role_Id`),
  KEY `FK_RoleId` (`Role_Id`),
  KEY `FK_RoleUserId` (`User_Id`),
  CONSTRAINT `FK_RoleId` FOREIGN KEY (`Role_Id`) REFERENCES `roles` (`Id`),
  CONSTRAINT `FK_RoleUserId` FOREIGN KEY (`User_Id`) REFERENCES `users` (`Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Data exporteren was gedeselecteerd
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
