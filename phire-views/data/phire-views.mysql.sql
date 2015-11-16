--
-- Views Module MySQL Database for Phire CMS 2.0
--

-- --------------------------------------------------------

SET FOREIGN_KEY_CHECKS = 0;

--
-- Table structure for table `views`
--

DROP TABLE IF EXISTS `[{prefix}]views`;
CREATE TABLE IF NOT EXISTS `[{prefix}]views` (
  `id` int(16) NOT NULL AUTO_INCREMENT,
  `name` varchar(255),
  PRIMARY KEY (`id`),
  INDEX `view_name` (`name`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=53001 ;

-- --------------------------------------------------------

SET FOREIGN_KEY_CHECKS = 1;
