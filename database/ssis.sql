-- phpMyAdmin SQL Dump
-- version 4.9.5
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Sep 11, 2020 at 09:30 PM
-- Server version: 10.4.11-MariaDB
-- PHP Version: 7.3.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `id13900578_project`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin_logs`
--

CREATE TABLE `admin_logs` (
  `id` int(11) NOT NULL,
  `admin_id` int(11) NOT NULL,
  `entry_date` varchar(50) DEFAULT NULL,
  `action` text NOT NULL,
  `level` int(11) NOT NULL DEFAULT 1 COMMENT '1 for low, 2 for medium and 3 for high'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `allocation`
--

CREATE TABLE `allocation` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `field_id` int(11) NOT NULL,
  `preference` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `allocation`
--

INSERT INTO `allocation` (`id`, `student_id`, `field_id`, `preference`) VALUES
(1, 50, 3, 1),
(2, 20, 9, 1),
(3, 18, 10, 1),
(4, 77, 1, 1),
(5, 72, 2, 1),
(6, 61, 11, 1),
(7, 44, 4, 1),
(8, 94, 1, 1),
(9, 75, 7, 1),
(10, 52, 9, 1),
(11, 79, 13, 1),
(12, 55, 4, 1),
(13, 134, 1, 1),
(14, 69, 1, 1),
(15, 133, 5, 1),
(16, 105, 4, 1),
(17, 47, 4, 1),
(18, 122, 16, 1),
(19, 83, 3, 1),
(20, 117, 19, 2),
(21, 130, 2, 1),
(22, 81, 1, 1),
(23, 129, 14, 1),
(24, 54, 4, 1),
(36, 82, 6, 2),
(26, 34, 1, 1),
(27, 107, 17, 1),
(28, 41, 3, 1),
(29, 6, 9, 1),
(30, 10, 7, 1),
(31, 85, 4, 1),
(37, 114, 10, 1),
(33, 96, 3, 1),
(34, 76, 4, 1),
(35, 17, 6, 1),
(38, 119, 1, 1),
(39, 9, 7, 1),
(40, 78, 4, 1),
(41, 100, 1, 1),
(42, 46, 9, 1),
(43, 37, 4, 1),
(44, 12, 10, 1),
(45, 73, 4, 1),
(46, 4, 2, 1),
(47, 16, 4, 1),
(48, 13, 7, 1),
(49, 87, 4, 1),
(50, 59, 7, 2),
(51, 19, 6, 1),
(52, 31, 8, 1),
(53, 103, 8, 1),
(54, 7, 7, 3),
(55, 104, 18, 2),
(56, 65, 1, 1),
(57, 57, 10, 1),
(58, 101, 7, 1),
(59, 80, 3, 2),
(60, 123, 3, 2),
(61, 2, 2, 3),
(62, 97, 9, 3),
(63, 106, 18, 3),
(64, 67, 3, 1),
(65, 88, 9, 2),
(66, 45, 9, 1),
(67, 64, 10, 2),
(68, 110, 2, 1),
(69, 30, 2, 2),
(70, 71, 13, 1),
(71, 126, 2, 1);

-- --------------------------------------------------------

--
-- Table structure for table `announcements`
--

CREATE TABLE `announcements` (
  `id` int(11) NOT NULL,
  `supervisor_id` int(11) NOT NULL,
  `cpu_id` int(11) NOT NULL,
  `attachment` varchar(64) NOT NULL DEFAULT '0',
  `msg` text DEFAULT NULL,
  `status` int(11) NOT NULL DEFAULT 0,
  `entry_date` varchar(32) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `assign_request`
--

CREATE TABLE `assign_request` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `cpu_id` int(11) NOT NULL,
  `date_entry` varchar(100) NOT NULL,
  `status` int(11) NOT NULL DEFAULT 0
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `change_request`
--

CREATE TABLE `change_request` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `supervisor_id` int(11) NOT NULL,
  `reason` varchar(1000) NOT NULL DEFAULT '0',
  `admin` int(11) NOT NULL DEFAULT 0,
  `response` int(11) NOT NULL DEFAULT 0
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `cpu`
--

CREATE TABLE `cpu` (
  `id` int(11) NOT NULL,
  `field_id` int(11) NOT NULL,
  `supervisor_id` int(11) NOT NULL,
  `no` int(11) DEFAULT NULL,
  `full` int(11) NOT NULL DEFAULT 0
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `cpu`
--

INSERT INTO `cpu` (`id`, `field_id`, `supervisor_id`, `no`, `full`) VALUES
(1, 1, 1, 5, 5),
(2, 1, 2, 3, 3),
(3, 2, 3, 4, 3),
(4, 2, 4, 5, 4),
(5, 3, 5, 7, 7),
(6, 4, 6, 5, 5),
(7, 4, 7, 6, 6),
(8, 6, 8, 5, 3),
(9, 7, 9, 7, 7),
(10, 12, 10, 4, 0),
(11, 13, 11, 5, 1),
(12, 9, 12, 7, 7),
(13, 10, 13, 5, 5),
(14, 14, 14, 5, 0),
(15, 11, 15, 6, 1),
(26, 15, 15, 3, 0),
(25, 8, 15, 2, 2),
(18, 13, 5, 4, 1),
(20, 5, 6, 1, 1),
(21, 16, 8, 1, 1),
(22, 19, 2, 1, 1),
(23, 14, 10, 1, 1),
(24, 17, 11, 1, 1),
(27, 18, 14, 3, 2);

-- --------------------------------------------------------

--
-- Table structure for table `failed_allocate`
--

CREATE TABLE `failed_allocate` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `fields` varchar(200) NOT NULL,
  `entry_date` varchar(40) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `field_of_interests`
--

CREATE TABLE `field_of_interests` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `field_of_interests`
--

INSERT INTO `field_of_interests` (`id`, `name`) VALUES
(1, 'Machine Learning'),
(2, 'Robotics'),
(3, 'Mobile Development'),
(4, 'Web Development'),
(5, 'Data Structure and Algorithms'),
(6, 'Big Data'),
(7, 'Data Mining'),
(8, 'Artificial Intelligence'),
(9, 'Cloud Computing'),
(10, 'Hardware'),
(11, 'Text Mining'),
(12, 'Search Based Software Engineering'),
(13, 'Optimization'),
(14, 'Empirical Software Engineering'),
(15, 'Ubiquitous Computing'),
(16, 'Software Testing'),
(17, 'Simulation and Modelling'),
(18, 'Bioinformatics'),
(19, 'Human Computer Interaction');

-- --------------------------------------------------------

--
-- Table structure for table `progress`
--

CREATE TABLE `progress` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `chapter` int(11) NOT NULL,
  `link` varchar(40) NOT NULL,
  `response` varchar(1000) NOT NULL DEFAULT '0',
  `date_accepted` varchar(30) NOT NULL DEFAULT '00-00-0000',
  `status` int(11) NOT NULL DEFAULT 0
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `id` int(11) NOT NULL,
  `category` varchar(20) NOT NULL,
  `value` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`id`, `category`, `value`) VALUES
(1, 'student_login', 1),
(2, 'supervisor_login', 1),
(3, 'supervisor_reg', 1),
(4, 'student_reg', 1),
(5, 'maintenance', 0);

-- --------------------------------------------------------

--
-- Table structure for table `special_request`
--

CREATE TABLE `special_request` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `supervisor_id` int(11) NOT NULL,
  `field_id` int(11) NOT NULL,
  `date_entry` varchar(100) NOT NULL,
  `status` int(11) NOT NULL DEFAULT 0
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE `students` (
  `id` int(11) NOT NULL,
  `regno` varchar(15) NOT NULL,
  `firstname` varchar(100) DEFAULT NULL,
  `lastname` varchar(100) DEFAULT NULL,
  `cpu_id` int(11) NOT NULL DEFAULT 0,
  `phone` varchar(11) NOT NULL DEFAULT '0',
  `email` varchar(60) NOT NULL DEFAULT '@',
  `username` varchar(100) DEFAULT NULL,
  `password` varchar(100) DEFAULT NULL,
  `location` varchar(100) DEFAULT NULL,
  `status` int(11) NOT NULL DEFAULT 1
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`id`, `regno`, `firstname`, `lastname`, `cpu_id`, `phone`, `email`, `username`, `password`, `location`, `status`) VALUES
(1, '16/52HA002', 'ABDULLATEEF', 'ABDULSAMAD', 0, '12345678765', 'jobowonubi@gmail.com', 'jojo', 'jojoo', 'joono', 1),
(2, '16/52HA003', 'ABDULSALAM', 'ABUBAKAR', 4, '12345678766', 'jobowonubi@gmail.com', 'jojo', 'jojoo', 'joono', 1),
(3, '16/52HA004', 'ABDULSALAM', 'HAWAU', 0, '12345678767', 'jobowonubi@gmail.com', 'jojo', 'jojoo', 'joono', 1),
(4, '16/52HA005', 'ABEGUNDE', 'SAMUEL', 3, '12345678768', 'jobowonubi@gmail.com', 'jojo', 'jojoo', 'joono', 1),
(5, '16/52HA006', 'ABIDAKUN', 'OLUWATOMIWA', 0, '12345678769', 'jobowonubi@gmail.com', 'jojo', 'jojoo', 'joono', 1),
(6, '16/52HA007', 'ABRAHAM', 'CELESTINE', 12, '12345678770', 'jobowonubi@gmail.com', 'jojo', 'jojoo', 'joono', 1),
(7, '17/52HA110', 'ABUBAKAR', 'ISHOLA', 9, '12345678771', 'jobowonubi@gmail.com', 'jojo', 'jojoo', 'joono', 1),
(8, '16/30GM001', 'ABUBAKAR', 'NURUDEEN', 0, '12345678772', 'jobowonubi@gmail.com', 'jojo', 'jojoo', 'joono', 1),
(9, '16/52HA008', 'ADEBAYO', 'DEBORAH', 9, '12345678773', 'jobowonubi@gmail.com', 'jojo', 'jojoo', 'joono', 1),
(10, '16/52HA009', 'ADEBAYO', 'OLADAYO', 9, '12345678774', 'jobowonubi@gmail.com', 'jojo', 'jojoo', 'joono', 1),
(11, '16/52HA010', 'ADEBIYI', 'EYITAYO', 0, '12345678775', 'jobowonubi@gmail.com', 'jojo', 'jojoo', 'joono', 1),
(12, '16/52HA011', 'ADEBOYE', 'WAREEZ', 13, '12345678776', 'jobowonubi@gmail.com', 'jojo', 'jojoo', 'joono', 1),
(13, '16/52HA012', 'ADEDAYO', 'OYINJESU', 9, '12345678777', 'jobowonubi@gmail.com', 'jojo', 'jojoo', 'joono', 1),
(14, '15/52HA004', 'ADEGOKE', 'KAYODE', 0, '12345678778', 'jobowonubi@gmail.com', 'jojo', 'jojoo', 'joono', 1),
(15, '16/52HA014', 'ADEGOKE', 'OLUWASEUN', 0, '12345678779', 'jobowonubi@gmail.com', 'jojo', 'jojoo', 'joono', 1),
(16, '16/52HA015', 'ADEKEYE', 'ADEBOYE', 6, '12345678780', 'jobowonubi@gmail.com', 'jojo', 'jojoo', 'joono', 1),
(17, '17/52HA111', 'ADEKOLA', 'SEKINAT', 8, '12345678781', 'jobowonubi@gmail.com', 'jojo', 'jojoo', 'joono', 1),
(18, '16/52HA016', 'ADELABU', 'SIMBIAT', 13, '12345678782', 'jobowonubi@gmail.com', 'jojo', 'jojoo', 'joono', 1),
(19, '17/52HA112', 'ADEMILOKUN', 'BOLUWATIFE', 8, '12345678783', 'jobowonubi@gmail.com', 'jojo', 'jojoo', 'joono', 1),
(20, '16/52HA017', 'ADENIYI', 'RIDWANULLAHI', 12, '12345678784', 'jobowonubi@gmail.com', 'jojo', 'jojoo', 'joono', 1),
(21, '16/52HA018', 'ADEOTI', 'DAMILOLA', 0, '12345678785', 'jobowonubi@gmail.com', 'jojo', 'jojoo', 'joono', 1),
(22, '16/52HA096', 'ADEPOJU', 'PETER', 0, '12345678786', 'jobowonubi@gmail.com', 'jojo', 'jojoo', 'joono', 1),
(23, '16/52HA019', 'ADERINTO', 'MUQEETAT', 0, '12345678787', 'jobowonubi@gmail.com', 'jojo', 'jojoo', 'joono', 1),
(24, '16/52HA020', 'ADESHINA', 'MUHEEZ', 0, '12345678788', 'jobowonubi@gmail.com', 'jojo', 'jojoo', 'joono', 1),
(25, '14/52HA020', 'ADEWOLE', 'YUSUF', 0, '12345678789', 'jobowonubi@gmail.com', 'jojo', 'jojoo', 'joono', 1),
(26, '13/52HA011', 'ADEYEMI', 'ADELODUN', 0, '12345678790', 'jobowonubi@gmail.com', 'jojo', 'jojoo', 'joono', 1),
(27, '16/52HA021', 'ADINOYI', 'SILAS', 0, '12345678791', 'jobowonubi@gmail.com', 'jojo', 'jojoo', 'joono', 1),
(28, '15/30GP009', 'AHMED', 'ISHAQ', 0, '12345678792', 'jobowonubi@gmail.com', 'jojo', 'jojoo', 'joono', 1),
(29, '16/52HA022', 'AINA', 'TIMOTHY', 0, '12345678793', 'jobowonubi@gmail.com', 'jojo', 'jojoo', 'joono', 1),
(30, '17/52HA113', 'AIYESIMI', 'JOSEPH', 4, '12345678794', 'jobowonubi@gmail.com', 'jojo', 'jojoo', 'joono', 1),
(31, '16/52HA023', 'AJAO', 'MARIAM', 25, '12345678795', 'jobowonubi@gmail.com', 'jojo', 'jojoo', 'joono', 1),
(32, '16/52HA024', 'AJIDE', 'TOSIN', 0, '12345678796', 'jobowonubi@gmail.com', 'jojo', 'jojoo', 'joono', 1),
(33, '15/52HA011', 'AJOGWU', 'Boluwatife', 0, '12345678797', 'jobowonubi@gmail.com', 'jojo', 'jojoo', 'joono', 1),
(34, '17/52HA114', 'AKINOLA', 'Roseline', 1, '12345678798', 'jobowonubi@gmail.com', 'jojo', 'jojoo', 'joono', 1),
(35, '16/52HA025', 'AKINPELU', 'ADEDOYIN', 0, '12345678799', 'jobowonubi@gmail.com', 'jojo', 'jojoo', 'joono', 1),
(36, '16/30GR016', 'AKINTOLA', 'ABDULMALIQ', 0, '12345678800', 'jobowonubi@gmail.com', 'jojo', 'jojoo', 'joono', 1),
(37, '16/30GR017', 'AKOGUN', 'MODASOLA', 6, '12345678801', 'jobowonubi@gmail.com', 'jojo', 'jojoo', 'joono', 1),
(38, '15/52HA012', 'ALAO', 'OLUWAMUREWA', 0, '12345678802', 'jobowonubi@gmail.com', 'jojo', 'jojoo', 'joono', 1),
(39, '14/52HA041', 'AMOO', 'BOLAJI', 0, '12345678803', 'jobowonubi@gmail.com', 'jojo', 'jojoo', 'joono', 1),
(40, '17/52HA115', 'ANIMASHAUN', 'ABDUL-AZEEM', 0, '12345678804', 'jobowonubi@gmail.com', 'jojo', 'jojoo', 'joono', 1),
(41, '16/52HA027', 'ATOLAGBE', 'FAVOUR', 5, '12345678805', 'jobowonubi@gmail.com', 'jojo', 'jojoo', 'joono', 1),
(42, '16/52HA028', 'AWOBIYI', 'TEMILOLA', 0, '12345678806', 'jobowonubi@gmail.com', 'jojo', 'jojoo', 'joono', 1),
(43, '16/52HA029', 'AYODELE', 'PEACE', 0, '12345678807', 'jobowonubi@gmail.com', 'jojo', 'jojoo', 'joono', 1),
(44, '16/52HA030', 'AYOKU', 'TAOFEEK', 7, '12345678808', 'jobowonubi@gmail.com', 'jojo', 'jojoo', 'joono', 1),
(45, '16/52HA031', 'BABAFEMI', 'ONAOPEMIPO', 12, '12345678809', 'jobowonubi@gmail.com', 'jojo', 'jojoo', 'joono', 1),
(46, '16/52HA032', 'BABALOLA', 'OMOTOMIWA', 12, '12345678810', 'jobowonubi@gmail.com', 'jojo', 'jojoo', 'joono', 1),
(47, '16/52HA035', 'BAMIJOKO', 'VICTOR', 6, '12345678811', 'jobowonubi@gmail.com', 'jojo', 'jojoo', 'joono', 1),
(48, '15/52HA021', 'BELLO', 'NURAT', 0, '12345678812', 'jobowonubi@gmail.com', 'jojo', 'jojoo', 'joono', 1),
(49, '15/52HA026', 'DIYAOLU', 'OLUWATOSIN', 0, '12345678813', 'jobowonubi@gmail.com', 'jojo', 'jojoo', 'joono', 1),
(50, '16/52HA036', 'EFEREYAN', 'KAREN', 5, '12345678814', 'jobowonubi@gmail.com', 'jojo', 'jojoo', 'joono', 1),
(51, '17/52HA116', 'EJEYE', 'Omagbemi', 0, '12345678815', 'jobowonubi@gmail.com', 'jojo', 'jojoo', 'joono', 1),
(52, '16/52HA037', 'ELEMIDE', 'SOFIYAH', 12, '12345678816', 'jobowonubi@gmail.com', 'jojo', 'jojoo', 'joono', 1),
(53, '16/52HA038', 'ERAGBIE', 'SOLOMON', 0, '12345678817', 'jobowonubi@gmail.com', 'jojo', 'jojoo', 'joono', 1),
(54, '17/52HA117', 'EZE-UKAUWA', 'Emmanuel', 7, '12345678818', 'jobowonubi@gmail.com', 'jojo', 'jojoo', 'joono', 1),
(55, '17/52HA118', 'FATAI', 'AZEEZAT', 7, '12345678819', 'jobowonubi@gmail.com', 'jojo', 'jojoo', 'joono', 1),
(56, '16/52HA039', 'GAMBARI', 'UMAR', 0, '12345678820', 'jobowonubi@gmail.com', 'jojo', 'jojoo', 'joono', 1),
(57, '15/52HA028', 'HASSAN', 'HAKEEM', 13, '12345678821', 'jobowonubi@gmail.com', 'jojo', 'jojoo', 'joono', 1),
(58, '16/52HA040', 'HASSAN', 'LATIFAT', 0, '12345678822', 'jobowonubi@gmail.com', 'jojo', 'jojoo', 'joono', 1),
(59, '16/52HA041', 'HUSSAIN', 'MONSUR', 9, '12345678823', 'jobowonubi@gmail.com', 'jojo', 'jojoo', 'joono', 1),
(60, '16/30GQ043', 'IBRAHIM', 'ABDULRAHIM', 0, '12345678824', 'jobowonubi@gmail.com', 'jojo', 'jojoo', 'joono', 1),
(61, '17/52HA119', 'IBRAHIM', 'FARUQ', 15, '12345678825', 'jobowonubi@gmail.com', 'jojo', 'jojoo', 'joono', 1),
(62, '15/52HA030', 'IBRAHIM', 'RIDWAN', 0, '12345678826', 'jobowonubi@gmail.com', 'jojo', 'jojoo', 'joono', 1),
(63, '16/30GB068', 'IDIONG', 'SAMUEL', 0, '12345678827', 'jobowonubi@gmail.com', 'jojo', 'jojoo', 'joono', 1),
(64, '16/52HA042', 'IDRIS', 'ABDULSAMAD', 13, '12345678828', 'jobowonubi@gmail.com', 'jojo', 'jojoo', 'joono', 1),
(65, '16/52HA043', 'IGE', 'OLUWASEGUN', 2, '12345678829', 'jobowonubi@gmail.com', 'jojo', 'jojoo', 'joono', 1),
(66, '16/30GB070', 'IJAIYA', 'TESLEEMAT', 0, '12345678830', 'jobowonubi@gmail.com', 'jojo', 'jojoo', 'joono', 1),
(67, '16/52HA044', 'ILORI', 'EMMANUEL', 5, '12345678831', 'jobowonubi@gmail.com', 'jojo', 'jojoo', 'joono', 1),
(68, '16/52HA110', 'ISMAILA', 'RAHEEM', 0, '12345678832', 'jobowonubi@gmail.com', 'jojo', 'jojoo', 'joono', 1),
(69, '16/52HA045', 'JIMOH', 'AISHAT', 1, '12345678833', 'jobowonubi@gmail.com', 'jojo', 'jojoo', 'joono', 1),
(70, '16/52HA046', 'JOGUNOLA', 'MUJIDAT', 0, '12345678834', 'jobowonubi@gmail.com', 'jojo', 'jojoo', 'joono', 1),
(71, '16/52HA047', 'KAREEM', 'AMINAT', 11, '12345678835', 'jobowonubi@gmail.com', 'jojo', 'jojoo', 'joono', 1),
(72, '17/52HA120', 'KOLAWOLE', 'HAKEEM', 4, '12345678836', 'jobowonubi@gmail.com', 'jojo', 'jojoo', 'joono', 1),
(73, '16/52HA049', 'LAMADINE', 'Jarry', 6, '12345678837', 'jobowonubi@gmail.com', 'jojo', 'jojoo', 'joono', 1),
(74, '16/52HA050', 'LAWAL', 'FAHD', 0, '12345678838', 'jobowonubi@gmail.com', 'jojo', 'jojoo', 'joono', 1),
(75, '16/52HA051', 'MAKANJUOLA', 'OLORUNFEMI', 9, '12345678839', 'jobowonubi@gmail.com', 'jojo', 'jojoo', 'joono', 1),
(76, '16/30GT040', 'MALOMO', 'OLARIKE', 6, '12345678840', 'jobowonubi@gmail.com', 'jojo', 'jojoo', 'joono', 1),
(77, '17/52HA121', 'MEMUDU', 'Alimatou', 1, '12345678841', 'jobowonubi@gmail.com', 'jojo', 'jojoo', 'joono', 1),
(78, '16/52HA052', 'MOIBI', 'OREOLUWA', 7, '12345678842', 'jobowonubi@gmail.com', 'jojo', 'jojoo', 'joono', 1),
(79, '16/52HA053', 'MOSES', 'SAMUEL', 18, '12345678843', 'jobowonubi@gmail.com', 'jojo', 'jojoo', 'joono', 1),
(80, '16/52HA055', 'MUHAMMED', 'FAROUK', 5, '12345678844', 'jobowonubi@gmail.com', 'jojo', 'jojoo', 'joono', 1),
(81, '16/52HA056', 'MURITALA', 'WALIU', 1, '12345678845', 'jobowonubi@gmail.com', 'jojo', 'jojoo', 'joono', 1),
(82, '16/52HA057', 'MUSA', 'QOZEEM', 8, '12345678846', 'jobowonubi@gmail.com', 'jojo', 'jojoo', 'joono', 1),
(83, '17/52HA123', 'MUSA', 'SOFIAT', 5, '12345678847', 'jobowonubi@gmail.com', 'jojo', 'jojoo', 'joono', 1),
(84, '16/52HA058', 'NASIRU', 'NAJEEM', 0, '12345678848', 'jobowonubi@gmail.com', 'jojo', 'jojoo', 'joono', 1),
(85, '16/52HA059', 'OBANLAGBEYI', 'ODUNAYO', 7, '12345678849', 'jobowonubi@gmail.com', 'jojo', 'jojoo', 'joono', 1),
(86, '15/52HA044', 'OBATOMI', 'YUSSUF', 0, '12345678850', 'jobowonubi@gmail.com', 'jojo', 'jojoo', 'joono', 1),
(87, '16/52HA060', 'ODENIYI', 'CHRISTOPHER', 7, '12345678851', 'jobowonubi@gmail.com', 'jojo', 'jojoo', 'joono', 1),
(88, '16/52HA061', 'ODIKE', 'PRECIOUS', 12, '12345678852', 'jobowonubi@gmail.com', 'jojo', 'jojoo', 'joono', 1),
(89, '16/52HA062', 'ODUNAYO', 'QUADRI', 0, '12345678853', 'jobowonubi@gmail.com', 'jojo', 'jojoo', 'joono', 1),
(90, '16/52HA118', 'ODUYEMI', 'KEHINDE', 0, '12345678854', 'jobowonubi@gmail.com', 'jojo', 'jojoo', 'joono', 1),
(91, '16/52HA119', 'OGA', 'Kouffole', 0, '12345678855', 'jobowonubi@gmail.com', 'jojo', 'jojoo', 'joono', 1),
(92, '16/52HA063', 'OGBUJI', 'MICHAEL', 0, '12345678856', 'jobowonubi@gmail.com', 'jojo', 'jojoo', 'joono', 1),
(93, '16/30GD080', 'OGUNYINKA', 'IREOLUWA', 0, '12345678857', 'jobowonubi@gmail.com', 'jojo', 'jojoo', 'joono', 1),
(94, '16/52HA064', 'OKERINDE', 'OMOTOLANI', 1, '12345678858', 'jobowonubi@gmail.com', 'jojo', 'jojoo', 'joono', 1),
(95, '15/52HA052', 'OLADEEBO', 'OLAWUNMI', 0, '12345678859', 'jobowonubi@gmail.com', 'jojo', 'jojoo', 'joono', 1),
(96, '16/52HA065', 'OLADELE', 'AZEEZAT', 5, '12345678860', 'jobowonubi@gmail.com', 'jojo', 'jojoo', 'joono', 1),
(97, '16/52HA066', 'OLAEGBE', 'REBECCA', 12, '12345678861', 'jobowonubi@gmail.com', 'jojo', 'jojoo', 'joono', 1),
(98, '16/52HA067', 'OLAJIDE', 'EDMUND', 0, '12345678862', 'jobowonubi@gmail.com', 'jojo', 'jojoo', 'joono', 1),
(99, '16/52HA068', 'OLAKOJO', 'AZEEZ', 0, '12345678863', 'jobowonubi@gmail.com', 'jojo', 'jojoo', 'joono', 1),
(100, '17/52HA124', 'OLANIYI', 'SEGUN', 1, '12345678864', 'jobowonubi@gmail.com', 'jojo', 'jojoo', 'joono', 1),
(101, '16/52HA069', 'OLAOMO', 'VICTOR', 9, '12345678865', 'jobowonubi@gmail.com', 'jojo', 'jojoo', 'joono', 1),
(102, '16/30GD089', 'OLAOYE', 'Kolawole', 0, '12345678866', 'jobowonubi@gmail.com', 'jojo', 'jojoo', 'joono', 1),
(103, '16/52HA070', 'OLATUNJI', 'OKIKIOLA', 25, '12345678867', 'jobowonubi@gmail.com', 'jojo', 'jojoo', 'joono', 1),
(104, '17/52HA125', 'OLAWALE', 'HAMMED', 27, '12345678868', 'jobowonubi@gmail.com', 'jojo', 'jojoo', 'joono', 1),
(105, '16/52HA071', 'OLOHUNGBEBE', 'KEHINDE', 6, '12345678869', 'jobowonubi@gmail.com', 'jojo', 'jojoo', 'joono', 1),
(106, '16/52HA072', 'OLONADE', 'IFEOLUWA', 27, '12345678870', 'jobowonubi@gmail.com', 'jojo', 'jojoo', 'joono', 1),
(107, '16/52HA074', 'OLUDARE', 'RUFUS', 24, '12345678871', 'jobowonubi@gmail.com', 'jojo', 'jojoo', 'joono', 1),
(108, '14/52HA100', 'OLUSEGUN', 'GIDEON', 0, '12345678872', 'jobowonubi@gmail.com', 'jojo', 'jojoo', 'joono', 1),
(109, '16/52HA075', 'ONIKEPE', 'ABDULAZEEZ', 0, '12345678873', 'jobowonubi@gmail.com', 'jojo', 'jojoo', 'joono', 1),
(110, '16/52HA076', 'ONIRETI', 'MUNIRAT', 3, '12345678874', 'jobowonubi@gmail.com', 'jojo', 'jojoo', 'joono', 1),
(111, '16/52HA078', 'OSADARE', 'OMOLEYE', 0, '12345678875', 'jobowonubi@gmail.com', 'jojo', 'jojoo', 'joono', 1),
(112, '17/52HA126', 'OSENI', 'KEHINDE', 0, '12345678876', 'jobowonubi@gmail.com', 'jojo', 'jojoo', 'joono', 1),
(113, '16/52HA079', 'OWOLOWO', 'MARIAM', 0, '12345678877', 'jobowonubi@gmail.com', 'jojo', 'jojoo', 'joono', 1),
(114, '17/52HA127', 'OWONUBI', 'JOB', 13, '12345678878', 'jobowonubi@gmail.com', 'jojo', 'jojoo', 'joono', 1),
(115, '15/52HA062', 'OYEBAMIJI', 'AYOMIDE', 0, '12345678879', 'jobowonubi@gmail.com', 'jojo', 'jojoo', 'joono', 1),
(116, '16/52HA080', 'OYENIYI', 'ABIODUN', 0, '12345678880', 'jobowonubi@gmail.com', 'jojo', 'jojoo', 'joono', 1),
(117, '16/52HA081', 'OYENIYI', 'GBOLAHAN', 22, '12345678881', 'jobowonubi@gmail.com', 'jojo', 'jojoo', 'joono', 1),
(118, '16/30GB124', 'OYEWOLE', 'RIDWAN', 0, '12345678882', 'jobowonubi@gmail.com', 'jojo', 'jojoo', 'joono', 1),
(119, '15/52HA064', 'OYINLOLA', 'ABASS', 2, '12345678883', 'jobowonubi@gmail.com', 'jojo', 'jojoo', 'joono', 1),
(120, '15/52HA069', 'RAHEEM', 'MUIZ', 0, '12345678884', 'jobowonubi@gmail.com', 'jojo', 'jojoo', 'joono', 1),
(121, '14/52HA113', 'SAAD', 'TAOFEEQ', 0, '12345678885', 'jobowonubi@gmail.com', 'jojo', 'jojoo', 'joono', 1),
(122, '16/30GR049', 'SHAIBU', 'ABDULFATAI', 21, '12345678886', 'jobowonubi@gmail.com', 'jojo', 'jojoo', 'joono', 1),
(123, '16/52HA084', 'SHUAIB', 'ABDUL-RAZAQ', 5, '12345678887', 'jobowonubi@gmail.com', 'jojo', 'jojoo', 'joono', 1),
(124, '16/52HA133', 'SODIQ', 'ADEWALE', 0, '12345678888', 'jobowonubi@gmail.com', 'jojo', 'jojoo', 'joono', 1),
(125, '16/52HA085', 'SOYOYE', 'TEMITOPE', 0, '12345678889', 'jobowonubi@gmail.com', 'jojo', 'jojoo', 'joono', 1),
(126, '16/52HA086', 'SUBAIR', 'TAOFIQ', 4, '12345678890', 'jobowonubi@gmail.com', 'jojo', 'jojoo', 'joono', 1),
(127, '16/52HA087', 'SULEIMAN', 'BABATUNDE', 0, '12345678891', 'jobowonubi@gmail.com', 'jojo', 'jojoo', 'joono', 1),
(128, '16/52HA134', 'SULYMAN', 'NURUHAKEEM', 0, '12345678892', 'jobowonubi@gmail.com', 'jojo', 'jojoo', 'joono', 1),
(129, '16/52HA088', 'UNUGBAI', 'FREEDOM', 23, '12345678893', 'jobowonubi@gmail.com', 'jojo', 'jojoo', 'joono', 1),
(130, '16/52HA089', 'USMAN', 'FATIMA', 3, '12345678894', 'jobowonubi@gmail.com', 'jojo', 'jojoo', 'joono', 1),
(131, '16/52HA090', 'USMAN', 'RIDWAN', 0, '12345678895', 'jobowonubi@gmail.com', 'jojo', 'jojoo', 'joono', 1),
(132, '15/52HA074', 'UWAHEREN', 'PRECIOUS', 0, '12345678896', 'jobowonubi@gmail.com', 'jojo', 'jojoo', 'joono', 1),
(133, '16/52HA091', 'YUSUF', 'ISSA', 20, '12345678897', 'jobowonubi@gmail.com', 'jojo', 'jojoo', 'joono', 1),
(134, '16/52HA092', 'YUSUPH', 'AHMAD', 2, '12345678898', 'jobowonubi@gmail.com', 'jojo', 'jojoo', 'joono', 1);

-- --------------------------------------------------------

--
-- Table structure for table `student_logs`
--

CREATE TABLE `student_logs` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `entry_date` varchar(50) DEFAULT NULL,
  `action` text NOT NULL,
  `level` int(11) NOT NULL DEFAULT 1 COMMENT '1 for low, 2 for medium and 3 for high'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `stud_to_group`
--

CREATE TABLE `stud_to_group` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `cpu_id` int(11) NOT NULL,
  `attachment` varchar(64) NOT NULL DEFAULT '0',
  `msg` text DEFAULT NULL,
  `status` int(11) NOT NULL DEFAULT 0,
  `entry_date` varchar(32) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `stud_to_sup`
--

CREATE TABLE `stud_to_sup` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `supervisor_id` int(11) NOT NULL,
  `attachment` varchar(64) NOT NULL DEFAULT '0',
  `msg` text DEFAULT NULL,
  `status` int(11) NOT NULL DEFAULT 0,
  `entry_date` varchar(32) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `supervisors`
--

CREATE TABLE `supervisors` (
  `id` int(11) NOT NULL,
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
  `status` int(11) NOT NULL DEFAULT 0
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `supervisors`
--

INSERT INTO `supervisors` (`id`, `title_id`, `fileno`, `password`, `firstname`, `lastname`, `phone`, `email`, `location`, `max`, `field`, `status`) VALUES
(1, 1, 'S0001', 'snosps', 'ABDULLATEEF', 'ABDULSAMAD', '12345678987', 'jobowonubi@gmail.com', 'jsjsjsjs', 9, 0, 0),
(2, 2, 'S0002', 'snosps', 'ABDULSALAM', 'ABUBAKAR', '12345678988', 'jobowonubi@gmail.com', 'jsjsjsjs', 11, 0, 0),
(3, 3, 'S0003', 'snosps', 'ABDULSALAM', 'HAWAU', '12345678989', 'jobowonubi@gmail.com', 'jsjsjsjs', 7, 0, 0),
(4, 4, 'S0004', 'snosps', 'ABEGUNDE', 'SAMUEL', '12345678990', 'jobowonubi@gmail.com', 'jsjsjsjs', 11, 0, 0),
(5, 5, 'S0005', 'snosps', 'ABIDAKUN', 'OLUWATOMIWA', '12345678991', 'jobowonubi@gmail.com', 'jsjsjsjs', 8, 0, 0),
(6, 6, 'S0006', 'snosps', 'ABRAHAM', 'CELESTINE', '12345678992', 'jobowonubi@gmail.com', 'jsjsjsjs', 9, 0, 0),
(7, 7, 'S0007', 'snosps', 'ABUBAKAR', 'ISHOLA', '12345678993', 'jobowonubi@gmail.com', 'jsjsjsjs', 10, 0, 0),
(8, 8, 'S0008', 'snosps', 'ABUBAKAR', 'NURUDEEN', '12345678994', 'jobowonubi@gmail.com', 'jsjsjsjs', 9, 0, 0),
(9, 9, 'S0009', 'snosps', 'ADEBAYO', 'DEBORAH', '12345678995', 'jobowonubi@gmail.com', 'jsjsjsjs', 7, 0, 0),
(10, 10, 'S0010', 'snosps', 'ADEBAYO', 'OLADAYO', '12345678996', 'jobowonubi@gmail.com', 'jsjsjsjs', 11, 0, 0),
(11, 11, 'S0011', 'snosps', 'ADEBIYI', 'EYITAYO', '12345678997', 'jobowonubi@gmail.com', 'jsjsjsjs', 12, 0, 0),
(12, 12, 'S0012', 'snosps', 'ADEBOYE', 'WAREEZ', '12345678998', 'jobowonubi@gmail.com', 'jsjsjsjs', 12, 0, 0),
(13, 13, 'S0013', 'snosps', 'ADEDAYO', 'OYINJESU', '12345678999', 'jobowonubi@gmail.com', 'jsjsjsjs', 12, 0, 0),
(14, 14, 'S0014', 'snosps', 'ADEGOKE', 'KAYODE', '12345679000', 'jobowonubi@gmail.com', 'jsjsjsjs', 10, 0, 0),
(15, 15, 'S0015', 'snosps', 'ADEGOKE', 'OLUWASEUN', '12345679001', 'jobowonubi@gmail.com', 'jsjsjsjs', 8, 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `supervisor_logs`
--

CREATE TABLE `supervisor_logs` (
  `id` int(11) NOT NULL,
  `supervisor_id` int(11) NOT NULL,
  `entry_date` varchar(50) DEFAULT NULL,
  `action` text NOT NULL,
  `level` int(11) NOT NULL DEFAULT 1 COMMENT '1 for low, 2 for medium and 3 for high'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `sup_to_group`
--

CREATE TABLE `sup_to_group` (
  `id` int(11) NOT NULL,
  `supervisor_id` int(11) NOT NULL,
  `cpu_id` int(11) NOT NULL,
  `attachment` varchar(64) NOT NULL DEFAULT '0',
  `msg` text DEFAULT NULL,
  `status` int(11) NOT NULL DEFAULT 0,
  `entry_date` varchar(32) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `sup_to_stud`
--

CREATE TABLE `sup_to_stud` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `supervisor_id` int(11) NOT NULL,
  `attachment` varchar(64) NOT NULL DEFAULT '0',
  `msg` text DEFAULT NULL,
  `status` int(11) NOT NULL DEFAULT 0,
  `entry_date` varchar(32) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `titles`
--

CREATE TABLE `titles` (
  `id` int(11) NOT NULL,
  `name` varchar(12) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(100) NOT NULL,
  `firstname` varchar(100) NOT NULL,
  `lastname` varchar(100) NOT NULL,
  `email` varchar(50) NOT NULL DEFAULT 'jobowonubi@gmail.com',
  `phone` varchar(11) NOT NULL DEFAULT '08035837211'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin_logs`
--
ALTER TABLE `admin_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `admin_id` (`admin_id`);

--
-- Indexes for table `allocation`
--
ALTER TABLE `allocation`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `student_id` (`student_id`);

--
-- Indexes for table `announcements`
--
ALTER TABLE `announcements`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cpu_id` (`cpu_id`);

--
-- Indexes for table `assign_request`
--
ALTER TABLE `assign_request`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `one_request_at_a_time` (`student_id`,`status`) USING BTREE,
  ADD KEY `cpu_id` (`cpu_id`);

--
-- Indexes for table `change_request`
--
ALTER TABLE `change_request`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `cpu`
--
ALTER TABLE `cpu`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id` (`id`),
  ADD KEY `field_id` (`field_id`);

--
-- Indexes for table `failed_allocate`
--
ALTER TABLE `failed_allocate`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `student_id` (`student_id`);

--
-- Indexes for table `field_of_interests`
--
ALTER TABLE `field_of_interests`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `progress`
--
ALTER TABLE `progress`
  ADD PRIMARY KEY (`id`),
  ADD KEY `chapter` (`chapter`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `special_request`
--
ALTER TABLE `special_request`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `student_id` (`student_id`),
  ADD KEY `field_id` (`field_id`);

--
-- Indexes for table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `student_logs`
--
ALTER TABLE `student_logs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `stud_to_group`
--
ALTER TABLE `stud_to_group`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cpu_id` (`cpu_id`);

--
-- Indexes for table `stud_to_sup`
--
ALTER TABLE `stud_to_sup`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `supervisors`
--
ALTER TABLE `supervisors`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `fileno` (`fileno`);

--
-- Indexes for table `supervisor_logs`
--
ALTER TABLE `supervisor_logs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sup_to_group`
--
ALTER TABLE `sup_to_group`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cpu_id` (`cpu_id`);

--
-- Indexes for table `sup_to_stud`
--
ALTER TABLE `sup_to_stud`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `titles`
--
ALTER TABLE `titles`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin_logs`
--
ALTER TABLE `admin_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `allocation`
--
ALTER TABLE `allocation`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=72;

--
-- AUTO_INCREMENT for table `announcements`
--
ALTER TABLE `announcements`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `assign_request`
--
ALTER TABLE `assign_request`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `change_request`
--
ALTER TABLE `change_request`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `cpu`
--
ALTER TABLE `cpu`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `failed_allocate`
--
ALTER TABLE `failed_allocate`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `field_of_interests`
--
ALTER TABLE `field_of_interests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `progress`
--
ALTER TABLE `progress`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `special_request`
--
ALTER TABLE `special_request`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=135;

--
-- AUTO_INCREMENT for table `student_logs`
--
ALTER TABLE `student_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `stud_to_group`
--
ALTER TABLE `stud_to_group`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `stud_to_sup`
--
ALTER TABLE `stud_to_sup`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `supervisors`
--
ALTER TABLE `supervisors`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `supervisor_logs`
--
ALTER TABLE `supervisor_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sup_to_group`
--
ALTER TABLE `sup_to_group`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sup_to_stud`
--
ALTER TABLE `sup_to_stud`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `titles`
--
ALTER TABLE `titles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
