CREATE TABLE IF NOT EXISTS `timetable`.`shifts` (
  `id` INT NOT NULL,
  `Staff_sid` INT NOT NULL,
  `starttime` DATE NOT NULL,
  `endtime` DATE NOT NULL,
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
  PRIMARY KEY (`sid`))
ENGINE = InnoDB