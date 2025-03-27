-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 27, 2025 at 06:44 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `jamii-hms`
--

-- --------------------------------------------------------

--
-- Table structure for table `appointment`
--

CREATE TABLE `appointment` (
  `A_Id` int(100) NOT NULL,
  `PatientID` varchar(100) NOT NULL,
  `Full_name` varchar(100) NOT NULL,
  `phone` varchar(100) NOT NULL,
  `Email_Address` varchar(100) NOT NULL,
  `Doctor` varchar(100) NOT NULL,
  `Date` date NOT NULL,
  `Time` time(6) NOT NULL,
  `Status` varchar(100) NOT NULL DEFAULT 'Pending',
  `previously_treated` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `appointment`
--

INSERT INTO `appointment` (`A_Id`, `PatientID`, `Full_name`, `phone`, `Email_Address`, `Doctor`, `Date`, `Time`, `Status`, `previously_treated`) VALUES
(15, '159', 'Nicholas Case Kimani', '0755555555', 'example001@gmail.com', 'Dr. Lisa Molly', '2025-03-28', '10:00:00.000000', 'Confirmed', ''),
(16, '158', 'Elisabeth Escobar Njoroge', '0744444444', 'example001@gmail.com', 'Dr. Lisa Molly', '2025-04-01', '14:00:00.000000', 'Pending', '');

-- --------------------------------------------------------

--
-- Table structure for table `billing`
--

CREATE TABLE `billing` (
  `bill_ID` int(100) NOT NULL,
  `VisitID` int(100) NOT NULL,
  `Unit` varchar(500) NOT NULL,
  `Description` varchar(100) NOT NULL,
  `Quantity` int(100) NOT NULL,
  `Unit_Price` int(100) NOT NULL,
  `Desc_Percentage` int(11) DEFAULT 0,
  `Desc_KES` int(11) DEFAULT 0,
  `Amount` int(11) NOT NULL,
  `Status` varchar(100) NOT NULL DEFAULT 'Pending',
  `Date_Created` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `billing`
--

INSERT INTO `billing` (`bill_ID`, `VisitID`, `Unit`, `Description`, `Quantity`, `Unit_Price`, `Desc_Percentage`, `Desc_KES`, `Amount`, `Status`, `Date_Created`) VALUES
(61, 80, 'Consultation', 'Consultation Fee', 1, 1000, 0, 0, 1000, 'Processed', '2025-03-27 16:35:31'),
(62, 80, 'Lab', 'Lab Fee - Malaria Test', 1, 500, 0, 0, 500, 'Processed', '2025-03-27 16:37:41'),
(63, 80, 'Quinine Sulfate', 'Medicine Prescription', 15, 700, 0, 0, 10500, 'Processed', '2025-03-27 16:40:00'),
(64, 81, 'Consultation', 'Consultation Fee', 1, 1000, 0, 0, 1000, 'Processed', '2025-03-27 16:43:28'),
(65, 81, 'Paracetamol', 'Medicine Prescription', 10, 100, 0, 0, 1000, 'Processed', '2025-03-27 16:45:28'),
(66, 82, 'Consultation', 'Consultation Fee', 1, 1000, 0, 0, 1000, 'Processed', '2025-03-27 16:50:26');

-- --------------------------------------------------------

--
-- Table structure for table `diagnoses`
--

CREATE TABLE `diagnoses` (
  `DiagnosisID` int(11) NOT NULL,
  `VisitID` int(11) NOT NULL,
  `Test_Request_ID` varchar(100) DEFAULT NULL,
  `Diagnosis` text NOT NULL,
  `Treatment` text NOT NULL,
  `Status` varchar(100) NOT NULL DEFAULT 'Pending',
  `CreatedAt` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `human_resource`
--

CREATE TABLE `human_resource` (
  `EmployeeID` int(11) NOT NULL,
  `FirstName` varchar(50) NOT NULL,
  `MiddleName` varchar(50) NOT NULL,
  `LastName` varchar(50) NOT NULL,
  `DateOfBirth` date NOT NULL,
  `Gender` enum('Male','Female','Other') NOT NULL,
  `PhoneNumber` varchar(20) NOT NULL,
  `Email` varchar(100) NOT NULL,
  `Address` text NOT NULL,
  `Position` varchar(100) NOT NULL,
  `Salary` decimal(10,2) NOT NULL,
  `HireDate` date NOT NULL,
  `CreatedAt` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `human_resource`
--

INSERT INTO `human_resource` (`EmployeeID`, `FirstName`, `MiddleName`, `LastName`, `DateOfBirth`, `Gender`, `PhoneNumber`, `Email`, `Address`, `Position`, `Salary`, `HireDate`, `CreatedAt`) VALUES
(1, 'Sally', 'Vanessa', 'Jones', '1990-05-15', 'Female', '0712345678', 'sallyv@example.com', '123 Main St, Cityville', 'Receptionist', 0.00, '2022-06-10', '2025-03-19 17:32:06'),
(8, 'Justin', 'Owen', 'Martin', '1990-05-15', 'Male', '0712345676', 'justin0@example.com', '123 Main St, Cityville', 'Triage Nurse', 45000.00, '2022-06-10', '2025-03-19 17:38:07'),
(9, 'Lisa', 'Molly', 'Janet', '1990-05-15', 'Female', '0712345679', 'lisam@example.com', '123 Main St, Cityville', 'Doctor', 45000.00, '2022-06-10', '2025-03-19 17:38:07'),
(10, 'Simon', 'Trevor', 'Daniel', '1990-05-15', 'Male', '0712355676', 'simont@example.com', '123 Main St, Cityville', 'Lab Technician', 45000.00, '2022-06-10', '2025-03-19 17:42:27'),
(11, 'Julia', 'Joanne', 'Lyn', '1990-05-15', 'Female', '0718245679', 'juliaj@example.com', '123 Main St, Cityville', 'Pharmacy', 45000.00, '2022-06-10', '2025-03-19 17:42:27'),
(12, 'Phil', 'Warren', 'Tom', '1990-05-15', 'Male', '0712245979', 'philw@example.com', '123 Main St, Cityville', 'Billing Officer', 45000.00, '2022-06-10', '2025-03-19 17:42:27');

-- --------------------------------------------------------

--
-- Table structure for table `invoice`
--

CREATE TABLE `invoice` (
  `invoice_ID` int(11) NOT NULL,
  `bill_ID` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `invoice`
--

INSERT INTO `invoice` (`invoice_ID`, `bill_ID`, `created_at`) VALUES
(56, 61, '2025-03-27 16:36:03'),
(57, 62, '2025-03-27 16:37:48'),
(58, 63, '2025-03-27 16:40:25'),
(59, 64, '2025-03-27 16:43:36'),
(60, 65, '2025-03-27 16:45:37'),
(61, 66, '2025-03-27 16:50:40');

-- --------------------------------------------------------

--
-- Table structure for table `issued_medications`
--

CREATE TABLE `issued_medications` (
  `issued_id` int(11) NOT NULL,
  `diagnosis_id` int(11) NOT NULL,
  `visit_id` int(11) NOT NULL,
  `diagnosis` varchar(255) NOT NULL,
  `treatment` varchar(255) NOT NULL,
  `item_name` varchar(255) NOT NULL,
  `quantity` int(100) NOT NULL,
  `issued_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `lab`
--

CREATE TABLE `lab` (
  `LabId` int(11) NOT NULL,
  `TriageId` int(11) NOT NULL,
  `VisitId` int(11) NOT NULL,
  `Weight` varchar(10) DEFAULT NULL,
  `Height` varchar(10) DEFAULT NULL,
  `Temperature` varchar(10) DEFAULT NULL,
  `HeartRate` varchar(10) DEFAULT NULL,
  `Symptoms` text DEFAULT NULL,
  `Assigned_Doctor` varchar(255) DEFAULT NULL,
  `Required_Test` varchar(255) DEFAULT NULL,
  `CreatedAt` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `lab_requests`
--

CREATE TABLE `lab_requests` (
  `Test_Request_ID` int(11) NOT NULL,
  `PatientId` int(11) NOT NULL,
  `VisitId` int(11) NOT NULL,
  `PatientName` varchar(100) NOT NULL,
  `Test_Type` varchar(100) NOT NULL,
  `Clinical_Notes` varchar(100) NOT NULL,
  `Urgency` varchar(100) NOT NULL,
  `Status` varchar(100) NOT NULL DEFAULT 'Pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `lab_requests`
--

INSERT INTO `lab_requests` (`Test_Request_ID`, `PatientId`, `VisitId`, `PatientName`, `Test_Type`, `Clinical_Notes`, `Urgency`, `Status`, `created_at`) VALUES
(29, 157, 80, 'June Kamau', 'Malaria Test', 'Malaria test', 'routine', 'Test Done', '2025-03-27 16:37:41');

-- --------------------------------------------------------

--
-- Table structure for table `lab_results`
--

CREATE TABLE `lab_results` (
  `Test_Result_ID` int(11) NOT NULL,
  `Test_Request_ID` int(11) NOT NULL,
  `PatientName` varchar(255) NOT NULL,
  `Test_Type` varchar(255) NOT NULL,
  `Clinical_Notes` text NOT NULL,
  `Results` text NOT NULL,
  `Conclusion` text NOT NULL,
  `CreatedAt` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `lab_results`
--

INSERT INTO `lab_results` (`Test_Result_ID`, `Test_Request_ID`, `PatientName`, `Test_Type`, `Clinical_Notes`, `Results`, `Conclusion`, `CreatedAt`) VALUES
(22, 29, 'June Kamau', 'Malaria Test', 'Malaria test', 'Positive', 'Positive for malaria', '2025-03-27 16:38:48');

-- --------------------------------------------------------

--
-- Table structure for table `patients`
--

CREATE TABLE `patients` (
  `PatientID` int(11) NOT NULL,
  `FirstName` varchar(100) NOT NULL,
  `MiddleName` varchar(100) NOT NULL,
  `LastName` varchar(100) NOT NULL,
  `DateOfBirth` date NOT NULL,
  `Gender` enum('Male','Female','Other') NOT NULL,
  `PhoneNumber` varchar(15) NOT NULL,
  `Address` text NOT NULL,
  `Email` varchar(255) DEFAULT NULL,
  `NOKFname` varchar(100) NOT NULL,
  `NOKMname` varchar(100) NOT NULL,
  `NOKLname` varchar(100) NOT NULL,
  `Relationship` varchar(100) NOT NULL,
  `NOKContact` varchar(15) NOT NULL,
  `CreatedAt` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `patients`
--

INSERT INTO `patients` (`PatientID`, `FirstName`, `MiddleName`, `LastName`, `DateOfBirth`, `Gender`, `PhoneNumber`, `Address`, `Email`, `NOKFname`, `NOKMname`, `NOKLname`, `Relationship`, `NOKContact`, `CreatedAt`) VALUES
(157, 'June', 'Davila', 'Kamau', '1990-01-02', 'Female', '0722222222', 'Kenol', 'example001@gmail.com', 'Dylan', 'Brewer', 'Kamau', 'Husband', '0733333333', '2025-03-27 16:35:23'),
(158, 'Elisabeth', 'Escobar', 'Njoroge', '2012-12-04', 'Female', '0744444444', 'Maragua', 'example001@gmail.com', 'Hudson', 'Njoroge', 'Freeman', 'Mother', '0744444444', '2025-03-27 16:43:08'),
(159, 'Nicholas', 'Case', 'Kimani', '2000-09-11', 'Male', '0755555555', 'Township', 'example001@gmail.com', 'Edwin', 'Clay', 'Kimani', 'Brother', '0766666666', '2025-03-27 16:48:59');

-- --------------------------------------------------------

--
-- Table structure for table `pharmacy_inventory`
--

CREATE TABLE `pharmacy_inventory` (
  `ItemID` int(11) NOT NULL,
  `ItemName` varchar(255) NOT NULL,
  `Category` varchar(100) NOT NULL,
  `Quantity` int(11) NOT NULL,
  `UnitPrice` decimal(10,2) NOT NULL,
  `ExpiryDate` date NOT NULL,
  `Supplier` varchar(255) DEFAULT NULL,
  `BatchNumber` varchar(100) DEFAULT NULL,
  `AddedAt` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pharmacy_inventory`
--

INSERT INTO `pharmacy_inventory` (`ItemID`, `ItemName`, `Category`, `Quantity`, `UnitPrice`, `ExpiryDate`, `Supplier`, `BatchNumber`, `AddedAt`) VALUES
(21, 'Artemether/Lumefantrine (Coartem)', 'Antimalarial', 85, 500.00, '2026-12-31', 'PharmaSupplier1', 'MAL001', '2025-03-19 06:21:50'),
(22, 'Quinine Sulfate', 'Antimalarial', 30, 700.00, '2026-10-15', 'PharmaSupplier2', 'MAL002', '2025-03-19 06:21:50'),
(23, 'Artesunate Injection', 'Antimalarial', 100, 1200.00, '2027-01-20', 'PharmaSupplier3', 'MAL003', '2025-03-19 06:21:50'),
(24, 'Ciprofloxacin', 'Antibiotic', 140, 300.00, '2025-09-30', 'PharmaSupplier1', 'TYP001', '2025-03-19 06:21:50'),
(25, 'Azithromycin', 'Antibiotic', 116, 450.00, '2026-08-15', 'PharmaSupplier2', 'TYP002', '2025-03-19 06:21:50'),
(26, 'Ceftriaxone Injection', 'Antibiotic', 100, 900.00, '2026-05-10', 'PharmaSupplier3', 'TYP003', '2025-03-19 06:21:50'),
(27, 'Tenofovir/Lamivudine/Dolutegravir (TLD)', 'Antiviral', 80, 2000.00, '2027-06-30', 'HIVSupplier1', 'HIV001', '2025-03-19 06:21:50'),
(28, 'Metformin', 'Diabetes', 120, 500.00, '2026-12-20', 'PharmaSupplier1', 'DB001', '2025-03-19 06:21:50'),
(29, 'Insulin Injection', 'Diabetes', 50, 2500.00, '2027-04-25', 'PharmaSupplier2', 'DB002', '2025-03-19 06:21:50'),
(30, 'Nitrofurantoin', 'Antibiotic', 90, 350.00, '2025-11-30', 'PharmaSupplier3', 'UTI001', '2025-03-19 06:21:50'),
(31, 'Ciprofloxacin', 'Antibiotic', 140, 300.00, '2026-07-10', 'PharmaSupplier2', 'UTI002', '2025-03-19 06:21:50'),
(32, 'Albendazole', 'Antiparasitic', 180, 250.00, '2027-03-15', 'PharmaSupplier1', 'PARA001', '2025-03-19 06:21:50'),
(33, 'Metronidazole', 'Antibiotic', 180, 400.00, '2025-12-01', 'PharmaSupplier3', 'PARA002', '2025-03-19 06:21:50'),
(34, 'Ferrous Sulfate', 'Supplements', 150, 200.00, '2026-09-20', 'PharmaSupplier1', 'ANEM001', '2025-03-19 06:21:50'),
(35, 'Vitamin B12 Injection', 'Supplements', 50, 800.00, '2027-02-28', 'PharmaSupplier2', 'ANEM002', '2025-03-19 06:21:50'),
(36, 'Tenofovir', 'Antiviral', 100, 1800.00, '2027-06-30', 'HIVSupplier1', 'HEP001', '2025-03-19 06:21:50'),
(37, 'Entecavir', 'Antiviral', 70, 2200.00, '2026-08-14', 'HIVSupplier2', 'HEP002', '2025-03-19 06:21:50'),
(38, 'Paracetamol', 'Pain Reliever', 134, 100.00, '2026-06-10', 'PharmaSupplier1', 'PAIN001', '2025-03-19 06:21:50'),
(39, 'Ibuprofen', 'Pain Reliever', 200, 250.00, '2025-12-20', 'PharmaSupplier2', 'PAIN002', '2025-03-19 06:21:50'),
(40, 'Diclofenac', 'Pain Reliever', 172, 300.00, '2026-11-30', 'PharmaSupplier3', 'PAIN003', '2025-03-19 06:21:50');

-- --------------------------------------------------------

--
-- Table structure for table `prescriptions`
--

CREATE TABLE `prescriptions` (
  `id` int(11) NOT NULL,
  `VisitID` int(11) NOT NULL,
  `diagnosis` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `prescriptions`
--

INSERT INTO `prescriptions` (`id`, `VisitID`, `diagnosis`, `created_at`) VALUES
(49, 80, 'Positive for malaria', '2025-03-27 16:40:00'),
(50, 81, 'Cold', '2025-03-27 16:45:28');

-- --------------------------------------------------------

--
-- Table structure for table `prescription_medicines`
--

CREATE TABLE `prescription_medicines` (
  `id` int(11) NOT NULL,
  `prescription_id` int(11) NOT NULL,
  `Test_Request_ID` varchar(100) DEFAULT NULL,
  `medicine_name` varchar(255) NOT NULL,
  `frequency` varchar(50) NOT NULL,
  `quantity` int(11) NOT NULL,
  `status` varchar(100) NOT NULL DEFAULT 'Pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `prescription_medicines`
--

INSERT INTO `prescription_medicines` (`id`, `prescription_id`, `Test_Request_ID`, `medicine_name`, `frequency`, `quantity`, `status`, `created_at`) VALUES
(66, 49, NULL, 'Quinine Sulfate', 'Three times a day', 15, 'Issued', '2025-03-27 16:40:00'),
(67, 50, NULL, 'Paracetamol', 'Twice a day', 10, 'Pending', '2025-03-27 16:45:28');

-- --------------------------------------------------------

--
-- Table structure for table `triage`
--

CREATE TABLE `triage` (
  `TriageId` int(11) NOT NULL,
  `VisitId` int(11) NOT NULL,
  `Weight` decimal(5,2) NOT NULL,
  `Height` decimal(5,2) NOT NULL,
  `Temperature` decimal(4,2) NOT NULL,
  `HeartRate` int(11) NOT NULL,
  `Status` varchar(100) NOT NULL DEFAULT 'Pending',
  `RecordedAt` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `triage`
--

INSERT INTO `triage` (`TriageId`, `VisitId`, `Weight`, `Height`, `Temperature`, `HeartRate`, `Status`, `RecordedAt`) VALUES
(50, 80, 55.00, 165.00, 36.90, 68, 'sent to lab', '2025-03-27 16:36:42'),
(51, 81, 45.00, 115.00, 36.90, 66, 'Diagnosed', '2025-03-27 16:44:16'),
(52, 82, 55.00, 175.00, 36.90, 64, 'Pending', '2025-03-27 16:51:21');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(100) NOT NULL,
  `name` varchar(100) NOT NULL,
  `user_role` varchar(100) NOT NULL,
  `user_password` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `name`, `user_role`, `user_password`) VALUES
(7, 'Justin Owen', 'Triage Nurse', '$2y$10$068uER1wZbGgzmXNAQSgIepYMQLZMbRNe8i/HM4sRi6Y2bvCvM68q'),
(8, 'Lisa Molly', 'Doctor', '$2y$10$csv1439zDu2FCSAv0fMQBOOi.1XJ9RsLlw.SqH1oUq51lmUuJjx1e'),
(9, 'Simon Trevor', 'Lab Technician', '$2y$10$fTs1TK.D4Y5sGfEcn7boIOkM91hfNRj2wrRcYidjPAxd0Ou1ziNUy'),
(10, 'Julia Joanne', 'Pharmacy', '$2y$10$JB0W51R9hKeUHYK5Id4LrOROioxGwlXYFq47znadhTHCo14GR3Zgq'),
(11, 'Phill Warren', 'Billing Officer', '$2y$10$yoSyGPYnufzcuokrXUkkP.IJTSm41fS.KkmL6W4ZrUZEYDj.frIde'),
(12, 'Sally Vanessa', 'Receptionist', '$2y$10$SEumF66/kYjDqpeBIUkste0HTFUBfN3UwctVioJQHH5q/rl48.6R2'),
(13, 'admin', 'admin', '$2y$10$pzcdj/vfTYInut8Xnj1TgOqfLLxL1UUEp99mia0LVqe7F4mi9zh/W');

-- --------------------------------------------------------

--
-- Table structure for table `visits`
--

CREATE TABLE `visits` (
  `VisitID` int(11) NOT NULL,
  `PatientID` varchar(10) NOT NULL,
  `A_Id` varchar(100) DEFAULT 'Null',
  `CreatedAt` timestamp NOT NULL DEFAULT current_timestamp(),
  `Status` varchar(20) DEFAULT 'Pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `visits`
--

INSERT INTO `visits` (`VisitID`, `PatientID`, `A_Id`, `CreatedAt`, `Status`) VALUES
(80, '157', 'Null', '2025-03-27 16:35:31', 'Diagnosed'),
(81, '158', 'Null', '2025-03-27 16:43:28', 'Attended'),
(82, '159', '15', '2025-03-27 16:50:26', 'Attended');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `appointment`
--
ALTER TABLE `appointment`
  ADD PRIMARY KEY (`A_Id`);

--
-- Indexes for table `billing`
--
ALTER TABLE `billing`
  ADD PRIMARY KEY (`bill_ID`);

--
-- Indexes for table `diagnoses`
--
ALTER TABLE `diagnoses`
  ADD PRIMARY KEY (`DiagnosisID`),
  ADD KEY `VisitID` (`VisitID`);

--
-- Indexes for table `human_resource`
--
ALTER TABLE `human_resource`
  ADD PRIMARY KEY (`EmployeeID`),
  ADD UNIQUE KEY `PhoneNumber` (`PhoneNumber`),
  ADD UNIQUE KEY `Email` (`Email`);

--
-- Indexes for table `invoice`
--
ALTER TABLE `invoice`
  ADD PRIMARY KEY (`invoice_ID`),
  ADD KEY `bill_ID` (`bill_ID`);

--
-- Indexes for table `issued_medications`
--
ALTER TABLE `issued_medications`
  ADD PRIMARY KEY (`issued_id`);

--
-- Indexes for table `lab`
--
ALTER TABLE `lab`
  ADD PRIMARY KEY (`LabId`);

--
-- Indexes for table `lab_requests`
--
ALTER TABLE `lab_requests`
  ADD PRIMARY KEY (`Test_Request_ID`);

--
-- Indexes for table `lab_results`
--
ALTER TABLE `lab_results`
  ADD PRIMARY KEY (`Test_Result_ID`),
  ADD KEY `Test_Request_ID` (`Test_Request_ID`);

--
-- Indexes for table `patients`
--
ALTER TABLE `patients`
  ADD PRIMARY KEY (`PatientID`);

--
-- Indexes for table `pharmacy_inventory`
--
ALTER TABLE `pharmacy_inventory`
  ADD PRIMARY KEY (`ItemID`);

--
-- Indexes for table `prescriptions`
--
ALTER TABLE `prescriptions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `VisitID` (`VisitID`);

--
-- Indexes for table `prescription_medicines`
--
ALTER TABLE `prescription_medicines`
  ADD PRIMARY KEY (`id`),
  ADD KEY `prescription_id` (`prescription_id`);

--
-- Indexes for table `triage`
--
ALTER TABLE `triage`
  ADD PRIMARY KEY (`TriageId`),
  ADD KEY `VisitId` (`VisitId`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`);

--
-- Indexes for table `visits`
--
ALTER TABLE `visits`
  ADD PRIMARY KEY (`VisitID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `appointment`
--
ALTER TABLE `appointment`
  MODIFY `A_Id` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `billing`
--
ALTER TABLE `billing`
  MODIFY `bill_ID` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=67;

--
-- AUTO_INCREMENT for table `diagnoses`
--
ALTER TABLE `diagnoses`
  MODIFY `DiagnosisID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `human_resource`
--
ALTER TABLE `human_resource`
  MODIFY `EmployeeID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `invoice`
--
ALTER TABLE `invoice`
  MODIFY `invoice_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=62;

--
-- AUTO_INCREMENT for table `issued_medications`
--
ALTER TABLE `issued_medications`
  MODIFY `issued_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `lab`
--
ALTER TABLE `lab`
  MODIFY `LabId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `lab_requests`
--
ALTER TABLE `lab_requests`
  MODIFY `Test_Request_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT for table `lab_results`
--
ALTER TABLE `lab_results`
  MODIFY `Test_Result_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `patients`
--
ALTER TABLE `patients`
  MODIFY `PatientID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=160;

--
-- AUTO_INCREMENT for table `pharmacy_inventory`
--
ALTER TABLE `pharmacy_inventory`
  MODIFY `ItemID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT for table `prescriptions`
--
ALTER TABLE `prescriptions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

--
-- AUTO_INCREMENT for table `prescription_medicines`
--
ALTER TABLE `prescription_medicines`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=68;

--
-- AUTO_INCREMENT for table `triage`
--
ALTER TABLE `triage`
  MODIFY `TriageId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=53;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `visits`
--
ALTER TABLE `visits`
  MODIFY `VisitID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=83;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `invoice`
--
ALTER TABLE `invoice`
  ADD CONSTRAINT `invoice_ibfk_1` FOREIGN KEY (`bill_ID`) REFERENCES `billing` (`bill_ID`) ON DELETE CASCADE;

--
-- Constraints for table `prescriptions`
--
ALTER TABLE `prescriptions`
  ADD CONSTRAINT `prescriptions_ibfk_1` FOREIGN KEY (`VisitID`) REFERENCES `visits` (`VisitID`) ON DELETE CASCADE;

--
-- Constraints for table `prescription_medicines`
--
ALTER TABLE `prescription_medicines`
  ADD CONSTRAINT `prescription_medicines_ibfk_1` FOREIGN KEY (`prescription_id`) REFERENCES `prescriptions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `triage`
--
ALTER TABLE `triage`
  ADD CONSTRAINT `triage_ibfk_1` FOREIGN KEY (`VisitId`) REFERENCES `visits` (`VisitID`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
