SET NAMES UTF8;
SET time_zone = "+00:00";

-- -----------------------------------------------------
-- Schema ultraviolet
-- -----------------------------------------------------
CREATE SCHEMA IF NOT EXISTS `ultraviolet` DEFAULT CHARACTER SET utf8 ;
USE `ultraviolet` ;

-- -----------------------------------------------------
-- Table `ultraviolet`.`uvtr_category`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `ultraviolet`.`uvtr_category` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(60) NOT NULL,
  `position` INT NOT NULL,
  `createdAt` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updatedAt` DATETIME NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;

-- -----------------------------------------------------
-- Table `ultraviolet`.`uvtr_settings`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `ultraviolet`.`uvtr_settings` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(20) NOT NULL,
  `value` VARCHAR(60) NOT NULL,
  `createdAt` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updatedAt` DATETIME NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `ultraviolet`.`uvtr_media`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `ultraviolet`.`uvtr_media` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `title` VARCHAR(150) NULL DEFAULT NULL,
  `path` VARCHAR(255) NOT NULL,
  `video` TINYINT NOT NULL DEFAULT '0',
  `createdAt` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updatedAt` DATETIME NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `deletedAt` DATETIME NULL DEFAULT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `ultraviolet`.`uvtr_person`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `ultraviolet`.`uvtr_person` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `fullName` VARCHAR(50) NULL DEFAULT NULL,
  `tmdbId` INT NULL DEFAULT NULL,
  `pseudo` VARCHAR(25) NULL DEFAULT NULL,
  `email` VARCHAR(130) NULL DEFAULT NULL,
  `emailConfirmed` TINYINT NULL DEFAULT '0',
  `emailKey` VARCHAR(255) NULL DEFAULT NULL,
  `password` VARCHAR(255) NULL DEFAULT NULL,
  `role` ENUM('user', 'moderator', 'admin', 'editor', 'vip') NULL DEFAULT 'user',
  `optin` TINYINT NULL DEFAULT '1',
  `createdAt` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updatedAt` DATETIME NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `deletedAt` DATETIME NULL DEFAULT NULL,
  `mediaId` INT NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_uvtr_person_uvtr_media1_idx` (`mediaId` ASC),
  CONSTRAINT `fk_uvtr_person_uvtr_media1`
    FOREIGN KEY (`mediaId`)
    REFERENCES `ultraviolet`.`uvtr_media` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `ultraviolet`.`uvtr_article`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `ultraviolet`.`uvtr_article` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `title` VARCHAR(100) NOT NULL,
  `description` VARCHAR(255) NOT NULL,
  `content` LONGTEXT NOT NULL,
  `rating` INT NULL DEFAULT NULL,
  `slug` VARCHAR(100) NOT NULL,
  `totalViews` INT NULL DEFAULT '0',
  `titleSeo` VARCHAR(60) NULL DEFAULT NULL,
  `descriptionSeo` VARCHAR(160) NULL DEFAULT NULL,
  `publicationDate` DATETIME NULL DEFAULT NULL,
  `contentUpdatedAt` DATETIME NULL DEFAULT NULL,
  `createdAt` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updatedAt` DATETIME NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `deletedAt` DATETIME NULL DEFAULT NULL,
  `mediaId` INT NOT NULL,
  `personId` INT NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_uvtr_article_uvtr_media1_idx` (`mediaId` ASC),
  INDEX `fk_uvtr_article_uvtr_person1_idx` (`personId` ASC),
  CONSTRAINT `fk_uvtr_article_uvtr_media1`
    FOREIGN KEY (`mediaId`)
    REFERENCES `ultraviolet`.`uvtr_media` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_uvtr_article_uvtr_person1`
    FOREIGN KEY (`personId`)
    REFERENCES `ultraviolet`.`uvtr_person` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `ultraviolet`.`uvtr_category_article`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `ultraviolet`.`uvtr_category_article` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `articleId` INT NOT NULL,
  `categoryId` INT NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_uvtr_category_article_uvtr_article_idx` (`articleId` ASC),
  INDEX `fk_uvtr_category_article_uvtr_category1_idx` (`categoryId` ASC),
  CONSTRAINT `fk_uvtr_category_article_uvtr_article`
    FOREIGN KEY (`articleId`)
    REFERENCES `ultraviolet`.`uvtr_article` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_uvtr_category_article_uvtr_category1`
    FOREIGN KEY (`categoryId`)
    REFERENCES `ultraviolet`.`uvtr_category` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `ultraviolet`.`uvtr_production`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `ultraviolet`.`uvtr_production` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `tmdbId` INT NULL DEFAULT NULL,
  `title` VARCHAR(100) NOT NULL,
  `originalTitle` VARCHAR(100) NULL DEFAULT NULL,
  `releaseDate` VARCHAR(10) NULL DEFAULT NULL,
  `type` ENUM('movie', 'series', 'season', 'episode') NOT NULL,
  `totalSeasons` INT NULL DEFAULT NULL,
  `totalEpisodes` INT NULL DEFAULT NULL,
  `overview` TEXT NULL DEFAULT NULL,
  `runtime` INT NULL DEFAULT NULL,
  `number` TINYINT NULL DEFAULT NULL,
  `createdAt` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updatedAt` DATETIME NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `deletedAt` DATETIME NULL DEFAULT NULL,
  `parentProductionId` INT NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_uvtr_production_uvtr_production1_idx` (`parentProductionId` ASC),
  CONSTRAINT `fk_uvtr_production_uvtr_production1`
    FOREIGN KEY (`parentProductionId`)
    REFERENCES `ultraviolet`.`uvtr_production` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `ultraviolet`.`uvtr_page`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `ultraviolet`.`uvtr_page` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `title` VARCHAR(100) NOT NULL,
  `slug` VARCHAR(100) NOT NULL,
  `position` TINYINT NOT NULL,
  `state` ENUM('draft', 'scheduled', 'published', 'hidden', 'deleted') NOT NULL DEFAULT 'draft',
  `titleSeo` VARCHAR(60) NULL DEFAULT NULL,
  `descriptionSeo` VARCHAR(160) NULL DEFAULT NULL,
  `publicationDate` DATETIME NULL DEFAULT NULL,
  `content` TEXT NULL DEFAULT NULL,
  `createdAt` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updatedAt` DATETIME NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `deletedAt` DATETIME NULL DEFAULT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `ultraviolet`.`uvtr_comment`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `ultraviolet`.`uvtr_comment` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `content` TEXT NULL DEFAULT NULL,
  `createdAt` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updatedAt` DATETIME NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `deletedAt` DATETIME NULL DEFAULT NULL,
  `articleId` INT NOT NULL,
  `personId` INT NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_uvtr_comment_uvtr_article1_idx` (`articleId` ASC),
  INDEX `fk_uvtr_comment_uvtr_person1_idx` (`personId` ASC),
  CONSTRAINT `fk_uvtr_comment_uvtr_article1`
    FOREIGN KEY (`articleId`)
    REFERENCES `ultraviolet`.`uvtr_article` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_uvtr_comment_uvtr_person1`
    FOREIGN KEY (`personId`)
    REFERENCES `ultraviolet`.`uvtr_person` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `ultraviolet`.`uvtr_production_media`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `ultraviolet`.`uvtr_production_media` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `mediaId` INT NOT NULL,
  `productionId` INT NOT NULL,
  `keyArt` TINYINT NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  INDEX `fk_uvtr_production_media_uvtr_media1_idx` (`mediaId` ASC),
  INDEX `fk_uvtr_production_media_uvtr_production1_idx` (`productionId` ASC),
  CONSTRAINT `fk_uvtr_production_media_uvtr_media1`
    FOREIGN KEY (`mediaId`)
    REFERENCES `ultraviolet`.`uvtr_media` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_uvtr_production_media_uvtr_production1`
    FOREIGN KEY (`productionId`)
    REFERENCES `ultraviolet`.`uvtr_production` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `ultraviolet`.`uvtr_production_person`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `ultraviolet`.`uvtr_production_person` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `personId` INT NOT NULL,
  `productionId` INT NOT NULL,
  `department` VARCHAR(15) NOT NULL,
  `character` VARCHAR(100) NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_uvtr_production_person_uvtr_person1_idx` (`personId` ASC),
  INDEX `fk_uvtr_production_person_uvtr_production1_idx` (`productionId` ASC),
  CONSTRAINT `fk_uvtr_production_person_uvtr_person1`
    FOREIGN KEY (`personId`)
    REFERENCES `ultraviolet`.`uvtr_person` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_uvtr_production_person_uvtr_production1`
    FOREIGN KEY (`productionId`)
    REFERENCES `ultraviolet`.`uvtr_production` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `ultraviolet`.`uvtr_production_article`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `ultraviolet`.`uvtr_production_article` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `productionId` INT NOT NULL,
  `articleId` INT NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_uvtr_production_article_uvtr_production1_idx` (`productionId` ASC),
  INDEX `fk_uvtr_production_article_uvtr_article1_idx` (`articleId` ASC),
  CONSTRAINT `fk_uvtr_production_article_uvtr_production1`
    FOREIGN KEY (`productionId`)
    REFERENCES `ultraviolet`.`uvtr_production` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_uvtr_production_article_uvtr_article1`
    FOREIGN KEY (`articleId`)
    REFERENCES `ultraviolet`.`uvtr_article` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;

-- -----------------------------------------------------
-- Insert default images in database
-- -----------------------------------------------------
INSERT INTO `ultraviolet`.`uvtr_media` (`title`, `path`) VALUES ("Utilisateur - Image par défaut", "/src/img/default_user.jpg");
INSERT INTO `ultraviolet`.`uvtr_media` (`title`, `path`) VALUES ("Article - Image par défaut", "/src/img/default_article.png");

-- -----------------------------------------------------
-- Insert example categories
-- -----------------------------------------------------
INSERT INTO `ultraviolet`.`uvtr_category` (`name`, `position`) VALUES ("Films", 1);
INSERT INTO `ultraviolet`.`uvtr_category` (`name`, `position`) VALUES ("Séries", 2);
INSERT INTO `ultraviolet`.`uvtr_category` (`name`, `position`) VALUES ("Actualités", 3);
INSERT INTO `ultraviolet`.`uvtr_category` (`name`, `position`) VALUES ("Critiques", 4);

-- -----------------------------------------------------
-- Insert example page
-- -----------------------------------------------------
INSERT INTO `ultraviolet`.`uvtr_page` (`title`, `slug`, `position`, `state`, `titleSeo`, `descriptionSeo`, `content`)
VALUES ("Ma première page", "ma-premiere-page", 1, "published", "Bienvenue chez moi", "Bienvenue sur la première page de mon site", "Ceci est une page d'exemple");