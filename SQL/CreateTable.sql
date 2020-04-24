# Staff table
create table if not exists staff
(
    sid int auto_increment
        primary key,
    first_name varchar(45) not null,
    last_name varchar(45) not null,
    phone_number varchar(20) not null,
    e_mail varchar(320) not null,
    job_title varchar(30) not null,
    gender tinyint not null comment '1:male 0:female',
    status tinyint default 1 not null comment '1: not working;2: at working;3:quit job',
    password varchar(250),
    constraint e_mail_unique
        unique (e_mail),
    constraint phone_number_unique
        unique (phone_number)
);

# Timetable
create table if not exists shift
(
    id int auto_increment
        primary key,
    staff_sid int not null,
    start_time date not null,
    end_time date not null,
    location varchar(150) null comment ' the place a staff working now',
    remark varchar(300) null comment 'description of this work.(for example: night-shift,day-shift )',
    constraint end_time_UNIQUE
        unique (end_time),
    constraint start_time_UNIQUE
        unique (start_time),
    constraint fk_timetable_Staff
        foreign key (staff_sid) references staff (sid)
);

# Supervisor table
create table if not exists supervisor
(
    SuId int auto_increment
        primary key,
    first_name varchar(100) not null,
    last_name varchar(100) not null,
    e_mail varchar(320) not null,
    password varchar(250) not null comment 'sha256',
    constraint supervisor_e_mail_unique
        unique (e_mail)
);

# Announcement Table
create table if not exists announcement
(
    aid int auto_increment
        primary key,
    title varchar(250) not null,
    content longtext not null,
    suid int null,
    timestamp timestamp default CURRENT_TIMESTAMP not null on update CURRENT_TIMESTAMP,
    constraint announcement_fk
        foreign key (suid) references supervisor (SuId)
            on update cascade on delete set null
);

# Feedback Table
create table if not exists feedback
(
    fid int auto_increment
        primary key,
    from_sid int not null,
    content text not null,
    unread tinyint(1) default 1 null,
    timestamp timestamp default CURRENT_TIMESTAMP null,
    constraint feedback_staff_sid_fk
        foreign key (from_sid) references staff (sid)
            on update cascade on delete cascade
);
