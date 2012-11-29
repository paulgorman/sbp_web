-- MySQL Schema for SBP Web App Database
-- Presence 2012
--  mysql> grant usage on *.* to username@localhost identified by 'pass';
--  mysql> grant all privileges on sbpweb.* to username@localhost;

-- DANGER!!! XXX --------------
-- DROP ALL TABLES FOR A FRESH START HERE 
DROP TABLE IF EXISTS artists, artistcategories, artistmembers, artistlocations, locations, categories, videos, sitehits, artiststyles, styles;
-- DANGER!!! XXX --------------

CREATE TABLE `sbpweb`.`artists` (
	`aid` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
	`name` VARCHAR(50) NOT NULL,
	`url` VARCHAR(50) NOT NULL UNIQUE,
	`slug` VARCHAR(255) NOT NULL,
	`bio` TEXT,
	`is_active` BOOLEAN NOT NULL DEFAULT 0,
	`is_highlighted` BOOLEAN NOT NULL DEFAULT 0,
	`is_searchable` BOOLEAN NOT NULL DEFAULT 0,
	`last_updated` DATETIME NOT NULL,
	INDEX(`url`),
	INDEX(`slug`)
) ENGINE=InnoDB;

CREATE TABLE `sbpweb`.`artistcategories` (
	`aid` INT NOT NULL,
	`cid` INT NOT NULL,
	INDEX(`aid`),
	INDEX(`cid`)
) ENGINE=InnoDB;

CREATE TABLE `sbpweb`.`artistmembers` (
	`aid` INT NOT NULL,
	`first` VARCHAR(30) NOT NULL,
	`last` VARCHAR(30) NOT NULL,
	`phone` CHAR(10),
	`email` VARCHAR(50),
	INDEX(`aid`)
) ENGINE=InnoDB;

CREATE TABLE `sbpweb`.`artistlocations` (
	`aid` INT NOT NULL, 
	`lid` INT NOT NULL,
	INDEX(`aid`),
	INDEX(`lid`)
) ENGINE=InnoDB;

CREATE TABLE `sbpweb`.`locations` (
	`lid` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
	`city` VARCHAR(40) NOT NULL UNIQUE,
	`state` CHAR(2) NOT NULL,
	INDEX(`city`),
	INDEX(`state`)
) ENGINE=InnoDB;

CREATE TABLE `sbpweb`.`categories` (
	`cid` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
	`category` VARCHAR(40) NOT NULL UNIQUE,
	`url` VARCHAR(50) NOT NULL UNIQUE,
	`description` VARCHAR(250),
	`published` BOOLEAN NOT NULL DEFAULT 1,
	`image_filename` VARCHAR(30) NOT NULL UNIQUE,
	`image_id` CHAR(17) NOT NULL UNIQUE,
	`last_updated` DATETIME NOT NULL,
	INDEX(`url`)
) ENGINE=InnoDB;

CREATE TABLE `sbpweb`.`videos` (
	`vid` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
	`aid` INT NOT NULL,
	`name` VARCHAR(50),
	`published` date,
	INDEX(`aid`)
) ENGINE=InnoDB;

CREATE TABLE `sbpweb`.`sitehits` (
	`hit_datetime` DATETIME NOT NULL ,
	`hit_ip` VARCHAR(16) NOT NULL ,
	`hit_url` VARCHAR(120) NOT NULL ,
	`user_agent` VARCHAR(255) NULL ,
	`referrer` VARCHAR(255) NULL ,
	`sessionid` VARCHAR(255) NOT NULL,
	`sesscount` INT NOT NULL DEFAULT 0,
	INDEX(`hit_datetime`,`hit_ip`,`hit_url`)
) ENGINE=InnoDB;

CREATE TABLE `sbpweb`.`artiststyles` (
	`aid` INT NOT NULL,
	`sid` INT NOT NULL,
	INDEX(`aid`),
	INDEX(`sid`)
) ENGINE=InnoDB;

CREATE TABLE `sbpweb`.`styles` (
	`sid` INT AUTO_INCREMENT NOT NULL PRIMARY KEY,
	`name` varchar(40) NOT NULL UNIQUE,
	INDEX(`name`)
) ENGINE=InnoDB;

