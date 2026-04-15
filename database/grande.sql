-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 15, 2026 at 05:59 AM
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
-- Database: `grande`
--

-- --------------------------------------------------------

--
-- Table structure for table `audit_logs`
--

CREATE TABLE `audit_logs` (
  `id` int(10) UNSIGNED NOT NULL,
  `actor_user_id` int(10) UNSIGNED DEFAULT NULL,
  `action` varchar(100) NOT NULL,
  `entity_type` varchar(50) NOT NULL,
  `entity_id` int(10) UNSIGNED DEFAULT NULL,
  `details_json` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`details_json`)),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `audit_logs`
--

INSERT INTO `audit_logs` (`id`, `actor_user_id`, `action`, `entity_type`, `entity_id`, `details_json`, `created_at`) VALUES
(28, 5, 'order_status_updated', 'order', 8, '{\"order_number\":\"GR-20260411012150-67A0\",\"status\":\"completed\"}', '2026-04-11 17:08:06'),
(29, 5, 'customer_reservation_cancelled', 'reservation', 3, '{\"from_status\":\"pending\",\"to_status\":\"cancelled\",\"cancelled_order_count\":0}', '2026-04-11 17:14:24'),
(30, 158, 'payment_verified', 'order', 191, '{\"order_number\":\"GR-20260412010742-0145\",\"payment_status\":\"verified\"}', '2026-04-11 17:16:55'),
(31, 158, 'order_status_updated', 'order', 191, '{\"order_number\":\"GR-20260412010742-0145\",\"status\":\"completed\"}', '2026-04-11 17:17:14'),
(32, 158, 'order_status_updated', 'order', 191, '{\"order_number\":\"GR-20260412010742-0145\",\"status\":\"pending\"}', '2026-04-11 17:20:07'),
(33, 158, 'order_status_updated', 'order', 191, '{\"order_number\":\"GR-20260412010742-0145\",\"status\":\"preparing\"}', '2026-04-11 17:20:32'),
(34, 158, 'order_status_updated', 'order', 191, '{\"order_number\":\"GR-20260412010742-0145\",\"status\":\"ready\"}', '2026-04-11 17:20:38'),
(35, 158, 'order_status_updated', 'order', 191, '{\"order_number\":\"GR-20260412010742-0145\",\"status\":\"completed\"}', '2026-04-11 17:20:48'),
(36, 5, 'order_status_updated', 'order', 191, '{\"order_number\":\"GR-20260412010742-0145\",\"status\":\"completed\"}', '2026-04-11 17:20:52'),
(37, 158, 'payment_verified', 'order', 192, '{\"order_number\":\"GR-20260412012358-6124\",\"payment_status\":\"verified\"}', '2026-04-11 17:25:09'),
(38, 158, 'reservation_status_updated', 'reservation', 122, '{\"status\":\"confirmed\"}', '2026-04-11 17:25:22'),
(39, 158, 'reservation_status_updated', 'reservation', 122, '{\"status\":\"completed\"}', '2026-04-11 17:25:43'),
(40, 158, 'order_status_updated', 'order', 192, '{\"order_number\":\"GR-20260412012358-6124\",\"status\":\"completed\"}', '2026-04-11 17:25:48');

-- --------------------------------------------------------

--
-- Table structure for table `cart_items`
--

CREATE TABLE `cart_items` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `menu_item_id` int(10) UNSIGNED NOT NULL,
  `item_name` varchar(100) NOT NULL,
  `item_price` decimal(10,2) NOT NULL,
  `size` varchar(20) DEFAULT 'Default',
  `quantity` int(10) UNSIGNED NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `cart_items`
--

INSERT INTO `cart_items` (`id`, `user_id`, `menu_item_id`, `item_name`, `item_price`, `size`, `quantity`, `created_at`, `updated_at`) VALUES
(58, 6, 9, 'Caffe Mocha', 120.00, '12oz', 1, '2026-04-14 18:05:29', '2026-04-14 18:05:29');

-- --------------------------------------------------------

--
-- Table structure for table `customer_order_notifications`
--

CREATE TABLE `customer_order_notifications` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `order_id` int(10) UNSIGNED NOT NULL,
  `previous_status` varchar(50) NOT NULL,
  `new_status` varchar(50) NOT NULL,
  `message` varchar(255) NOT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `customer_reservation_notifications`
--

CREATE TABLE `customer_reservation_notifications` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `reservation_id` int(10) UNSIGNED NOT NULL,
  `previous_status` varchar(50) NOT NULL,
  `new_status` varchar(50) NOT NULL,
  `message` varchar(255) NOT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `feedback`
--

CREATE TABLE `feedback` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED DEFAULT NULL,
  `name` varchar(120) NOT NULL,
  `email` varchar(120) NOT NULL,
  `rating` tinyint(3) UNSIGNED NOT NULL,
  `category` varchar(50) NOT NULL,
  `message` text NOT NULL,
  `status` enum('new','in_review','resolved','archived') NOT NULL DEFAULT 'new',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `feedback`
--

INSERT INTO `feedback` (`id`, `user_id`, `name`, `email`, `rating`, `category`, `message`, `status`, `created_at`, `updated_at`) VALUES
(30, 159, 'QA Customer', 'qa.customer@grande.local', 5, 'service', '[QA] Friendly staff and quick counter flow.', 'new', '2026-04-14 01:15:36', '2026-04-14 02:00:36'),
(31, 159, 'QA Customer', 'qa.customer@grande.local', 4, 'food', '[QA] Ensaymada and coffee pairing looked good in the dashboard.', 'in_review', '2026-04-12 02:00:36', '2026-04-14 02:00:36'),
(32, 159, 'QA Customer', 'qa.customer@grande.local', 5, 'experience', '[QA] Resolved feedback sample for history panels.', 'resolved', '2026-04-07 02:00:36', '2026-04-14 02:00:36');

-- --------------------------------------------------------

--
-- Table structure for table `menu_items`
--

CREATE TABLE `menu_items` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `category` varchar(50) NOT NULL,
  `description` text DEFAULT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `is_available` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `menu_items`
--

INSERT INTO `menu_items` (`id`, `name`, `category`, `description`, `image_url`, `is_available`, `created_at`) VALUES
(2, 'Long Black', 'COFFEE.', 'Rich espresso over hot water - Grande signature.', 'public/images/menu-items/long_black.png', 1, '2025-11-26 00:17:27'),
(3, 'Spanish Latte', 'COFFEE.', 'Sweet and creamy latte with condensed milk.', 'public/images/menu-items/spanish_latte.jpg', 1, '2025-11-26 00:17:27'),
(4, 'Latte', 'COFFEE.', 'Smooth espresso with steamed milk.', 'public/images/menu-items/latte.png', 1, '2025-11-26 00:17:27'),
(5, 'Honey Long Black', 'COFFEE.', 'Long Black sweetened with natural honey.', 'public/images/menu-items/honey_long_black.png', 1, '2025-11-26 00:17:27'),
(6, 'Cappuccino', 'COFFEE.', 'Bold espresso with foam and steamed milk.', 'public/images/menu-items/cappuccino.png', 1, '2025-11-26 00:17:27'),
(7, 'Mocha Hazel', 'COFFEE.', 'Chocolate and hazelnut blended with espresso.', 'public/images/menu-items/mocha_hazel.png', 1, '2025-11-26 00:17:27'),
(8, 'White Macadamia Nut', 'COFFEE.', 'Creamy white chocolate with macadamia flavor.', 'public/images/menu-items/white_macadamia_nut.jpg', 1, '2025-11-26 00:17:27'),
(9, 'Caffe Mocha', 'COFFEE.', 'Classic chocolate and espresso combination.', 'public/images/menu-items/caffe_mocha.png', 1, '2025-11-26 00:17:27'),
(10, 'Seasalt Foam', 'COFFEE.', 'Espresso topped with creamy seasalt foam.', 'public/images/menu-items/seasalt_foam.jpg', 1, '2025-11-26 00:17:27'),
(11, 'Strawberry Latte', 'COFFEE.', 'Fruity strawberry meets creamy latte.', 'public/images/menu-items/strawberry_latte.jpg', 1, '2025-11-26 00:17:27'),
(12, 'Oat Latte', 'COFFEE.', 'Smooth latte made with oat milk.', 'public/images/menu-items/oat_latte.png', 1, '2025-11-26 00:17:27'),
(24, 'Peppermint Longblack', 'COFFEE.', 'Refreshing peppermint with Long Black.', 'public/images/menu-items/peppermint_longblack.jpg', 1, '2026-01-26 16:02:41'),
(25, 'Honey Oat Latte', 'COFFEE.', 'Honey sweetness with creamy oat milk.', 'public/images/menu-items/honey_oat_latte.png', 1, '2026-01-26 16:02:41'),
(26, 'Pistachio Latte', 'COFFEE.', 'Nutty pistachio flavor with espresso.', 'public/images/menu-items/pistachio_latte.jpg', 1, '2026-01-26 16:02:41'),
(27, 'Dirty Mocha Hazel', 'COFFEE.', 'Mocha Hazel with an extra espresso shot.', 'public/images/menu-items/dirty_mocha_hazel.png', 1, '2026-01-26 16:02:41'),
(28, 'Dirty Macadamia Foam', 'COFFEE.', 'White Macadamia with extra espresso shot.', 'public/images/menu-items/dirty_macadamia_foam.png', 1, '2026-01-26 16:02:41'),
(29, 'Dirty Seasalt Foam', 'COFFEE.', 'Seasalt Foam with an extra espresso shot.', 'public/images/menu-items/dirty_seasalt_foam.png', 1, '2026-01-26 16:02:41'),
(30, 'Grande Coffee', 'COFFEE.', 'Our signature house blend coffee.', 'public/images/menu-items/grande_coffee.jpg', 1, '2026-01-26 16:02:41'),
(31, 'Strawberry Milk', 'NON COFFEE.', 'Sweet and creamy strawberry milk.', 'public/images/menu-items/strawberry_milk.jpg', 1, '2026-01-26 16:02:41'),
(32, 'Ginger Honey Tea', 'NON COFFEE.', 'Warming ginger with sweet honey.', 'public/images/menu-items/ginger_honey_tea.jpg', 1, '2026-01-26 16:02:41'),
(33, 'Matcha Foam', 'NON COFFEE.', 'Premium matcha with creamy foam.', 'public/images/menu-items/matcha_foam.jpg', 1, '2026-01-26 16:02:41'),
(34, 'Cinnamon Rice Drink', 'NON COFFEE.', 'Traditional cinnamon rice drink.', 'public/images/menu-items/cinnamon-rice.jpg', 1, '2026-01-26 16:02:41'),
(35, 'Strawberry Chocolate', 'NON COFFEE.', 'Strawberry and chocolate fusion.', 'public/images/menu-items/strawberry_chocolate.jpg', 1, '2026-01-26 16:02:41'),
(36, 'Strawberry Cinnamon Rice', 'NON COFFEE.', 'Fruity twist on classic cinnamon rice drink.', 'public/images/menu-items/strawberry-cinnamon-rice.jpg', 1, '2026-01-26 16:02:41'),
(37, 'Matcha Oat', 'NON COFFEE.', 'Matcha with creamy oat milk.', 'public/images/menu-items/matcha_oat.png', 1, '2026-01-26 16:02:41'),
(38, 'Fizzy Plum', 'QUENCHERS.', 'Refreshing fizzy plum drink - perfect for hot days!', 'public/images/menu-items/fizzy_plum.jpg', 1, '2026-01-26 16:02:41'),
(39, 'Frosted Punch', 'QUENCHERS.', 'Cool and fruity frosted punch beverage.', 'public/images/menu-items/frosted_punch.jpg', 1, '2026-01-26 16:02:41'),
(40, 'Passion Nibs', 'QUENCHERS.', 'Tropical passion fruit refresher.', 'public/images/menu-items/passion_nibs.jpg', 1, '2026-01-26 16:02:41'),
(41, 'Peach Please', 'TEA BASED.', 'Sweet peach iced tea.', 'public/images/menu-items/peach_please.jpg', 1, '2026-01-26 16:02:41'),
(42, 'Pomegranate Honey Tea', 'TEA BASED.', 'Pomegranate with natural honey.', 'public/images/menu-items/pomegranate_honey_tea.jpg', 1, '2026-01-26 16:02:41'),
(43, 'English Breakfast Tea', 'TEA BASED.', 'Classic robust breakfast tea.', 'public/images/menu-items/english_breakfast_tea.jpg', 1, '2026-01-26 16:02:41'),
(44, 'Green Tea', 'TEA BASED.', 'Fresh and healthy green tea.', 'public/images/menu-items/green_tea.jpg', 1, '2026-01-26 16:02:41'),
(45, 'Pomelo Okinawa', 'TEA BASED.', 'Citrus pomelo with Okinawa brown sugar.', 'public/images/menu-items/pomelo_okinawa.jpg', 1, '2026-01-26 16:02:41'),
(46, 'Pomegranate Hibiscus Tea', 'TEA BASED.', 'Tart pomegranate with floral hibiscus.', 'public/images/menu-items/pomegranate_hibiscus_tea.jpg', 1, '2026-01-26 16:02:41'),
(47, 'Butter Bar Ensaymada', 'ENSAYMADA.', 'Rich buttery ensaymada bar style.', 'public/images/menu-items/butter_bar_ensaymada.jpg', 1, '2026-01-26 16:02:41'),
(48, 'Salted Egg Ensaymada', 'ENSAYMADA.', 'Sweet and savory with salted egg topping.', 'public/images/menu-items/salted_egg_ensaymada.png', 1, '2026-01-26 16:02:41'),
(49, 'Ube Cheese Ensaymada', 'ENSAYMADA.', 'Purple yam flavor with cheese topping.', 'public/images/menu-items/ube_cheese_ensaymada.jpg', 1, '2026-01-26 16:02:41'),
(50, 'Floss Shredded Ensaymada', 'ENSAYMADA.', 'Ensaymada topped with savory pork floss.', 'public/images/menu-items/floss_shredded_ensaymada.jpg', 1, '2026-01-26 16:02:41'),
(51, 'Ube Cheese Puff Loaf', 'LOAF.', 'Fluffy ube and cheese bread loaf.', 'public/images/menu-items/ube_cheese_puff_loaf.jpg', 1, '2026-01-26 16:02:41'),
(52, 'Cranberry Puff Loaf', 'LOAF.', 'Sweet cranberry studded bread loaf.', 'public/images/menu-items/cranberry_puff_loaf.jpg', 1, '2026-01-26 16:02:41'),
(53, 'Cheese Loaf', 'LOAF.', 'Savory cheese-filled bread loaf.', 'public/images/menu-items/cheese_loaf.jpg', 1, '2026-01-26 16:02:41'),
(54, 'Ube Loaf', 'LOAF.', 'Premium ube flavored bread loaf.', 'public/images/menu-items/ube_loaf.jpg', 1, '2026-01-26 16:02:41'),
(55, 'Dark Chocolate Loaf', 'LOAF.', 'Decadent dark chocolate bread loaf.', 'public/images/menu-items/dark_chocolate_loaf.jpg', 1, '2026-01-26 16:02:41'),
(79, 'Plain Classic Pandesal', 'PANDESAL.', 'Traditional Filipino bread roll - baked fresh 24/7.', 'public/images/menu-items/plain_classic_pandesal.png', 1, '2026-01-26 16:06:56'),
(80, 'Pandesal Fest Bundle', 'PANDESAL.', 'Bundle of assorted Pandesal - perfect for sharing!', 'public/images/menu-items/pandesal_fest_bundle.jpg', 1, '2026-01-26 16:06:56'),
(81, 'Ube Cheese Pandesal', 'PANDESAL.', 'Filipino purple yam (ube) with melted cheese.', 'public/images/menu-items/ube_cheese_pandesal.png', 1, '2026-01-26 16:06:56'),
(82, 'Classic Ensaymada', 'ENSAYMADA.', 'Traditional sweet spiral bread with butter and sugar.', 'public/images/menu-items/classic_ensaymada.jpg', 1, '2026-01-26 16:06:56'),
(87, 'Plain Loaf', 'LOAF.', 'Soft classic white bread loaf.', 'public/images/menu-items/plain_loaf.jpg', 1, '2026-01-26 16:06:56'),
(88, 'Chocolate Loaf', 'LOAF.', 'Rich chocolate flavored bread loaf.', 'public/images/menu-items/chocolate_loaf.jpg', 1, '2026-01-26 16:06:56'),
(90, 'Wheat Loaf', 'LOAF.', 'Healthy whole wheat bread loaf.', 'public/images/menu-items/wheat_loaf.jpg', 1, '2026-01-26 16:06:56'),
(97, 'Macchiato', 'COFFEE.', 'Espresso marked with a dollop of foamed milk. *Original creation*', 'public/images/menu-items/macchiato.png', 1, '2026-01-26 16:16:54'),
(98, 'Borgir', 'SANDWICHES.', 'Burgirgir hihi', 'public/images/menu-items/borgir.jpg', 1, '2026-03-12 03:30:35');

-- --------------------------------------------------------

--
-- Table structure for table `menu_item_sizes`
--

CREATE TABLE `menu_item_sizes` (
  `id` int(10) UNSIGNED NOT NULL,
  `menu_item_id` int(10) UNSIGNED NOT NULL,
  `size_label` varchar(20) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `is_default` tinyint(1) NOT NULL DEFAULT 0,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `is_available` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `menu_item_sizes`
--

INSERT INTO `menu_item_sizes` (`id`, `menu_item_id`, `size_label`, `price`, `is_default`, `sort_order`, `is_available`, `created_at`, `updated_at`) VALUES
(3, 2, '12oz', 80.00, 1, 1, 1, '2026-04-07 15:35:47', '2026-04-07 15:35:47'),
(4, 2, '16oz', 90.00, 0, 2, 1, '2026-04-07 15:35:47', '2026-04-07 15:35:47'),
(5, 3, '12oz', 95.00, 1, 1, 1, '2026-04-07 15:35:47', '2026-04-07 15:35:47'),
(6, 3, '16oz', 110.00, 0, 2, 1, '2026-04-07 15:35:47', '2026-04-07 15:35:47'),
(7, 4, '12oz', 95.00, 1, 1, 1, '2026-04-07 15:35:47', '2026-04-07 15:35:47'),
(8, 4, '16oz', 110.00, 0, 2, 1, '2026-04-07 15:35:47', '2026-04-07 15:35:47'),
(9, 5, '12oz', 100.00, 1, 1, 1, '2026-04-07 15:35:47', '2026-04-07 15:35:47'),
(10, 5, '16oz', 110.00, 0, 2, 1, '2026-04-07 15:35:47', '2026-04-07 15:35:47'),
(11, 6, '12oz', 100.00, 1, 1, 1, '2026-04-07 15:35:47', '2026-04-07 15:35:47'),
(12, 6, '16oz', 110.00, 0, 2, 1, '2026-04-07 15:35:47', '2026-04-07 15:35:47'),
(13, 7, '12oz', 120.00, 1, 1, 1, '2026-04-07 15:35:47', '2026-04-07 15:35:47'),
(14, 7, '16oz', 130.00, 0, 2, 1, '2026-04-07 15:35:47', '2026-04-07 15:35:47'),
(15, 8, '12oz', 120.00, 1, 1, 1, '2026-04-07 15:35:47', '2026-04-07 15:35:47'),
(16, 8, '16oz', 130.00, 0, 2, 1, '2026-04-07 15:35:47', '2026-04-07 15:35:47'),
(17, 9, '12oz', 120.00, 1, 1, 1, '2026-04-07 15:35:47', '2026-04-07 15:35:47'),
(18, 9, '16oz', 130.00, 0, 2, 1, '2026-04-07 15:35:47', '2026-04-07 15:35:47'),
(19, 10, '12oz', 140.00, 1, 1, 1, '2026-04-07 15:35:47', '2026-04-07 15:35:47'),
(20, 10, '16oz', 150.00, 0, 2, 1, '2026-04-07 15:35:47', '2026-04-07 15:35:47'),
(21, 11, '12oz', 160.00, 1, 1, 1, '2026-04-07 15:35:47', '2026-04-07 15:35:47'),
(22, 11, '16oz', 180.00, 0, 2, 1, '2026-04-07 15:35:47', '2026-04-07 15:35:47'),
(23, 12, '12oz', 160.00, 1, 1, 1, '2026-04-07 15:35:47', '2026-04-07 15:35:47'),
(24, 12, '16oz', 180.00, 0, 2, 1, '2026-04-07 15:35:47', '2026-04-07 15:35:47'),
(25, 24, '12oz', 160.00, 1, 1, 1, '2026-04-07 15:35:47', '2026-04-07 15:35:47'),
(26, 24, '16oz', 180.00, 0, 2, 1, '2026-04-07 15:35:47', '2026-04-07 15:35:47'),
(27, 25, '12oz', 160.00, 1, 1, 1, '2026-04-07 15:35:47', '2026-04-07 15:35:47'),
(28, 25, '16oz', 180.00, 0, 2, 1, '2026-04-07 15:35:47', '2026-04-07 15:35:47'),
(29, 26, '12oz', 170.00, 1, 1, 1, '2026-04-07 15:35:47', '2026-04-07 15:35:47'),
(30, 26, '16oz', 190.00, 0, 2, 1, '2026-04-07 15:35:47', '2026-04-07 15:35:47'),
(31, 27, '12oz', 170.00, 1, 1, 1, '2026-04-07 15:35:47', '2026-04-07 15:35:47'),
(32, 27, '16oz', 190.00, 0, 2, 1, '2026-04-07 15:35:47', '2026-04-07 15:35:47'),
(33, 28, '12oz', 170.00, 1, 1, 1, '2026-04-07 15:35:47', '2026-04-07 15:35:47'),
(34, 28, '16oz', 190.00, 0, 2, 1, '2026-04-07 15:35:47', '2026-04-07 15:35:47'),
(35, 29, '12oz', 170.00, 1, 1, 1, '2026-04-07 15:35:47', '2026-04-07 15:35:47'),
(36, 29, '16oz', 190.00, 0, 2, 1, '2026-04-07 15:35:47', '2026-04-07 15:35:47'),
(37, 30, '12oz', 170.00, 1, 1, 1, '2026-04-07 15:35:47', '2026-04-07 15:35:47'),
(38, 30, '16oz', 190.00, 0, 2, 1, '2026-04-07 15:35:47', '2026-04-07 15:35:47'),
(39, 31, '12oz', 120.00, 1, 1, 1, '2026-04-07 15:35:47', '2026-04-07 15:35:47'),
(40, 31, '16oz', 130.00, 0, 2, 1, '2026-04-07 15:35:47', '2026-04-07 15:35:47'),
(41, 32, '12oz', 130.00, 1, 1, 1, '2026-04-07 15:35:47', '2026-04-07 15:35:47'),
(42, 32, '16oz', 150.00, 0, 2, 1, '2026-04-07 15:35:47', '2026-04-07 15:35:47'),
(43, 33, '12oz', 140.00, 1, 1, 1, '2026-04-07 15:35:47', '2026-04-07 15:35:47'),
(44, 33, '16oz', 160.00, 0, 2, 1, '2026-04-07 15:35:47', '2026-04-07 15:35:47'),
(45, 34, '12oz', 150.00, 1, 1, 1, '2026-04-07 15:35:47', '2026-04-07 15:35:47'),
(46, 34, '16oz', 160.00, 0, 2, 1, '2026-04-07 15:35:47', '2026-04-07 15:35:47'),
(47, 35, '12oz', 160.00, 1, 1, 1, '2026-04-07 15:35:47', '2026-04-07 15:35:47'),
(48, 35, '16oz', 180.00, 0, 2, 1, '2026-04-07 15:35:47', '2026-04-07 15:35:47'),
(49, 36, '12oz', 160.00, 1, 1, 1, '2026-04-07 15:35:47', '2026-04-07 15:35:47'),
(50, 36, '16oz', 180.00, 0, 2, 1, '2026-04-07 15:35:47', '2026-04-07 15:35:47'),
(51, 37, '12oz', 170.00, 1, 1, 1, '2026-04-07 15:35:47', '2026-04-07 15:35:47'),
(52, 37, '16oz', 190.00, 0, 2, 1, '2026-04-07 15:35:47', '2026-04-07 15:35:47'),
(53, 41, '12oz', 110.00, 1, 1, 1, '2026-04-07 15:35:47', '2026-04-07 15:35:47'),
(54, 41, '16oz', 120.00, 0, 2, 1, '2026-04-07 15:35:47', '2026-04-07 15:35:47'),
(55, 42, '12oz', 110.00, 1, 1, 1, '2026-04-07 15:35:47', '2026-04-07 15:35:47'),
(56, 42, '16oz', 120.00, 0, 2, 1, '2026-04-07 15:35:47', '2026-04-07 15:35:47'),
(57, 43, '12oz', 120.00, 1, 1, 1, '2026-04-07 15:35:47', '2026-04-07 15:35:47'),
(58, 43, '16oz', 130.00, 0, 2, 1, '2026-04-07 15:35:47', '2026-04-07 15:35:47'),
(59, 44, '12oz', 120.00, 1, 1, 1, '2026-04-07 15:35:47', '2026-04-07 15:35:47'),
(60, 44, '16oz', 130.00, 0, 2, 1, '2026-04-07 15:35:47', '2026-04-07 15:35:47'),
(61, 45, '12oz', 160.00, 1, 1, 1, '2026-04-07 15:35:47', '2026-04-07 15:35:47'),
(62, 45, '16oz', 180.00, 0, 2, 1, '2026-04-07 15:35:47', '2026-04-07 15:35:47'),
(63, 46, '12oz', 160.00, 1, 1, 1, '2026-04-07 15:35:47', '2026-04-07 15:35:47'),
(64, 46, '16oz', 180.00, 0, 2, 1, '2026-04-07 15:35:47', '2026-04-07 15:35:47'),
(65, 97, '12oz', 95.00, 1, 1, 1, '2026-04-07 15:35:47', '2026-04-07 15:35:47'),
(66, 97, '16oz', 110.00, 0, 2, 1, '2026-04-07 15:35:47', '2026-04-07 15:35:47'),
(70, 38, 'Default', 110.00, 1, 1, 1, '2026-04-07 15:51:22', '2026-04-07 15:51:22'),
(71, 39, 'Default', 110.00, 1, 1, 1, '2026-04-07 15:51:22', '2026-04-07 15:51:22'),
(72, 40, 'Default', 110.00, 1, 1, 1, '2026-04-07 15:51:22', '2026-04-07 15:51:22'),
(73, 47, 'Default', 45.00, 1, 1, 1, '2026-04-07 15:51:22', '2026-04-07 15:51:22'),
(74, 48, 'Default', 50.00, 1, 1, 1, '2026-04-07 15:51:22', '2026-04-07 15:51:22'),
(75, 49, 'Default', 50.00, 1, 1, 1, '2026-04-07 15:51:22', '2026-04-07 15:51:22'),
(76, 50, 'Default', 50.00, 1, 1, 1, '2026-04-07 15:51:22', '2026-04-07 15:51:22'),
(77, 51, 'Default', 140.00, 1, 1, 1, '2026-04-07 15:51:22', '2026-04-07 15:51:22'),
(78, 52, 'Default', 170.00, 1, 1, 1, '2026-04-07 15:51:22', '2026-04-07 15:51:22'),
(79, 53, 'Default', 200.00, 1, 1, 1, '2026-04-07 15:51:22', '2026-04-07 15:51:22'),
(80, 54, 'Default', 200.00, 1, 1, 1, '2026-04-07 15:51:22', '2026-04-07 15:51:22'),
(81, 55, 'Default', 200.00, 1, 1, 1, '2026-04-07 15:51:22', '2026-04-07 15:51:22'),
(82, 79, 'Default', 35.00, 1, 1, 1, '2026-04-07 15:51:22', '2026-04-07 15:51:22'),
(83, 80, 'Default', 400.00, 1, 1, 1, '2026-04-07 15:51:22', '2026-04-07 15:51:22'),
(84, 81, 'Default', 60.00, 1, 1, 1, '2026-04-07 15:51:22', '2026-04-07 15:51:22'),
(85, 82, 'Default', 45.00, 1, 1, 1, '2026-04-07 15:51:22', '2026-04-07 15:51:22'),
(86, 87, 'Default', 80.00, 1, 1, 1, '2026-04-07 15:51:22', '2026-04-07 15:51:22'),
(87, 88, 'Default', 140.00, 1, 1, 1, '2026-04-07 15:51:22', '2026-04-07 15:51:22'),
(88, 90, 'Default', 150.00, 1, 1, 1, '2026-04-07 15:51:22', '2026-04-07 15:51:22'),
(89, 98, 'Default', 6.90, 1, 1, 1, '2026-04-07 15:51:22', '2026-04-07 15:51:22');

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `entity_type` varchar(50) NOT NULL,
  `entity_id` int(10) UNSIGNED DEFAULT NULL,
  `title` varchar(150) NOT NULL,
  `message` varchar(500) NOT NULL,
  `read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED DEFAULT NULL,
  `reservation_id` int(10) UNSIGNED DEFAULT NULL,
  `order_number` varchar(50) DEFAULT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `status` enum('pending','preparing','ready','completed','cancelled','rejected') NOT NULL DEFAULT 'pending',
  `payment_status` enum('pending','verified','rejected') NOT NULL DEFAULT 'pending',
  `receipt_image` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `reservation_id`, `order_number`, `total_amount`, `status`, `payment_status`, `receipt_image`, `created_at`) VALUES
(8, 5, NULL, 'GR-20260411012150-67A0', 120.00, 'completed', 'verified', 'receipt_1775841710_ba07e921.png', '2026-04-10 17:21:50'),
(191, 6, NULL, 'GR-20260412010742-0145', 340.00, 'completed', 'verified', 'receipt_1775927262_8c5c7ffc.png', '2026-04-11 17:07:42'),
(192, 6, 122, 'GR-20260412012358-6124', 310.00, 'completed', 'verified', 'receipt_1775928238_74abe6c9.png', '2026-04-11 17:23:58'),
(205, 159, NULL, 'QA-PENDING-PAYMENT', 265.00, 'pending', 'pending', 'uploads/receipts/qa-receipt.png', '2026-04-14 01:30:36'),
(206, 159, NULL, 'QA-PREPARING', 245.00, 'preparing', 'verified', NULL, '2026-04-13 23:00:36'),
(207, 159, NULL, 'QA-COMPLETED', 95.00, 'completed', 'verified', NULL, '2026-04-09 02:00:36'),
(208, 159, 133, 'QA-RESERVATION-LINKED', 225.00, 'pending', 'verified', NULL, '2026-04-13 22:00:36');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` int(10) UNSIGNED NOT NULL,
  `order_id` int(10) UNSIGNED NOT NULL,
  `menu_item_id` int(10) UNSIGNED NOT NULL,
  `quantity` int(10) UNSIGNED NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `subtotal` decimal(10,2) NOT NULL,
  `size` varchar(20) DEFAULT 'Default'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `menu_item_id`, `quantity`, `price`, `subtotal`, `size`) VALUES
(8, 8, 9, 1, 120.00, 120.00, '12oz'),
(192, 191, 35, 1, 160.00, 160.00, '12oz'),
(193, 191, 36, 1, 180.00, 180.00, '16oz'),
(194, 192, 9, 1, 120.00, 120.00, '12oz'),
(195, 192, 27, 1, 190.00, 190.00, '16oz');

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `email` varchar(100) NOT NULL,
  `token_hash` varchar(64) NOT NULL,
  `expires_at` datetime NOT NULL,
  `used_at` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `reservations`
--

CREATE TABLE `reservations` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED DEFAULT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `date` date NOT NULL,
  `time` time NOT NULL,
  `guests` int(10) UNSIGNED NOT NULL,
  `status` enum('pending','confirmed','completed','cancelled') NOT NULL DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `reservations`
--

INSERT INTO `reservations` (`id`, `user_id`, `first_name`, `last_name`, `email`, `phone`, `date`, `time`, `guests`, `status`, `created_at`) VALUES
(122, 6, 'Princezz Mae', 'Santayana', 'princezzmaesantayana2@gmail.com', '09192570961', '2026-04-16', '13:00:00', 2, 'completed', '2026-04-11 17:23:42'),
(132, 159, 'QA', 'Customer', 'qa.customer@grande.local', '09170000001', '2026-04-16', '15:00:00', 4, 'pending', '2026-04-14 00:00:36'),
(133, 159, 'QA', 'Customer', 'qa.customer@grande.local', '09170000001', '2026-04-19', '10:30:00', 2, 'confirmed', '2026-04-13 02:00:36'),
(134, 159, 'QA', 'Customer', 'qa.customer@grande.local', '09170000001', '2026-04-11', '14:00:00', 3, 'completed', '2026-04-08 02:00:36');

-- --------------------------------------------------------

--
-- Table structure for table `schema_migrations`
--

CREATE TABLE `schema_migrations` (
  `migration` varchar(255) NOT NULL,
  `applied_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `schema_migrations`
--

INSERT INTO `schema_migrations` (`migration`, `applied_at`) VALUES
('2026_04_10_000001_create_users.sql', '2026-04-11 07:14:06'),
('2026_04_10_000002_create_signup_verifications.sql', '2026-04-11 07:14:06'),
('2026_04_10_000003_create_menu_items.sql', '2026-04-11 07:14:06'),
('2026_04_10_000004_create_menu_item_sizes.sql', '2026-04-11 07:14:06'),
('2026_04_11_000005_create_cart_items.sql', '2026-04-11 07:14:06'),
('2026_04_11_000006_create_reservations.sql', '2026-04-11 07:14:06'),
('2026_04_11_000007_create_orders.sql', '2026-04-11 07:14:06'),
('2026_04_11_000008_create_order_items.sql', '2026-04-11 07:14:06'),
('2026_04_11_000009_create_feedback.sql', '2026-04-11 07:14:06'),
('2026_04_11_000010_create_audit_logs.sql', '2026-04-11 07:14:06'),
('2026_04_11_000011_create_password_resets.sql', '2026-04-11 07:14:06'),
('2026_04_11_000012_create_notifications.sql', '2026-04-11 10:16:02');

-- --------------------------------------------------------

--
-- Table structure for table `signup_verifications`
--

CREATE TABLE `signup_verifications` (
  `id` int(10) UNSIGNED NOT NULL,
  `email` varchar(100) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `password` varchar(255) NOT NULL,
  `verification_code` varchar(10) NOT NULL,
  `expires_at` datetime NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(10) UNSIGNED NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('customer','admin','employee') NOT NULL DEFAULT 'customer',
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `profile_picture` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `first_name`, `last_name`, `email`, `phone`, `password`, `role`, `is_active`, `profile_picture`, `created_at`, `updated_at`) VALUES
(5, 'Christian', 'Britanico', 'britanix23@gmail.com', '09946834809', '$2y$10$5QsUNqD6ZNhFblxjj.aOk.lrus/cXQa0ZtxdTiTFooqvT5GSMvbvq', 'admin', 1, NULL, '2026-04-10 15:18:17', '2026-04-11 17:16:35'),
(6, 'Princezz Mae', 'Santayana', 'princezzmaesantayana2@gmail.com', '09192570961', '$2y$10$5GgGcb99ZjmUpH4eVr4K7OUO5fJDZ/xppAaj5qJ3/sE2kjLsGrJT.', 'customer', 1, NULL, '2026-04-10 15:35:40', '2026-04-11 00:56:13'),
(158, 'Dev', 'Britss', 'dev.britss@gmail.com', '09998751258', '$2y$10$PuVcaNCLdhduvuFT0ALGrOcOv.noxTWDhZ1mqXxECNdQg30g4y0IS', 'employee', 1, NULL, '2026-04-11 17:10:10', '2026-04-11 17:11:23'),
(159, 'QA', 'Customer', 'qa.customer@grande.local', '09170000001', '$2y$10$U2EI3p9NFTIxewBxkhqQTuY6/c56uMbSDZ6I4T5X2mvTrFPvRTtVO', 'customer', 1, NULL, '2026-04-14 00:56:09', '2026-04-14 02:00:36'),
(160, 'QA', 'Employee', 'qa.employee@grande.local', '09170000002', '$2y$10$U2EI3p9NFTIxewBxkhqQTuY6/c56uMbSDZ6I4T5X2mvTrFPvRTtVO', 'employee', 1, NULL, '2026-04-14 00:56:09', '2026-04-14 02:00:36'),
(161, 'QA', 'Admin', 'qa.admin@grande.local', '09170000003', '$2y$10$U2EI3p9NFTIxewBxkhqQTuY6/c56uMbSDZ6I4T5X2mvTrFPvRTtVO', 'admin', 1, NULL, '2026-04-14 00:56:09', '2026-04-14 02:00:36');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `audit_logs_actor_index` (`actor_user_id`),
  ADD KEY `audit_logs_entity_index` (`entity_type`,`entity_id`),
  ADD KEY `audit_logs_action_index` (`action`);

--
-- Indexes for table `cart_items`
--
ALTER TABLE `cart_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cart_items_user_index` (`user_id`),
  ADD KEY `cart_items_menu_item_index` (`menu_item_id`);

--
-- Indexes for table `customer_order_notifications`
--
ALTER TABLE `customer_order_notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `customer_order_notifications_user_read_index` (`user_id`,`is_read`,`created_at`),
  ADD KEY `customer_order_notifications_order_index` (`order_id`);

--
-- Indexes for table `customer_reservation_notifications`
--
ALTER TABLE `customer_reservation_notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `customer_reservation_notifications_user_read_index` (`user_id`,`is_read`,`created_at`),
  ADD KEY `customer_reservation_notifications_reservation_index` (`reservation_id`);

--
-- Indexes for table `feedback`
--
ALTER TABLE `feedback`
  ADD PRIMARY KEY (`id`),
  ADD KEY `feedback_user_index` (`user_id`),
  ADD KEY `feedback_status_index` (`status`),
  ADD KEY `feedback_category_index` (`category`);

--
-- Indexes for table `menu_items`
--
ALTER TABLE `menu_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `menu_items_category_index` (`category`),
  ADD KEY `menu_items_available_index` (`is_available`);

--
-- Indexes for table `menu_item_sizes`
--
ALTER TABLE `menu_item_sizes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `menu_item_sizes_menu_item_index` (`menu_item_id`),
  ADD KEY `menu_item_sizes_available_index` (`is_available`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `notifications_user_read_index` (`user_id`,`read_at`,`created_at`),
  ADD KEY `notifications_entity_index` (`entity_type`,`entity_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `orders_order_number_unique` (`order_number`),
  ADD KEY `orders_user_index` (`user_id`),
  ADD KEY `orders_reservation_index` (`reservation_id`),
  ADD KEY `orders_status_index` (`status`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_items_order_index` (`order_id`),
  ADD KEY `order_items_menu_item_index` (`menu_item_id`);

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `password_resets_token_hash_unique` (`token_hash`),
  ADD KEY `password_resets_email_index` (`email`),
  ADD KEY `password_resets_user_id_index` (`user_id`);

--
-- Indexes for table `reservations`
--
ALTER TABLE `reservations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `reservations_user_index` (`user_id`),
  ADD KEY `reservations_status_index` (`status`);

--
-- Indexes for table `schema_migrations`
--
ALTER TABLE `schema_migrations`
  ADD PRIMARY KEY (`migration`);

--
-- Indexes for table `signup_verifications`
--
ALTER TABLE `signup_verifications`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `signup_verifications_email_unique` (`email`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`),
  ADD UNIQUE KEY `users_phone_unique` (`phone`),
  ADD KEY `users_role_index` (`role`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `audit_logs`
--
ALTER TABLE `audit_logs`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT for table `cart_items`
--
ALTER TABLE `cart_items`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=59;

--
-- AUTO_INCREMENT for table `customer_order_notifications`
--
ALTER TABLE `customer_order_notifications`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `customer_reservation_notifications`
--
ALTER TABLE `customer_reservation_notifications`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `feedback`
--
ALTER TABLE `feedback`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `menu_items`
--
ALTER TABLE `menu_items`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=126;

--
-- AUTO_INCREMENT for table `menu_item_sizes`
--
ALTER TABLE `menu_item_sizes`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=104;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=209;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=220;

--
-- AUTO_INCREMENT for table `password_resets`
--
ALTER TABLE `password_resets`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `reservations`
--
ALTER TABLE `reservations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=135;

--
-- AUTO_INCREMENT for table `signup_verifications`
--
ALTER TABLE `signup_verifications`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=186;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD CONSTRAINT `audit_logs_actor_fk` FOREIGN KEY (`actor_user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `cart_items`
--
ALTER TABLE `cart_items`
  ADD CONSTRAINT `cart_items_menu_item_fk` FOREIGN KEY (`menu_item_id`) REFERENCES `menu_items` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cart_items_user_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `customer_order_notifications`
--
ALTER TABLE `customer_order_notifications`
  ADD CONSTRAINT `customer_order_notifications_order_fk` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `customer_order_notifications_user_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `customer_reservation_notifications`
--
ALTER TABLE `customer_reservation_notifications`
  ADD CONSTRAINT `customer_reservation_notifications_reservation_fk` FOREIGN KEY (`reservation_id`) REFERENCES `reservations` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `customer_reservation_notifications_user_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `feedback`
--
ALTER TABLE `feedback`
  ADD CONSTRAINT `feedback_user_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `menu_item_sizes`
--
ALTER TABLE `menu_item_sizes`
  ADD CONSTRAINT `menu_item_sizes_menu_item_fk` FOREIGN KEY (`menu_item_id`) REFERENCES `menu_items` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_user_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_reservation_fk` FOREIGN KEY (`reservation_id`) REFERENCES `reservations` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `orders_user_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_menu_item_fk` FOREIGN KEY (`menu_item_id`) REFERENCES `menu_items` (`id`),
  ADD CONSTRAINT `order_items_order_fk` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD CONSTRAINT `password_resets_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `reservations`
--
ALTER TABLE `reservations`
  ADD CONSTRAINT `reservations_user_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
