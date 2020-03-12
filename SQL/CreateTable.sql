
CREATE TABLE IF NOT EXISTS `timetable`.`staff` (
  `sid` int NOT NULL AUTO_INCREMENT,
  `first_name` VARCHAR(45) NOT NULL,
  `last_name` VARCHAR(45) NOT NULL,
  `phone_number` VARCHAR(20) NOT NULL,
  `e_mail` VARCHAR(320) NOT NULL,
  `job_title` VARCHAR(30) NOT NULL,
  `gender` tinyint(4) NOT NULL COMMENT '1:male 0:female',
  `status` tinyint(4) NOT NULL DEFAULT '1' COMMENT '1: not working;2: at working;3:quit job',
  PRIMARY KEY (`sid`),
  UNIQUE KEY `phone_number_UNIQUE` (`phone_number`),
  UNIQUE KEY `e_mail_UNIQUE` (`e_mail`))
ENGINE = InnoDB;

CREATE TABLE IF NOT EXISTS `timetable`.`shift` (
  `id` int NOT NULL AUTO_INCREMENT,
  `staff_sid` int(11) NOT NULL,
  `start_time` DATE NOT NULL,
  `end_time` DATE NOT NULL,
  `location` varchar(150) DEFAULT NULL COMMENT ' the place a staff working now',
  `remark` varchar(300) DEFAULT NULL COMMENT 'description of this work.(for example: night-shift,day-shift )',
  PRIMARY KEY (`id`),
  UNIQUE KEY `start_time_UNIQUE` (`start_time`),
  UNIQUE KEY `end_time_UNIQUE` (`end_time`),
  INDEX `fk_timetable_Staff_idx` (`staff_sid` ASC) VISIBLE,
  CONSTRAINT `fk_timetable_Staff`
    FOREIGN KEY (`staff_sid`)
    REFERENCES `timetable`.`staff` (`sid`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

-- auto-generated definition
create table supervisor
(
    SuId       int auto_increment
        primary key,
    first_name varchar(100)  not null,
    last_name  varchar(100)  not null,
    e_mail     varchar(320)  not null,
    password   varchar(250)  not null comment 'sha256',
    level      int default 1 not null,
    UNIQUE KEY `e_mail_UNIQUE` (`e_mail`)
)
ENGINE = InnoDB;


