CREATE TABLE IF NOT EXISTS `timetable`. `Staff` (
  `sid` char(12) NOT NULL,
  `name` char(100) NOT NULL,
  `phoneNumber` char(15) NOT NULL,
  `email` char(20) NOT NULL,
  `jobTitle` char(30) NOT NULL,
  PRIMARY KEY (`sid`)
) ENGINE=InnoDB

CREATE TABLE IF NOT EXISTS `timetable`. `Shift` (
  `shiftNumber` int(11) NOT NULL,
  `startDate` date NOT NULL,
  `endDate` date NOT NULL,
  `sid` char(12) NOT NULL,
  PRIMARY KEY (`shiftNumber`),
  KEY `Shift_FK` (`sid`),
  CONSTRAINT `Shift_FK` FOREIGN KEY (`sid`) REFERENCES `Staff` (`sid`)
) ENGINE=InnoDB