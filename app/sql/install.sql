SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

DROP TABLE IF EXISTS `questions`;
CREATE TABLE `questions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `quiz_one` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `quiz_two` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `quiz_tree` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `quiz_four` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_8ADC54D5A76ED395` (`user_id`),
  CONSTRAINT `FK_8ADC54D5A76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


DROP TABLE IF EXISTS `user`;
CREATE TABLE `user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fb_id` bigint(20) DEFAULT NULL,
  `email` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `firstname` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `lastname` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `role` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `gender` int(11) DEFAULT NULL,
  `street` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `strno` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `zip` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `city` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `day` int(11) DEFAULT NULL,
  `month` int(11) DEFAULT NULL,
  `year` int(11) DEFAULT NULL,
  `birthday` date DEFAULT NULL,
  `access_token` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_8D93D649E7927C74` (`email`),
  KEY `fbId_idx` (`fb_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


-- 2014-11-09 23:44:04
