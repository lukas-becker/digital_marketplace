SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `marketplace_db`
--
CREATE DATABASE IF NOT EXISTS `marketplace_db` DEFAULT CHARACTER SET utf16 COLLATE utf16_bin;
USE `marketplace_db`;

-- --------------------------------------------------------

--
-- Structure for table `Address`
--

CREATE TABLE `Address` (
  `id` int(255) NOT NULL,
  `str_street` varchar(255) COLLATE utf16_bin NOT NULL,
  `str_number` varchar(255) COLLATE utf16_bin NOT NULL,
  `str_zip` varchar(255) COLLATE utf16_bin NOT NULL,
  `str_city` varchar(255) COLLATE utf16_bin NOT NULL,
  `fk_user` int(11) NOT NULL,
  `bool_primary` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf16 COLLATE=utf16_bin;

--
-- Data for table `Address`
--

INSERT INTO `Address` (`id`, `str_street`, `str_number`, `str_zip`, `str_city`, `fk_user`, `bool_primary`) VALUES
(23, 'Modstreet', '1', '12345', 'Modcity', 15, 1),
(25, 'Adminstreet', '1', '12345', 'Admincity', 17, 1),
(26, 'Competitorstreet', '1', '12345', 'Comptown', 18, 1),
(27, 'Userstreet', '1', '12345', 'Usertown', 14, 0);

-- --------------------------------------------------------

--
-- Structure for table `Article`
--

CREATE TABLE `Article` (
  `id` int(11) NOT NULL,
  `str_title` varchar(255) COLLATE utf16_bin NOT NULL,
  `str_description` text COLLATE utf16_bin NOT NULL,
  `str_location` varchar(255) COLLATE utf16_bin NOT NULL,
  `int_days_until_shipping` int(11) NOT NULL,
  `int_current_available` int(11) NOT NULL,
  `float_current_price` float DEFAULT NULL,
  `fk_organization` int(11) NOT NULL,
  `float_shipping_cost` float NOT NULL,
  `bool_auction` tinyint(1) NOT NULL DEFAULT 0,
  `date_auction_end` datetime DEFAULT NULL,
  `bool_visible` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf16 COLLATE=utf16_bin;

--
-- Data for table `Article`
--

INSERT INTO `Article` (`id`, `str_title`, `str_description`, `str_location`, `int_days_until_shipping`, `int_current_available`, `float_current_price`, `fk_organization`, `float_shipping_cost`, `bool_auction`, `date_auction_end`, `bool_visible`) VALUES
(18, 'MacBook Pro', 'from 2021', 'Munich', 3, 33, 1399.99, 1, 0, 0, NULL, 1),
(24, 'Natural face mask', 'Make <i>your </i>face glow.', 'Munich, Germany', 3, 999, 0.39, 1, 0, 0, NULL, 1),
(27, 'Essential Oils', 'Our great essential oils smell great and benefit <b>your </b>health.', 'Biblis', 7, 4, 9.99, 13, 0, 0, NULL, 1),
(28, 'Cameralens', 'Lens for your DSLR camera.', 'Berlin', 3, 20, 479.89, 13, 0, 0, NULL, 1),
(29, 'Parfume', 'Smells like roses mixed with lavender', 'Biblis', 2, 99, 72.99, 13, 0, 0, NULL, 1),
(30, 'Natural Soap', 'Our new all natural soap', 'Munich', 4, 73, 4.99, 1, 0, 0, NULL, 1),
(31, 'Crystal', 'The crystal was harvested in south mexico.', 'Munich', 4, 3, 17.99, 1, 0, 0, NULL, 1),
(32, 'Extra Virgin Olive Oil', 'Ideally combined with <a href=\"https://letmegooglethat.com/?q=Cooking+supplies\">our </a>other cooking supplies.', 'Munich', 4, 60, 9.99, 1, 0, 0, NULL, 1),
(33, 'Giraffe Painting', 'For more information contact us.', 'Munich', 3, 1, NULL, 1, 0, 1, '2021-08-01 20:00:00', 1);

-- --------------------------------------------------------

--
-- Structure for table `Article_Category`
--

CREATE TABLE `Article_Category` (
  `id` int(11) NOT NULL,
  `fk_article` int(11) NOT NULL,
  `fk_category` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf16 COLLATE=utf16_bin;

--
-- Data for table `Article_Category`
--

INSERT INTO `Article_Category` (`id`, `fk_article`, `fk_category`) VALUES
(70, 28, 5),
(71, 29, 6),
(76, 30, 6),
(77, 27, 6),
(78, 27, 7),
(79, 31, 7),
(80, 33, 7),
(84, 24, 6),
(85, 18, 3),
(86, 18, 4),
(87, 18, 5);

-- --------------------------------------------------------

--
-- Structure for table `Article_Highlight`
--

CREATE TABLE `Article_Highlight` (
  `id` int(11) NOT NULL,
  `fk_article` int(11) NOT NULL,
  `str_highlight` varchar(250) COLLATE utf16_bin NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf16 COLLATE=utf16_bin;

--
-- Data for table `Article_Highlight`
--

INSERT INTO `Article_Highlight` (`id`, `fk_article`, `str_highlight`) VALUES
(95, 28, 'Fast Focus'),
(96, 29, 'Smells great'),
(98, 30, 'Cleans without chemicals'),
(100, 27, 'Cures Cancer (Maby)'),
(101, 31, 'Looks beatiful'),
(102, 32, 'From italian olives'),
(103, 32, 'refined in switzerland'),
(104, 33, 'Drawn by a talented artist'),
(106, 24, 'Easy to use'),
(107, 18, 'Genuine Apple');

-- --------------------------------------------------------

--
-- Structure for table `Article_Images`
--

CREATE TABLE `Article_Images` (
  `id` int(11) NOT NULL,
  `fk_article` int(11) NOT NULL,
  `str_image` longtext COLLATE utf16_bin NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf16 COLLATE=utf16_bin;

--
-- Structure for table `Article_Property`
--

CREATE TABLE `Article_Property` (
  `id` int(11) NOT NULL,
  `fk_article` int(11) NOT NULL,
  `fk_property` int(11) NOT NULL,
  `str_value` varchar(255) COLLATE utf16_bin NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf16 COLLATE=utf16_bin;

--
-- Structure for table `Article_Review`
--

CREATE TABLE `Article_Review` (
  `id` int(11) NOT NULL,
  `str_title` varchar(255) COLLATE utf16_bin NOT NULL,
  `str_text` varchar(255) COLLATE utf16_bin NOT NULL,
  `float_rating` float NOT NULL,
  `fk_article` int(11) NOT NULL,
  `fk_user` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf16 COLLATE=utf16_bin;

--
-- Structure for table `Bids`
--

CREATE TABLE `Bids` (
  `id` int(11) NOT NULL,
  `fk_article` int(11) NOT NULL,
  `fk_user` int(11) NOT NULL,
  `float_amount` float NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf16 COLLATE=utf16_bin;

--
-- Structure for table `Category`
--

CREATE TABLE `Category` (
  `id` int(11) NOT NULL,
  `str_name` varchar(255) COLLATE utf16_bin NOT NULL,
  `str_description` text COLLATE utf16_bin NOT NULL,
  `str_image` varchar(255) COLLATE utf16_bin NOT NULL,
  `fk_parent` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf16 COLLATE=utf16_bin;

--
-- Structure for table `Category_Property`
--

CREATE TABLE `Category_Property` (
  `id` int(11) NOT NULL,
  `fk_category` int(11) NOT NULL,
  `fk_property` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf16 COLLATE=utf16_bin;

--
-- Structure for table `Guide`
--

CREATE TABLE `Guide` (
  `id` int(11) NOT NULL,
  `fk_category` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf16 COLLATE=utf16_bin;

--
-- Structure for table `Guide_Questions`
--

CREATE TABLE `Guide_Questions` (
  `fk_question` int(11) DEFAULT NULL,
  `fk_guide` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf16 COLLATE=utf16_bin;

--
-- Structure for table `Order`
--

CREATE TABLE `Order` (
  `id` int(11) NOT NULL,
  `date_order_date` datetime NOT NULL,
  `float_applied_rebate` float DEFAULT 0,
  `bool_paid` tinyint(1) DEFAULT 0,
  `fk_user` int(11) NOT NULL,
  `fk_state` int(11) NOT NULL,
  `date_shipping_date` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf16 COLLATE=utf16_bin;

--
-- Structure for table `Order_Article`
--

CREATE TABLE `Order_Article` (
  `id` int(11) NOT NULL,
  `fk_order` int(11) DEFAULT NULL,
  `fk_article` int(11) DEFAULT NULL,
  `float_price` float DEFAULT NULL,
  `int_amount` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf16 COLLATE=utf16_bin;

--
-- Structure for table `Order_State`
--

CREATE TABLE `Order_State` (
  `id` int(11) NOT NULL,
  `str_state` varchar(55) COLLATE utf16_bin NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf16 COLLATE=utf16_bin;

--
-- Data for table `Order_State`
--

INSERT INTO `Order_State` (`id`, `str_state`) VALUES
(3, 'Completed'),
(1, 'Placed'),
(2, 'Shipped');

-- --------------------------------------------------------

--
-- Structure for table `Organization`
--

CREATE TABLE `Organization` (
  `id` int(11) NOT NULL,
  `str_name` varchar(255) COLLATE utf16_bin NOT NULL,
  `str_description` text COLLATE utf16_bin DEFAULT NULL,
  `str_organization_picture` longtext COLLATE utf16_bin DEFAULT NULL,
  `str_street` varchar(55) COLLATE utf16_bin NOT NULL,
  `str_nr` varchar(3) COLLATE utf16_bin NOT NULL,
  `str_zip` varchar(7) COLLATE utf16_bin NOT NULL,
  `str_city` varchar(255) COLLATE utf16_bin NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf16 COLLATE=utf16_bin;

--
-- Data for table `Organization`
--

INSERT INTO `Organization` (`id`, `str_name`, `str_description`, `str_organization_picture`, `str_street`, `str_nr`, `str_zip`, `str_city`) VALUES
(1, 'Marketplace', 'The owner of this marketplace.<br>Also we rule this place.', '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', 'Strasse', '1', '12345 ', 'Stadt');

-- --------------------------------------------------------

--
-- Structure for table `Property`
--

CREATE TABLE `Property` (
  `id` int(11) NOT NULL,
  `str_name` varchar(2555) COLLATE utf16_bin NOT NULL,
  `str_type` varchar(255) COLLATE utf16_bin NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf16 COLLATE=utf16_bin;


--
-- Structure for table `Question`
--

CREATE TABLE `Question` (
  `id` int(11) NOT NULL,
  `str_question` text COLLATE utf16_bin DEFAULT NULL,
  `str_description` text COLLATE utf16_bin DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf16 COLLATE=utf16_bin;


--
-- Structure for table `Question_Answers`
--

CREATE TABLE `Question_Answers` (
  `id` int(11) NOT NULL,
  `fk_question` int(11) NOT NULL,
  `str_answer` text COLLATE utf16_bin NOT NULL,
  `str_filter_properties` text COLLATE utf16_bin NOT NULL,
  `str_filter_values` text COLLATE utf16_bin DEFAULT NULL,
  `str_description` text COLLATE utf16_bin DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf16 COLLATE=utf16_bin;

--
-- Structure for table `Session`
--

CREATE TABLE `Session` (
  `id` int(11) NOT NULL,
  `fk_user` int(11) NOT NULL COMMENT 'reference to the customer',
  `str_browser_fingerprint` varchar(65) COLLATE utf16_bin NOT NULL,
  `str_security_code` varchar(32) COLLATE utf16_bin NOT NULL,
  `date_valid_until` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf16 COLLATE=utf16_bin;

--
-- Structure for table `Shopping_Basket`
--

CREATE TABLE `Shopping_Basket` (
  `id` int(11) NOT NULL,
  `fk_user` int(11) NOT NULL,
  `fk_article` int(11) NOT NULL,
  `int_amount` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf16 COLLATE=utf16_bin;

-- --------------------------------------------------------

--
-- Structure for table `Site_Admin`
--

CREATE TABLE `Site_Admin` (
  `id` int(11) NOT NULL,
  `fk_user` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf16 COLLATE=utf16_bin;

--
-- Structure for table `User`
--

CREATE TABLE `User` (
  `id` int(255) NOT NULL,
  `str_e_mail` varchar(255) COLLATE utf16_bin NOT NULL,
  `str_password_hash` varchar(255) COLLATE utf16_bin NOT NULL,
  `str_first_name` varchar(255) COLLATE utf16_bin NOT NULL,
  `str_last_name` varchar(255) COLLATE utf16_bin NOT NULL,
  `str_public_name` varchar(255) COLLATE utf16_bin DEFAULT NULL,
  `str_profile_picture` longtext COLLATE utf16_bin DEFAULT NULL,
  `bool_active` tinyint(1) NOT NULL DEFAULT 0,
  `fk_organization` int(255) DEFAULT NULL,
  `bool_shadow_ban` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf16 COLLATE=utf16_bin;

--
-- Indexes for table `Address`
--
ALTER TABLE `Address`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_Address_User` (`fk_user`);

--
-- Indexes for table `Article`
--
ALTER TABLE `Article`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_Article_Organisation` (`fk_organization`);

--
-- Indexes for table `Article_Category`
--
ALTER TABLE `Article_Category`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_article_category_article` (`fk_article`),
  ADD KEY `fk_article_category_category` (`fk_category`);

--
-- Indexes for table `Article_Highlight`
--
ALTER TABLE `Article_Highlight`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_article_highlight_article` (`fk_article`);

--
-- Indexes for table `Article_Images`
--
ALTER TABLE `Article_Images`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_article_image` (`fk_article`);

--
-- Indexes for table `Article_Property`
--
ALTER TABLE `Article_Property`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_article_property_article` (`fk_article`),
  ADD KEY `fk_article_property_property` (`fk_property`);

--
-- Indexes for table `Article_Review`
--
ALTER TABLE `Article_Review`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_article_review_article` (`fk_article`),
  ADD KEY `fk_article_review_user` (`fk_user`);

--
-- Indexes for table `Bids`
--
ALTER TABLE `Bids`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `Bids_id_uindex` (`id`),
  ADD KEY `fk_bids_article` (`fk_article`),
  ADD KEY `fk_bids_user` (`fk_user`);

--
-- Indexes for table `Category`
--
ALTER TABLE `Category`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `Category_Property`
--
ALTER TABLE `Category_Property`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_category_property_category` (`fk_category`),
  ADD KEY `fk_category_property_property` (`fk_property`);

--
-- Indexes for table `Guide`
--
ALTER TABLE `Guide`
  ADD PRIMARY KEY (`id`),
  ADD KEY `Guide_Category_id_fk` (`fk_category`);

--
-- Indexes for table `Guide_Questions`
--
ALTER TABLE `Guide_Questions`
  ADD UNIQUE KEY `Guide_Questions_pk` (`fk_question`,`fk_guide`),
  ADD KEY `fk_Guide_Questions_Guide` (`fk_guide`);

--
-- Indexes for table `Order`
--
ALTER TABLE `Order`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_order_user` (`fk_user`),
  ADD KEY `fk_order_state` (`fk_state`);

--
-- Indexes for table `Order_Article`
--
ALTER TABLE `Order_Article`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_Order_Article_Article` (`fk_article`),
  ADD KEY `fk_Order_Article_Order` (`fk_order`);

--
-- Indexes for table `Order_State`
--
ALTER TABLE `Order_State`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `Order_State_str_state_uindex` (`str_state`);

--
-- Indexes for table `Organization`
--
ALTER TABLE `Organization`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `Property`
--
ALTER TABLE `Property`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `Question`
--
ALTER TABLE `Question`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `Question_Answers_id_uindex` (`id`);

--
-- Indexes for table `Question_Answers`
--
ALTER TABLE `Question_Answers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `Question_Answers_Question_id_fk` (`fk_question`);

--
-- Indexes for table `Session`
--
ALTER TABLE `Session`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_customer` (`fk_user`);

--
-- Indexes for table `Shopping_Basket`
--
ALTER TABLE `Shopping_Basket`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_basket_user` (`fk_user`),
  ADD KEY `fk_basket_article` (`fk_article`);

--
-- Indexes for table `Site_Admin`
--
ALTER TABLE `Site_Admin`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_user_admin` (`fk_user`);

--
-- Indexes for table `User`
--
ALTER TABLE `User`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_User_Organization` (`fk_organization`);

--
-- AUTO_INCREMENT for table `Address`
--
ALTER TABLE `Address`
  MODIFY `id` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `Article`
--
ALTER TABLE `Article`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT for table `Article_Category`
--
ALTER TABLE `Article_Category`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=88;

--
-- AUTO_INCREMENT for table `Article_Highlight`
--
ALTER TABLE `Article_Highlight`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=108;

--
-- AUTO_INCREMENT for table `Article_Images`
--
ALTER TABLE `Article_Images`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=179;

--
-- AUTO_INCREMENT for table `Article_Property`
--
ALTER TABLE `Article_Property`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=62;

--
-- AUTO_INCREMENT for table `Article_Review`
--
ALTER TABLE `Article_Review`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `Bids`
--
ALTER TABLE `Bids`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `Category`
--
ALTER TABLE `Category`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `Category_Property`
--
ALTER TABLE `Category_Property`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `Guide`
--
ALTER TABLE `Guide`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `Order`
--
ALTER TABLE `Order`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=190;

--
-- AUTO_INCREMENT for table `Order_Article`
--
ALTER TABLE `Order_Article`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=82;

--
-- AUTO_INCREMENT for table `Order_State`
--
ALTER TABLE `Order_State`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `Organization`
--
ALTER TABLE `Organization`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `Property`
--
ALTER TABLE `Property`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `Question`
--
ALTER TABLE `Question`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `Question_Answers`
--
ALTER TABLE `Question_Answers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `Session`
--
ALTER TABLE `Session`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=152;

--
-- AUTO_INCREMENT for table `Shopping_Basket`
--
ALTER TABLE `Shopping_Basket`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=142;

--
-- AUTO_INCREMENT for table `Site_Admin`
--
ALTER TABLE `Site_Admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `User`
--
ALTER TABLE `User`
  MODIFY `id` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- Constraints for table `Address`
--
ALTER TABLE `Address`
  ADD CONSTRAINT `fk_Address_User` FOREIGN KEY (`fk_user`) REFERENCES `User` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `Article`
--
ALTER TABLE `Article`
  ADD CONSTRAINT `fk_Article_Organisation` FOREIGN KEY (`fk_organization`) REFERENCES `Organization` (`id`);

--
-- Constraints for table `Article_Category`
--
ALTER TABLE `Article_Category`
  ADD CONSTRAINT `fk_article_category_article` FOREIGN KEY (`fk_article`) REFERENCES `Article` (`id`),
  ADD CONSTRAINT `fk_article_category_category` FOREIGN KEY (`fk_category`) REFERENCES `Category` (`id`);

--
-- Constraints for table `Article_Highlight`
--
ALTER TABLE `Article_Highlight`
  ADD CONSTRAINT `fk_article_highlight_article` FOREIGN KEY (`fk_article`) REFERENCES `Article` (`id`);

--
-- Constraints for table `Article_Images`
--
ALTER TABLE `Article_Images`
  ADD CONSTRAINT `fk_article_image` FOREIGN KEY (`fk_article`) REFERENCES `Article` (`id`);

--
-- Constraints for table `Article_Property`
--
ALTER TABLE `Article_Property`
  ADD CONSTRAINT `fk_article_property_article` FOREIGN KEY (`fk_article`) REFERENCES `Article` (`id`),
  ADD CONSTRAINT `fk_article_property_property` FOREIGN KEY (`fk_property`) REFERENCES `Property` (`id`);

--
-- Constraints for table `Article_Review`
--
ALTER TABLE `Article_Review`
  ADD CONSTRAINT `fk_article_review_article` FOREIGN KEY (`fk_article`) REFERENCES `Article` (`id`),
  ADD CONSTRAINT `fk_article_review_user` FOREIGN KEY (`fk_user`) REFERENCES `User` (`id`);

--
-- Constraints for table `Bids`
--
ALTER TABLE `Bids`
  ADD CONSTRAINT `fk_bids_article` FOREIGN KEY (`fk_article`) REFERENCES `Article` (`id`),
  ADD CONSTRAINT `fk_bids_user` FOREIGN KEY (`fk_user`) REFERENCES `User` (`id`);

--
-- Constraints for table `Category_Property`
--
ALTER TABLE `Category_Property`
  ADD CONSTRAINT `fk_category_property_category` FOREIGN KEY (`fk_category`) REFERENCES `Category` (`id`),
  ADD CONSTRAINT `fk_category_property_property` FOREIGN KEY (`fk_property`) REFERENCES `Property` (`id`);

--
-- Constraints for table `Guide`
--
ALTER TABLE `Guide`
  ADD CONSTRAINT `Guide_Category_id_fk` FOREIGN KEY (`fk_category`) REFERENCES `Category` (`id`);

--
-- Constraints for table `Guide_Questions`
--
ALTER TABLE `Guide_Questions`
  ADD CONSTRAINT `fk_Guide_Questions_Guide` FOREIGN KEY (`fk_guide`) REFERENCES `Guide` (`id`),
  ADD CONSTRAINT `fk_Guide_Questions_Question` FOREIGN KEY (`fk_question`) REFERENCES `Question` (`id`);

--
-- Constraints for table `Order`
--
ALTER TABLE `Order`
  ADD CONSTRAINT `fk_order_state` FOREIGN KEY (`fk_state`) REFERENCES `Order_State` (`id`),
  ADD CONSTRAINT `fk_order_user` FOREIGN KEY (`fk_user`) REFERENCES `User` (`id`);

--
-- Constraints for table `Order_Article`
--
ALTER TABLE `Order_Article`
  ADD CONSTRAINT `fk_Order_Article_Article` FOREIGN KEY (`fk_article`) REFERENCES `Article` (`id`),
  ADD CONSTRAINT `fk_Order_Article_Order` FOREIGN KEY (`fk_order`) REFERENCES `Order` (`id`);

--
-- Constraints for table `Question_Answers`
--
ALTER TABLE `Question_Answers`
  ADD CONSTRAINT `Question_Answers_Question_id_fk` FOREIGN KEY (`fk_question`) REFERENCES `Question` (`id`);

--
-- Constraints for table `Session`
--
ALTER TABLE `Session`
  ADD CONSTRAINT `fk_customer` FOREIGN KEY (`fk_user`) REFERENCES `User` (`id`);

--
-- Constraints for table `Shopping_Basket`
--
ALTER TABLE `Shopping_Basket`
  ADD CONSTRAINT `fk_basket_article` FOREIGN KEY (`fk_article`) REFERENCES `Article` (`id`),
  ADD CONSTRAINT `fk_basket_user` FOREIGN KEY (`fk_user`) REFERENCES `User` (`id`);

--
-- Constraints for table `Site_Admin`
--
ALTER TABLE `Site_Admin`
  ADD CONSTRAINT `fk_user_admin` FOREIGN KEY (`fk_user`) REFERENCES `User` (`id`);

--
-- Constraints for table `User`
--
ALTER TABLE `User`
  ADD CONSTRAINT `fk_User_Organization` FOREIGN KEY (`fk_organization`) REFERENCES `Organization` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
