-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 16, 2025 at 08:06 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `garden`
--

DELIMITER $$
--
-- Procedures
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `GetRevenue` (IN `period_type` VARCHAR(10))   BEGIN
    IF period_type = 'year' THEN
        SELECT YEAR(order_date) AS year, SUM(total_price) AS total_revenue
        FROM orders
        WHERE status = 'delivered'
        GROUP BY YEAR(order_date)
        ORDER BY year DESC;
        
    ELSEIF period_type = 'month' THEN
        SELECT YEAR(order_date) AS year, MONTH(order_date) AS month, SUM(total_price) AS total_revenue
        FROM orders
        WHERE status = 'delivered'
        GROUP BY YEAR(order_date), MONTH(order_date)
        ORDER BY year DESC, month DESC;

    ELSEIF period_type = 'week' THEN
        SELECT YEAR(order_date) AS year, WEEK(order_date) AS week, SUM(total_price) AS total_revenue
        FROM orders
        WHERE status = 'delivered'
        GROUP BY YEAR(order_date), WEEK(order_date)
        ORDER BY year DESC, week DESC;
        
    ELSEIF period_type = 'current_year' THEN
        SELECT YEAR(order_date) AS year, SUM(total_price) AS total_revenue
        FROM orders
        WHERE status = 'delivered' AND YEAR(order_date) = YEAR(CURDATE())
        GROUP BY YEAR(order_date);

    ELSEIF period_type = 'current_month' THEN
        SELECT YEAR(order_date) AS year, MONTH(order_date) AS month, SUM(total_price) AS total_revenue
        FROM orders
        WHERE status = 'delivered' AND YEAR(order_date) = YEAR(CURDATE()) AND MONTH(order_date) = MONTH(CURDATE())
        GROUP BY YEAR(order_date), MONTH(order_date);

    ELSEIF period_type = 'current_week' THEN
        SELECT YEAR(order_date) AS year, WEEK(order_date) AS week, SUM(total_price) AS total_revenue
        FROM orders
        WHERE status = 'delivered' AND YEAR(order_date) = YEAR(CURDATE()) AND WEEK(order_date) = WEEK(CURDATE())
        GROUP BY YEAR(order_date), WEEK(order_date);

    END IF;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

CREATE TABLE `cart` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `added_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `cart`
--

INSERT INTO `cart` (`id`, `user_id`, `product_id`, `quantity`, `added_at`) VALUES
(2, 2, 8, 4, '2025-02-08 13:49:35'),
(5, 2, 1, 2, '2025-02-10 13:58:11'),
(10, 2, 17, 1, '2025-02-14 14:10:20');

-- --------------------------------------------------------

--
-- Table structure for table `contact_form`
--

CREATE TABLE `contact_form` (
  `id` int(11) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `phone_number` varchar(15) NOT NULL,
  `email` varchar(100) NOT NULL,
  `message` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `contact_form`
--

INSERT INTO `contact_form` (`id`, `first_name`, `phone_number`, `email`, `message`, `created_at`) VALUES
(1, 'John Doe', '9876543210', 'john.doe@example.com', 'Do you offer discounts for bulk purchases?', '2025-01-31 11:33:20'),
(2, 'Alice Smith', '8765432109', 'alice.smith@example.com', 'Is there a warranty on gardening tools?', '2025-01-31 11:33:20'),
(3, 'Michael Johnson', '7654321098', 'michael.johnson@example.com', 'When will the hedge trimmer be back in stock?', '2025-01-31 11:33:20'),
(4, 'Emily Davis', '6543210987', 'emily.davis@example.com', 'Can I track my order shipment?', '2025-01-31 11:33:20'),
(5, 'Robert Brown', '5432109876', 'robert.brown@example.com', 'Do you provide free delivery for garden tools?', '2025-01-31 11:33:20'),
(6, 'Sophia Wilson', '4321098765', 'sophia.wilson@example.com', 'What is the return policy for damaged tools?', '2025-01-31 11:33:20'),
(7, 'David Martinez', '3210987654', 'david.martinez@example.com', 'Do you have an offline store?', '2025-01-31 11:33:20'),
(8, 'Olivia Taylor', '2109876543', 'olivia.taylor@example.com', 'Looking for a bulk purchase of shovels and rakes.', '2025-01-31 11:33:20'),
(9, 'James Anderson', '1098765432', 'james.anderson@example.com', 'Do you sell eco-friendly gardening tools?', '2025-01-31 11:33:20'),
(10, 'Charlotte Thomas', '9876012345', 'charlotte.thomas@example.com', 'How can I cancel my order?', '2025-01-31 11:33:20'),
(11, 'Daniel White', '8765012345', 'daniel.white@example.com', 'Do you have pruning shears with ergonomic handles?', '2025-01-31 11:33:20'),
(12, 'Emma Harris', '7654012345', 'emma.harris@example.com', 'Can I get a discount on first-time purchase?', '2025-01-31 11:33:20'),
(13, 'Matthew Clark', '6543012345', 'matthew.clark@example.com', 'Do you offer gardening tool sets for beginners?', '2025-01-31 11:33:20'),
(14, 'Ava Lewis', '5432012345', 'ava.lewis@example.com', 'What are the payment options available?', '2025-01-31 11:33:20'),
(15, 'Ethan Walker', '4321012345', 'ethan.walker@example.com', 'I received the wrong product, how to return it?', '2025-01-31 11:33:20'),
(16, 'Isabella Hall', '3210012345', 'isabella.hall@example.com', 'Do you have an affiliate program?', '2025-01-31 11:33:20'),
(17, 'Mason Allen', '2109012345', 'mason.allen@example.com', 'Looking for long-handled weeding tools, any suggestions?', '2025-01-31 11:33:20'),
(18, 'Sophia Young', '1098012345', 'sophia.young@example.com', 'Can I change my shipping address after placing an order?', '2025-01-31 11:33:20'),
(19, 'Liam King', '9876912345', 'liam.king@example.com', 'What is the durability of your stainless steel garden tools?', '2025-01-31 11:33:20'),
(20, 'Mia Scott', '8765812345', 'mia.scott@example.com', 'Do you sell organic soil testing kits?', '2025-01-31 11:33:20'),
(21, 'Benjamin Green', '7654712345', 'benjamin.green@example.com', 'Looking for a professional gardening toolkit.', '2025-01-31 11:33:20'),
(22, 'Abigail Adams', '6543612345', 'abigail.adams@example.com', 'Is there a membership program with exclusive deals?', '2025-01-31 11:33:20'),
(23, 'Jacob Baker', '5432512345', 'jacob.baker@example.com', 'Do you sell children-friendly gardening tools?', '2025-01-31 11:33:20'),
(24, 'Harper Nelson', '4321412345', 'harper.nelson@example.com', 'Can I pre-order out-of-stock products?', '2025-01-31 11:33:20');

-- --------------------------------------------------------

--
-- Table structure for table `issues`
--

CREATE TABLE `issues` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `issue` text NOT NULL,
  `reported_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `issues`
--

INSERT INTO `issues` (`id`, `name`, `email`, `issue`, `reported_at`) VALUES
(1, 'John Doe', 'john.doe@example.com', 'Image gallery not loading properly', '2025-01-31 10:08:17'),
(2, 'Michael Johnson', 'michael.johnson@example.com', 'Unable to submit the contact form', '2025-01-31 10:08:17'),
(3, 'Emily Davis', 'emily.davis@example.com', 'Slow loading time on the homepage', '2025-01-31 10:08:17'),
(4, 'Robert Brown', 'robert.brown@example.com', 'Missing plant category filters', '2025-01-31 10:08:17'),
(5, 'Sophia Wilson', 'sophia.wilson@example.com', 'Search functionality not working', '2025-01-31 10:08:17'),
(6, 'David Martinez', 'david.martinez@example.com', 'Product images not displaying in the store', '2025-01-31 10:08:17'),
(7, 'Olivia Taylor', 'olivia.taylor@example.com', 'Error while adding plants to the cart', '2025-01-31 10:08:17'),
(8, 'James Anderson', 'james.anderson@example.com', 'FAQs page is showing a 404 error', '2025-01-31 10:08:17'),
(9, 'Charlotte Thomas', 'charlotte.thomas@example.com', 'User registration form validation issue', '2025-01-31 10:08:17'),
(10, 'Daniel White', 'daniel.white@example.com', 'Garden blog images not loading on mobile', '2025-01-31 10:08:17'),
(11, 'Emma Harris', 'emma.harris@example.com', 'Payment gateway issue on checkout', '2025-01-31 10:08:17'),
(12, 'Matthew Clark', 'matthew.clark@example.com', 'Header menu overlaps on smaller screens', '2025-01-31 10:08:17'),
(13, 'Ava Lewis', 'ava.lewis@example.com', 'Newsletter subscription confirmation email not received', '2025-01-31 10:08:17'),
(14, 'Ethan Walker', 'ethan.walker@example.com', 'Plant care guides are missing descriptions', '2025-01-31 10:08:17'),
(15, 'Isabella Hall', 'isabella.hall@example.com', 'Website layout breaks on Safari browser', '2025-01-31 10:08:17'),
(16, 'Mason Allen', 'mason.allen@example.com', 'Plant images take too long to load', '2025-01-31 10:08:17'),
(17, 'Sophia Young', 'sophia.young@example.com', 'Unable to post comments on the gardening blog', '2025-01-31 10:08:17'),
(18, 'Liam King', 'liam.king@example.com', 'Broken video tutorials on the homepage', '2025-01-31 10:08:17'),
(19, 'Mia Scott', 'mia.scott@example.com', 'Navigation bar is not sticky while scrolling', '2025-01-31 10:08:17'),
(20, 'Benjamin Green', 'benjamin.green@example.com', 'Wishlist feature not saving selected items', '2025-01-31 10:08:17'),
(21, 'Abigail Adams', 'abigail.adams@example.com', 'Some plants have incorrect care instructions', '2025-01-31 10:08:17'),
(22, 'Jacob Baker', 'jacob.baker@example.com', 'Dark mode option missing in settings', '2025-01-31 10:08:17'),
(23, 'Harper Nelson', 'harper.nelson@example.com', 'Checkout page showing incorrect tax calculations', '2025-01-31 10:08:17'),
(24, 'Alexander Carter', 'alexander.carter@example.com', 'Broken links to gardening tools suppliers', '2025-01-31 10:08:17'),
(25, 'Ella Mitchell', 'ella.mitchell@example.com', 'Incorrect plant names in product descriptions', '2025-01-31 10:08:17'),
(26, 'Lucas Perez', 'lucas.perez@example.com', 'Live chat support is not working', '2025-01-31 10:08:17'),
(27, 'Avery Roberts', 'avery.roberts@example.com', 'Mobile menu does not expand on some devices', '2025-01-31 10:08:17'),
(28, 'William Evans', 'william.evans@example.com', 'Error while filtering plants by sunlight requirement', '2025-01-31 10:08:17'),
(29, 'Madison Collins', 'madison.collins@example.com', 'Some pages show outdated gardening tips', '2025-01-31 10:08:17'),
(30, 'Alice Smith', 'alice.smith@example.com', 'Broken links on the plant care page', '2025-01-31 10:08:17');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `order_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `order_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `delivery_date` date NOT NULL,
  `status` varchar(50) DEFAULT 'pending',
  `total_price` decimal(10,2) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `product_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`order_id`, `user_id`, `order_date`, `delivery_date`, `status`, `total_price`, `quantity`, `product_id`) VALUES
(1, 2, '2024-12-31 18:30:00', '2025-01-06', 'delivered', 60.00, 1, 1),
(2, 3, '2025-01-01 18:30:00', '2025-01-07', 'delivered', 80.00, 2, 2),
(3, 4, '2025-01-02 18:30:00', '2025-01-08', 'delivered', 80.00, 1, 3),
(4, 5, '2025-01-03 18:30:00', '2025-01-09', 'delivered', 80.00, 3, 4),
(5, 6, '2025-01-04 18:30:00', '2025-01-10', 'delivered', 69.00, 1, 5),
(6, 7, '2025-01-05 18:30:00', '2025-01-11', 'delivered', 500.00, 2, 6),
(7, 8, '2025-01-06 18:30:00', '2025-01-12', 'delivered', 700.00, 1, 7),
(8, 9, '2025-01-07 18:30:00', '2025-01-13', 'delivered', 600.00, 3, 8),
(9, 10, '2025-01-08 18:30:00', '2025-01-14', 'delivered', 800.00, 2, 9),
(10, 11, '2025-01-09 18:30:00', '2025-01-15', 'delivered', 700.00, 1, 10),
(11, 12, '2025-01-10 18:30:00', '2025-01-16', 'delivered', 650.00, 2, 11),
(12, 13, '2025-01-11 18:30:00', '2025-01-17', 'delivered', 750.00, 1, 12),
(13, 14, '2025-01-12 18:30:00', '2025-01-18', 'delivered', 400.00, 2, 13),
(14, 15, '2025-01-13 18:30:00', '2025-01-19', 'delivered', 680.00, 1, 14),
(15, 16, '2025-01-14 18:30:00', '2025-01-20', 'delivered', 700.00, 2, 15),
(16, 17, '2025-01-15 18:30:00', '2025-01-21', 'delivered', 72.00, 3, 16),
(17, 18, '2025-01-16 18:30:00', '2025-01-22', 'delivered', 6500.00, 1, 17),
(18, 19, '2025-01-17 18:30:00', '2025-01-23', 'delivered', 0.00, 1, 18),
(19, 20, '2025-01-18 18:30:00', '2025-01-24', 'delivered', 200.00, 2, 19),
(20, 2, '2025-01-19 18:30:00', '2025-01-25', 'delivered', 60.00, 1, 1),
(21, 3, '2025-01-20 18:30:00', '2025-01-26', 'delivered', 80.00, 2, 2),
(22, 4, '2025-01-21 18:30:00', '2025-01-27', 'delivered', 80.00, 1, 3),
(23, 5, '2025-01-22 18:30:00', '2025-01-28', 'delivered', 80.00, 3, 4),
(24, 6, '2025-01-23 18:30:00', '2025-01-29', 'delivered', 69.00, 1, 5),
(25, 7, '2025-01-24 18:30:00', '2025-01-30', 'delivered', 500.00, 2, 6),
(26, 8, '2025-01-25 18:30:00', '2025-01-31', 'delivered', 700.00, 1, 7),
(27, 9, '2025-01-26 18:30:00', '2025-02-01', 'delivered', 600.00, 3, 8),
(28, 10, '2025-01-27 18:30:00', '2025-02-02', 'delivered', 800.00, 2, 9),
(29, 11, '2025-01-28 18:30:00', '2025-02-03', 'delivered', 700.00, 1, 10),
(30, 12, '2025-01-29 18:30:00', '2025-02-04', 'delivered', 650.00, 2, 11),
(31, 13, '2025-01-30 18:30:00', '2025-02-05', 'delivered', 750.00, 1, 12),
(32, 14, '2025-01-31 18:30:00', '2025-02-06', 'delivered', 400.00, 2, 13),
(33, 15, '2025-02-01 18:30:00', '2025-02-07', 'delivered', 680.00, 1, 14),
(34, 16, '2025-02-02 18:30:00', '2025-02-08', 'delivered', 700.00, 2, 15),
(35, 17, '2025-02-03 18:30:00', '2025-02-09', 'delivered', 72.00, 3, 16),
(36, 18, '2025-02-04 18:30:00', '2025-02-10', 'delivered', 6500.00, 1, 17),
(37, 19, '2025-02-05 18:30:00', '2025-02-11', 'delivered', 0.00, 1, 18),
(38, 20, '2025-02-06 18:30:00', '2025-02-12', 'delivered', 200.00, 2, 19),
(39, 2, '2025-02-07 18:30:00', '2025-02-13', 'delivered', 60.00, 1, 1),
(40, 3, '2025-02-08 18:30:00', '2025-02-14', 'delivered', 80.00, 2, 2),
(41, 4, '2025-02-09 18:30:00', '2025-02-15', 'delivered', 80.00, 1, 3),
(42, 2, '2025-02-16 06:58:22', '2025-02-21', 'cancelled', 4000.00, 5, 9),
(43, 2, '2025-02-16 06:58:28', '2025-02-21', 'cancelled', 414.00, 6, 5),
(44, 2, '2025-02-16 06:58:34', '2025-02-21', 'ongoing', 2040.00, 3, 14),
(45, 2, '2025-02-16 06:58:38', '2025-02-21', 'ongoing', 80.00, 1, 2),
(46, 2, '2025-02-16 06:58:40', '2025-02-21', 'cancelled', 160.00, 2, 3),
(47, 2, '2025-02-16 06:58:42', '2025-02-21', 'pending', 700.00, 1, 7),
(48, 2, '2025-02-16 06:58:46', '2025-02-21', 'pending', 288.00, 4, 16),
(49, 2, '2025-02-16 06:58:53', '2025-02-21', 'ongoing', 640.00, 8, 4);

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `image` varchar(255) NOT NULL,
  `current_price` decimal(10,2) NOT NULL,
  `original_price` decimal(10,2) NOT NULL,
  `is_on_sale` tinyint(1) NOT NULL DEFAULT 0,
  `quantity` int(11) NOT NULL DEFAULT 0,
  `sold` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `name`, `image`, `current_price`, `original_price`, `is_on_sale`, `quantity`, `sold`) VALUES
(1, 'Garden Tool Set', 'products/3.webp', 60.00, 75.00, 0, 350, 3),
(2, 'Welly Clog', 'products/5.jpg', 80.00, 100.00, 1, 200, 7),
(3, 'Clener', 'products/4.webp', 80.00, 90.00, 0, 400, 3),
(4, 'No bend weed remover', 'products/6.webp', 80.00, 100.00, 1, 500, 14),
(5, 'Rabbit with Basket Ornament', 'products/7.webp', 69.00, 90.00, 0, 100, 2),
(6, 'Drain Covers', 'products/8.jpg', 500.00, 590.11, 0, 500, 4),
(7, 'Trio of ducks', 'products/9.jpg', 700.00, 790.00, 1, 50, 3),
(8, 'cobble edging', 'products/10.jpg', 600.00, 690.10, 0, 40, 6),
(9, 'Leaf Grabber', 'products/11.jpg', 800.00, 890.00, 1, 100, 4),
(10, 'Garden Brushes', 'products/12.webp', 700.00, 777.00, 1, 200, 2),
(11, 'Foldable Pruning', 'products/13.webp', 650.00, 700.00, 1, 200, 4),
(12, 'Tree Lopper', 'products/14.webp', 750.00, 765.00, 1, 500, 2),
(13, 'Axe', 'products/16.jpg', 400.00, 444.00, 1, 100, 4),
(14, 'Flat Brass', 'products/17.jpg', 680.00, 700.00, 1, 120, 5),
(15, 'Waterfall Watering', 'products/18.jpg', 700.00, 723.00, 1, 300, 4),
(16, 'Pro Gloves', 'products/19.jpg', 72.00, 89.99, 1, 200, 10),
(17, 'Tool Box', 'products/20.webp', 6500.00, 6999.00, 1, 320, 2),
(19, 'hi', 'uploads/profile.jpg', 200.00, 250.00, 1, 200, 4);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `img` varchar(50) DEFAULT NULL,
  `NIC` varchar(12) DEFAULT NULL,
  `DOB` date DEFAULT NULL,
  `Address` varchar(255) DEFAULT NULL,
  `Gender` varchar(10) DEFAULT NULL,
  `phone` varchar(15) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `first_name`, `last_name`, `email`, `password`, `img`, `NIC`, `DOB`, `Address`, `Gender`, `phone`) VALUES
(1, 'Admin', 'User', 'admin@gmail.com', '$2y$10$IXdEyxNeox8iNEK1WLmeAO9ekC8mac40Sh.3cOxiKKzrzGolrS3KO', 'profiles/admin.jpg', '200031900500', '2000-02-13', '123 Main St, City A', 'Male', NULL),
(2, 'Lax', 'Doe', 'lax@gmail.com', '$2y$10$IXdEyxNeox8iNEK1WLmeAO9ekC8mac40Sh.3cOxiKKzrzGolrS3KO', 'profiles/profile.jpg', '920385192V', '2000-02-10', '123 Main St, City A', 'Male', '0754750000'),
(3, 'Jane', 'Smith', 'janesmith2@example.com', '$2y$10$hjba71atDoukDjM.4v6lM.EPVbF8Vzsm9oJWoQAtw/2z1Q2isGqZ6', 'profiles/User.jpg', '810375291V', '2001-02-13', '456 Oak St, City B', 'Female', NULL),
(4, 'Michael', 'Brown', 'michaelbrown3@example.com', '$2y$10$hjba71atDoukDjM.4v6lM.EPVbF8Vzsm9oJWoQAtw/2z1Q2isGqZ6', 'profiles/User.jpg', '700482910V', '2002-04-13', '789 Pine St, City C', 'Male', NULL),
(5, 'Emily', 'Davis', 'emilydavis4@example.com', '$2y$10$hjba71atDoukDjM.4v6lM.EPVbF8Vzsm9oJWoQAtw/2z1Q2isGqZ6', 'profiles/User.jpg', '930284756V', '2000-02-07', '234 Birch St, City D', 'Female', NULL),
(6, 'David', 'Wilson', 'davidwilson5@example.com', '$2y$10$hjba71atDoukDjM.4v6lM.EPVbF8Vzsm9oJWoQAtw/2z1Q2isGqZ6', 'profiles/User.jpg', '820194735V', '1999-07-08', '567 Maple St, City E', 'Male', NULL),
(7, 'Olivia', 'Martinez', 'oliviamartinez6@example.com', '$2y$10$hjba71atDoukDjM.4v6lM.EPVbF8Vzsm9oJWoQAtw/2z1Q2isGqZ6', 'profiles/User.jpg', '900583271V', '1990-01-03', '789 Cedar St, City F', 'Female', NULL),
(8, 'James', 'Anderson', 'jamesanderson7@example.com', '$2y$10$hjba71atDoukDjM.4v6lM.EPVbF8Vzsm9oJWoQAtw/2z1Q2isGqZ6', 'profiles/User.jpg', '910284653V', '1998-02-23', '345 Walnut St, City G', 'Male', NULL),
(9, 'Sophia', 'Thomas', 'sophiathomas8@example.com', '$2y$10$hjba71atDoukDjM.4v6lM.EPVbF8Vzsm9oJWoQAtw/2z1Q2isGqZ6', 'profiles/User.jpg', '820375926V', '0000-00-00', '678 Chestnut St, City H', 'Female', NULL),
(10, 'Benjamin', 'Taylor', 'benjamintaylor9@example.com', '$2y$10$hjba71atDoukDjM.4v6lM.EPVbF8Vzsm9oJWoQAtw/2z1Q2isGqZ6', 'profiles/User.jpg', '870291836V', '2003-10-27', '910 Spruce St, City I', 'Male', NULL),
(11, 'Ava', 'Harris', 'avaharris10@example.com', '$2y$10$hjba71atDoukDjM.4v6lM.EPVbF8Vzsm9oJWoQAtw/2z1Q2isGqZ6', 'profiles/User.jpg', '930284765V', '2010-12-11', '112 Willow St, City J', 'Female', NULL),
(12, 'William', 'Moore', 'williammoore11@example.com', '$2y$10$hjba71atDoukDjM.4v6lM.EPVbF8Vzsm9oJWoQAtw/2z1Q2isGqZ6', 'profiles/User.jpg', '850472938V', '2007-11-09', '123 Elm St, City K', 'Male', NULL),
(13, 'Mia', 'White', 'miawhite12@example.com', '$2y$10$hjba71atDoukDjM.4v6lM.EPVbF8Vzsm9oJWoQAtw/2z1Q2isGqZ6', 'profiles/User.jpg', '800394857V', '0198-10-10', '234 Ash St, City L', 'Female', NULL),
(14, 'Alexander', 'Clark', 'alexanderclark13@example.com', '$2y$10$hjba71atDoukDjM.4v6lM.EPVbF8Vzsm9oJWoQAtw/2z1Q2isGqZ6', 'profiles/User.jpg', '920184756V', '1987-09-13', '345 Redwood St, City M', 'Male', NULL),
(15, 'Charlotte', 'Lewis', 'charlottelewis14@example.com', '$2y$10$hjba71atDoukDjM.4v6lM.EPVbF8Vzsm9oJWoQAtw/2z1Q2isGqZ6', 'profiles/User.jpg', '830275938V', '1990-08-13', '567 Oakwood St, City N', 'Female', NULL),
(16, 'Henry', 'Walker', 'henrywalker15@example.com', '$2y$10$hjba71atDoukDjM.4v6lM.EPVbF8Vzsm9oJWoQAtw/2z1Q2isGqZ6', 'profiles/User.jpg', '890384726V', '1998-05-22', '678 Pinewood St, City O', 'Male', NULL),
(17, 'Amelia', 'Hall', 'ameliahall16@example.com', '$2y$10$hjba71atDoukDjM.4v6lM.EPVbF8Vzsm9oJWoQAtw/2z1Q2isGqZ6', 'profiles/User.jpg', '910293847V', '0000-00-00', '789 Maplewood St, City P', 'Female', NULL),
(18, 'Daniel', 'Allen', 'danielallen17@example.com', '$2y$10$hjba71atDoukDjM.4v6lM.EPVbF8Vzsm9oJWoQAtw/2z1Q2isGqZ6', 'profiles/User.jpg', '870184625V', '2000-11-09', '890 Cedarwood St, City Q', 'Male', NULL),
(19, 'Grace', 'Young', 'graceyoung18@example.com', '$2y$10$hjba71atDoukDjM.4v6lM.EPVbF8Vzsm9oJWoQAtw/2z1Q2isGqZ6', 'profiles/User.jpg', '820395716V', '2001-06-06', '901 Birchwood St, City R', 'Female', NULL),
(20, 'Matthew', 'King', 'matthewking19@example.com', '$2y$10$hjba71atDoukDjM.4v6lM.EPVbF8Vzsm9oJWoQAtw/2z1Q2isGqZ6', 'profiles/User.jpg', '880293847V', '1998-09-30', '234 Walnutwood St, City S', 'Male', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `contact_form`
--
ALTER TABLE `contact_form`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `issues`
--
ALTER TABLE `issues`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`order_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `fk_product_id` (`product_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `cart`
--
ALTER TABLE `cart`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=179;

--
-- AUTO_INCREMENT for table `contact_form`
--
ALTER TABLE `contact_form`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `issues`
--
ALTER TABLE `issues`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2462;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=61;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `cart`
--
ALTER TABLE `cart`
  ADD CONSTRAINT `cart_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cart_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
