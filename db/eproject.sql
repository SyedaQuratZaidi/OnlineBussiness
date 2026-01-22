-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 02, 2026 at 07:58 AM
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
-- Database: `eproject`
--

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(10) UNSIGNED NOT NULL,
  `customer_name` varchar(255) NOT NULL,
  `customer_email` varchar(255) NOT NULL,
  `customer_phone` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `order_data` text NOT NULL,
  `total` decimal(10,2) NOT NULL DEFAULT 0.00,
  `status` enum('pending','processing','shipped','delivered','cancelled') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `category` varchar(64) NOT NULL,
  `subcategory` varchar(64) NOT NULL,
  `price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `image` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `name`, `category`, `subcategory`, `price`, `image`, `description`, `created_at`) VALUES
(1, 'HAVOC Body Spray', 'fragrance', 'body-sprays', 500.00, 'assets/images/HAVOC Body Spray.webp', 'Long-lasting fragrance body spray', '2025-12-22 03:04:43'),
(2, 'Miss Rose Lovely Pink', 'cosmetics', 'lips', 200.00, 'assets/images/Miss Rose Lovely Pink.jpg', 'Beautiful pink lipstick', '2025-12-22 03:04:43'),
(3, 'Eye and Lip pencil Hudabeauty', 'cosmetics', 'eyes', 100.00, 'assets/images/Eye and Lip pencil Hudabeauty.webp', 'Professional eye and lip pencil', '2025-12-22 03:04:43'),
(4, 'Square Stone Ear Rings', 'jewelry', 'earrings', 300.00, 'assets/images/Antique Golden Jhumka.jpg', 'Elegant square stone earrings', '2025-12-22 03:04:43'),
(5, 'Metallic Bangle Set', 'jewelry', 'bangles', 350.00, 'assets/images/Metallic Bangle Set.jpg', 'Beautiful metallic bangle set', '2025-12-22 03:04:43'),
(6, 'Romance Body Spray', 'fragrance', 'body-sprays', 450.00, 'assets/images/HAVOC Body Spray.webp', 'Romantic fragrance body spray', '2025-12-22 03:04:43'),
(7, 'Antique Golden Jhumka', 'jewelry', 'earrings', 400.00, 'assets/images/Antique Golden Jhumka.jpg', 'Traditional golden jhumka earrings', '2025-12-22 03:04:43'),
(8, 'Mutual Love Perfume 50ml', 'fragrance', 'perfumes', 450.00, 'assets/images/images.jpg', 'Premium perfume 50ml', '2025-12-22 03:04:43'),
(9, 'Karite Make up Fixer', 'cosmetics', 'complexion', 350.00, 'assets/images/cosmetic.webp', 'Long-lasting makeup fixer', '2025-12-22 03:04:43'),
(10, 'Emotions – Rasai', 'fragrance', 'body-sprays', 400.00, 'assets/images/images.jpg', 'Emotional fragrance body spray', '2025-12-22 03:04:43'),
(11, 'Gold Plated Necklace', 'jewelry', 'necklace', 600.00, 'assets/images/Gold Plated Necklace.jpg', 'Elegant gold plated necklace', '2025-12-22 03:04:43'),
(12, 'Diamond Ring', 'jewelry', 'rings', 800.00, 'assets/images/Diamond Ring.webp', 'Beautiful diamond ring', '2025-12-22 03:04:43'),
(13, 'Foundation Cream', 'cosmetics', 'complexion', 550.00, 'assets/images/Foundation Makeup Kit.jpg', 'Natural finish foundation', '2025-12-22 03:04:43'),
(14, 'Nail Polish Set', 'cosmetics', 'nails', 250.00, 'assets/images/cosmetic.webp', 'Set of 6 nail polish colors', '2025-12-22 03:04:43'),
(15, 'Silver Anklet', 'jewelry', 'anklets', 300.00, 'assets/images/Silver Anklet.jpg', 'Elegant silver anklet', '2025-12-22 03:04:43'),
(16, 'Foundation Makeup Kit', 'cosmetics', 'complexion', 650.00, 'assets/images/Foundation Makeup Kit.jpg', 'Complete foundation kit for flawless skin', '2025-12-22 03:04:43'),
(17, 'BB Cream', 'cosmetics', 'complexion', 450.00, 'assets/images/BB-cream.jpg', 'Lightweight BB cream with natural coverage', '2025-12-22 03:04:43'),
(18, 'Concealer Stick', 'cosmetics', 'complexion', 2000.00, 'assets/images/product_6956777211b2e.png', 'High-coverage concealer for dark circles', '2025-12-22 03:04:43');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_created_at` (`created_at`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_category` (`category`),
  ADD KEY `idx_subcategory` (`subcategory`),
  ADD KEY `idx_name` (`name`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;
 
--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);
ALTER TABLE `categories`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
