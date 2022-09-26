CREATE TABLE `field_of_interests` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=53 DEFAULT CHARSET=latin1;

CREATE TABLE `cpu` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `field_id` int(11) NOT NULL,
  `supervisor_id` int(11) NOT NULL,
  `no` int(11) DEFAULT NULL,
  `full` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`),
  KEY `field_id` (`field_id`)
) ENGINE=InnoDB AUTO_INCREMENT=136 DEFAULT CHARSET=latin1;

CREATE TABLE `students` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `regno` varchar(15) NOT NULL,
  `firstname` varchar(100) DEFAULT NULL,
  `lastname` varchar(100) DEFAULT NULL,
  `cpu_id` int(11) NOT NULL DEFAULT 0,
  `phone` varchar(11) NOT NULL DEFAULT '0',
  `email` varchar(60) NOT NULL DEFAULT '@',
  `username` varchar(100) DEFAULT NULL,
  `password` varchar(100) DEFAULT NULL,
  `location` varchar(100) DEFAULT NULL,
  `status` int(11) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=272 DEFAULT CHARSET=latin1;


CREATE TABLE `admin_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `admin_id` int(11) NOT NULL,
  `entry_date` varchar(50) DEFAULT NULL,
  `action` text NOT NULL,
  `level` int(11) NOT NULL DEFAULT 1 COMMENT '1 for low, 2 for medium and 3 for high',
  PRIMARY KEY (`id`),
  KEY `admin_id` (`admin_id`)
) ENGINE=InnoDB AUTO_INCREMENT=185 DEFAULT CHARSET=latin1;

CREATE TABLE `allocation` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `student_id` int(11) NOT NULL,
  `field_id` int(11) NOT NULL,
  `preference` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `student_id` (`student_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;

CREATE TABLE `announcements` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `supervisor_id` int(11) NOT NULL,
  `cpu_id` int(11) NOT NULL,
  `attachment` varchar(64) NOT NULL DEFAULT '0',
  `msg` text DEFAULT NULL,
  `status` int(11) NOT NULL DEFAULT 0,
  `entry_date` varchar(32) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `cpu_id` (`cpu_id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=latin1;

CREATE TABLE `assign_request` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `student_id` int(11) NOT NULL,
  `cpu_id` int(11) NOT NULL,
  `date_entry` varchar(100) NOT NULL,
  `status` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `one_request_at_a_time` (`student_id`,`status`) USING BTREE,
  KEY `cpu_id` (`cpu_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;

CREATE TABLE `change_request` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `student_id` int(11) NOT NULL,
  `supervisor_id` int(11) NOT NULL,
  `reason` varchar(1000) NOT NULL DEFAULT '0',
  `admin` int(11) NOT NULL DEFAULT 0,
  `response` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;



CREATE TABLE `failed_allocate` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `student_id` int(11) NOT NULL,
  `fields` varchar(200) NOT NULL,
  `entry_date` varchar(40) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `student_id` (`student_id`)
) ENGINE=InnoDB AUTO_INCREMENT=41 DEFAULT CHARSET=latin1;



CREATE TABLE `progress` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `student_id` int(11) NOT NULL,
  `chapter` int(11) NOT NULL,
  `link` varchar(40) NOT NULL,
  `response` varchar(1000) NOT NULL DEFAULT '0',
  `date_accepted` varchar(30) NOT NULL DEFAULT '00-00-0000',
  `status` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `chapter` (`chapter`)
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=latin1;


CREATE TABLE `settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category` varchar(20) NOT NULL,
  `value` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=latin1;

CREATE TABLE `special_request` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `student_id` int(11) NOT NULL,
  `supervisor_id` int(11) NOT NULL,
  `field_id` int(11) NOT NULL,
  `date_entry` varchar(100) NOT NULL,
  `status` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `student_id` (`student_id`),
  KEY `field_id` (`field_id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=latin1;

CREATE TABLE `stud_to_group` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `student_id` int(11) NOT NULL,
  `cpu_id` int(11) NOT NULL,
  `attachment` varchar(64) NOT NULL DEFAULT '0',
  `msg` text DEFAULT NULL,
  `status` int(11) NOT NULL DEFAULT 0,
  `entry_date` varchar(32) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `cpu_id` (`cpu_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

CREATE TABLE `stud_to_sup` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `student_id` int(11) NOT NULL,
  `supervisor_id` int(11) NOT NULL,
  `attachment` varchar(64) NOT NULL DEFAULT '0',
  `msg` text DEFAULT NULL,
  `status` int(11) NOT NULL DEFAULT 0,
  `entry_date` varchar(32) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;

CREATE TABLE `student_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `student_id` int(11) NOT NULL,
  `entry_date` varchar(50) DEFAULT NULL,
  `action` text NOT NULL,
  `level` int(11) NOT NULL DEFAULT 1 COMMENT '1 for low, 2 for medium and 3 for high',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=63 DEFAULT CHARSET=latin1;


CREATE TABLE `sup_to_group` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `supervisor_id` int(11) NOT NULL,
  `cpu_id` int(11) NOT NULL,
  `attachment` varchar(64) NOT NULL DEFAULT '0',
  `msg` text DEFAULT NULL,
  `status` int(11) NOT NULL DEFAULT 0,
  `entry_date` varchar(32) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `cpu_id` (`cpu_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `sup_to_stud` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `student_id` int(11) NOT NULL,
  `supervisor_id` int(11) NOT NULL,
  `attachment` varchar(64) NOT NULL DEFAULT '0',
  `msg` text DEFAULT NULL,
  `status` int(11) NOT NULL DEFAULT 0,
  `entry_date` varchar(32) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;

CREATE TABLE `supervisor_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `supervisor_id` int(11) NOT NULL,
  `entry_date` varchar(50) DEFAULT NULL,
  `action` text NOT NULL,
  `level` int(11) NOT NULL DEFAULT 1 COMMENT '1 for low, 2 for medium and 3 for high',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=94 DEFAULT CHARSET=latin1;

CREATE TABLE `supervisors` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title_id` int(11) NOT NULL DEFAULT 0,
  `fileno` varchar(100) DEFAULT NULL,
  `password` varchar(200) DEFAULT NULL,
  `firstname` varchar(100) DEFAULT NULL,
  `lastname` varchar(100) DEFAULT NULL,
  `phone` varchar(11) NOT NULL DEFAULT '0',
  `email` varchar(50) NOT NULL DEFAULT '@',
  `location` varchar(100) DEFAULT NULL,
  `max` int(11) NOT NULL DEFAULT 0,
  `field` int(11) NOT NULL DEFAULT 0,
  `status` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `fileno` (`fileno`)
) ENGINE=MyISAM AUTO_INCREMENT=295 DEFAULT CHARSET=latin1;

CREATE TABLE `titles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(12) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=latin1;

INSERT INTO `titles` (`id`, `name`) VALUES
(1, 'Comr.'),
(2, 'Dr.'),
(3, 'Dr. (Mrs.)'),
(4, 'Miss'),
(5, 'Mr.'),
(6, 'Mrs.'),
(7, 'Prof.'),
(8, 'Prof. (Mrs.)');

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(100) NOT NULL,
  `password` varchar(100) NOT NULL,
  `firstname` varchar(100) NOT NULL,
  `lastname` varchar(100) NOT NULL,
  `email` varchar(50) NOT NULL DEFAULT 'jobowonubi@gmail.com',
  `phone` varchar(11) NOT NULL DEFAULT '08035837211',
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=latin1;

