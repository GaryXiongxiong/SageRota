CREATE TABLE IF NOT EXISTS `timetable`.`shifts` (
  `id` INT NOT NULL,
  `staff_sid` INT NOT NULL,
  `start_time` DATE NOT NULL,
  `end_time` DATE NOT NULL,
  `location` varchar(150) DEFAULT NULL COMMENT ' the place a staff working now',
  `remark` varchar(300) DEFAULT NULL COMMENT 'description of this work.(for example: night-shift,day-shift )',
  UNIQUE (`start_time`,`end_time`),
  PRIMARY KEY (`id`),
  INDEX `fk_timetable_Staff_idx` (`staff_sid` ASC) VISIBLE,
  CONSTRAINT `fk_timetable_Staff`
    FOREIGN KEY (`staff_sid`)
    REFERENCES `timetable`.`staff` (`sid`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB

CREATE TABLE IF NOT EXISTS `timetable`.`staff` (
  `sid` INT NOT NULL,
  `first_name` VARCHAR(45) NOT NULL,
  `last_name` VARCHAR(45) NOT NULL,
  `phone_number` VARCHAR(12) NOT NULL,
  `e_mail` VARCHAR(30) NOT NULL,
  `job_title` VARCHAR(30) NOT NULL,
  `gender` tinyint(4) NOT NULL COMMENT '1:male 0:female',
  `status` tinyint(4) NOT NULL DEFAULT '1' COMMENT '1: not working;2: at working;3:quit job',
  PRIMARY KEY (`sid`))
ENGINE = InnoDB
