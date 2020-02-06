CREATE TABLE IF NOT EXISTS `timetable`.`shifts` (
  `id` INT NOT NULL,
  `Staff_sid` INT NOT NULL,
  `starttime` DATE NOT NULL,
  `endtime` DATE NOT NULL,
  `location` varchar(150) DEFAULT NULL COMMENT ' the place a staff working now',
  `duration` double NOT NULL COMMENT 'Duration of this time (hour)',
  `date` date NOT NULL,
  `title` varchar(45) DEFAULT NULL COMMENT 'description of this work.(for example: night-shift,day-shift )',
  PRIMARY KEY (`id`),
  INDEX `fk_timetable_Staff_idx` (`Staff_sid` ASC) VISIBLE,
  CONSTRAINT `fk_timetable_Staff`
    FOREIGN KEY (`Staff_sid`)
    REFERENCES `timetable`.`staff` (`sid`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB

CREATE TABLE IF NOT EXISTS `timetable`.`staff` (
  `sid` INT NOT NULL,
  `first_name` VARCHAR(45) NOT NULL,
  `last_name` VARCHAR(45) NOT NULL,
  `gender` tinyint(4) NOT NULL COMMENT '1:male 0:female',
  `status` tinyint(4) NOT NULL DEFAULT '1' COMMENT '1: not working;2: at working;3:quit job',
  PRIMARY KEY (`sid`))
ENGINE = InnoDB