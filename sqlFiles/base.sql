use `rockefellercss`;

DROP TABLE IF EXISTS `correspondence`;
CREATE TABLE `correspondence` (
  `ID` int(30) unsigned NOT NULL AUTO_INCREMENT,
  `prefix` varchar(30) DEFAULT NULL,
  `first` varchar(50) DEFAULT NULL,
  `middle` varchar(50) DEFAULT NULL,
  `last` varchar(50) DEFAULT NULL,
  `suffix` varchar(30) DEFAULT NULL,
  `appellation` varchar(50) DEFAULT NULL,
  `title` varchar(30) DEFAULT NULL,
  `org` varchar(30) DEFAULT NULL,
  `addr1` varchar(100),
  `addr2` varchar(50) DEFAULT NULL,
  `addr3` varchar(30) DEFAULT NULL,
  `addr4` varchar(30) DEFAULT NULL,
  `city` varchar(30),
  `state` varchar(30),
  `zip` varchar(30),
  `country` varchar(30),
  `in_id` varchar(30),
  `in_type` varchar(30) DEFAULT NULL,
  `in_method` varchar(30) DEFAULT NULL,
  `in_date` varchar(30) DEFAULT NULL,
  `in_topic` varchar(50) DEFAULT NULL,
  `in_text` varchar(100) DEFAULT NULL,
  `in_document_name` varchar(100) DEFAULT NULL,
  `in_fillin` varchar(30) DEFAULT NULL,
  `out_id` varchar(30),
  `out_type` varchar(30) DEFAULT NULL,
  `out_method` varchar(30) DEFAULT NULL,
  `out_date` varchar(30) DEFAULT NULL,
  `out_topic` varchar(50) DEFAULT NULL,
  `out_text` varchar(100) DEFAULT NULL,
  `out_document_name` varchar(100) DEFAULT NULL,
  `out_fillin` varchar(100) DEFAULT NULL,
  `publicAccess` tinyint(3) unsigned NOT NULL DEFAULT 1,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=latin1;


DROP TABLE IF EXISTS `formLetters`;
CREATE TABLE `formLetters` (
  `ID` int(30) unsigned NOT NULL AUTO_INCREMENT,
  `letterID` varchar(50) NOT NULL,
  `descr` TEXT DEFAULT NULL,
  `docName` varchar(50) DEFAULT NULL,
  `creatnDate` varchar(50) DEFAULT NULL,
  `topics` TEXT DEFAULT NULL,
  `fillInFields` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=latin1;
