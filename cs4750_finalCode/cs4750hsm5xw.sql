-- phpMyAdmin SQL Dump
-- version 4.0.5
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Dec 03, 2013 at 09:07 AM
-- Server version: 5.5.34-0ubuntu0.12.04.1
-- PHP Version: 5.3.10-1ubuntu3.8

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `cs4750hsm5xw`
--

DELIMITER $$
--
-- Procedures
--
CREATE DEFINER=`cs4750hsm5xw`@`%` PROCEDURE `RemoveProductsAfterOrder`(IN `given_listID` VARCHAR(25))
    NO SQL
BEGIN
	DECLARE done int default false;
	DECLARE newProductID varchar(25);
	DECLARE amountTaken int;
	DECLARE productCursor CURSOR FOR SELECT product_id, amount FROM product_list WHERE list_id = given_listID;
	DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;

	OPEN productCursor;

	read_loop: LOOP
		FETCH productCursor INTO newProductID, amountTaken;
		IF done THEN
			LEAVE read_loop;
		END IF;
		UPDATE product SET amount_left = (amount_left - amountTaken) WHERE product_id = newProductID;
	END LOOP read_loop;
	close productCursor;
END$$

--
-- Functions
--
CREATE DEFINER=`cs4750hsm5xw`@`%` FUNCTION `getInStock`(`given_listID` VARCHAR(8)) RETURNS int(11)
    NO SQL
BEGIN
	DECLARE done int default false;
	DECLARE newProductID varchar(25);
	DECLARE amountTaken int;
	DECLARE result int;
	DECLARE tempAmount int;
	DECLARE productCursor CURSOR FOR SELECT product_id, amount FROM product_list WHERE list_id = given_listID;
	DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;

	SET result = 1;
	OPEN productCursor;

	read_loop: LOOP
		FETCH productCursor INTO newProductID, amountTaken;
		IF done THEN
			LEAVE read_loop;
		END IF;
		SELECT amount_left INTO tempAmount FROM product WHERE product_id = newProductID;
		IF (tempAmount - amountTaken) < 0 THEN
			SET result = 0;
		END IF;
	END LOOP read_loop;
	close productCursor;
	RETURN result;
END$$

CREATE DEFINER=`cs4750hsm5xw`@`%` FUNCTION `getNextListId`() RETURNS varchar(11) CHARSET latin1
    NO SQL
BEGIN
DECLARE returnValue varchar(11);

SELECT MAX(list_id) INTO returnValue FROM product_list;

RETURN returnValue+1;
END$$

CREATE DEFINER=`cs4750hsm5xw`@`%` FUNCTION `getTotalPrice`(`given_listID` VARCHAR(8)) RETURNS float
    NO SQL
BEGIN
	DECLARE done int default false;
	DECLARE newProductID varchar(8);
	DECLARE amountTaken int;
	DECLARE returnPrice float;
	DECLARE tempPrice float;
	DECLARE productName varchar(150);
	DECLARE productCursor CURSOR FOR SELECT `product_id`, `amount` FROM `product_list` WHERE `list_id` = given_listID;
	DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;

	OPEN productCursor;

	SET returnPrice = 0;
	read_loop: LOOP
		FETCH productCursor INTO newProductID, amountTaken;
		IF done THEN
			LEAVE read_loop;
		END IF;
		SELECT product_name INTO productName from `product` WHERE `product_id` = newProductID;
		SELECT price INTO tempPrice from `prices` WHERE `product_name` = productName;
		SET returnPrice = returnPrice + (amountTaken * tempPrice);
	END LOOP read_loop;
	close productCursor;
	RETURN returnPrice;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `address`
--

CREATE TABLE IF NOT EXISTS `address` (
  `phone_number` varchar(12) NOT NULL DEFAULT '',
  `street_addr` varchar(40) NOT NULL,
  `city` varchar(25) NOT NULL,
  `zip_code` varchar(5) NOT NULL,
  PRIMARY KEY (`phone_number`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `address`
--

INSERT INTO `address` (`phone_number`, `street_addr`, `city`, `zip_code`) VALUES
('123-333-7777', '1234 Main Street', 'Charlottesville', '22903'),
('123-456-7890', '106 Cherry Av', 'Lancaster', '55281'),
('12345678890', '555 Basit Street', 'Los Angeles', '11111'),
('133-555-4432', '643 Ashton St', 'Reno', '83752'),
('465-111-2222', '9321 Friendship Ln', 'Stapleton', '66234'),
('555-666-8888', '888 Main Street', 'Charlottesville', '22903'),
('772-156-3852', '412 August Rd', 'Tulsa', '52173'),
('888-888-9999', '555 Main Street', 'Charlottesville', '22903');

-- --------------------------------------------------------

--
-- Table structure for table `credit_card`
--

CREATE TABLE IF NOT EXISTS `credit_card` (
  `credit_card_num` varchar(16) NOT NULL DEFAULT '',
  `credit_card_type` varchar(20) NOT NULL DEFAULT '',
  `csv` varchar(3) NOT NULL,
  PRIMARY KEY (`credit_card_num`,`credit_card_type`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `credit_card`
--

INSERT INTO `credit_card` (`credit_card_num`, `credit_card_type`, `csv`) VALUES
('1111222233334444', 'Visa', '123'),
('2222333344445555', 'Visa', '234'),
('3333444455556666', 'MasterCard', '345'),
('4444555566667777', 'Discover', '456'),
('5555444433332222', 'Master Card', '888'),
('5555444433332222', 'Visa', '767'),
('7777666655554444', 'Visa', '777'),
('8888777766665555', 'Master Card', '897');

-- --------------------------------------------------------

--
-- Table structure for table `customer`
--

CREATE TABLE IF NOT EXISTS `customer` (
  `customer_id` varchar(20) NOT NULL DEFAULT '',
  `password` varchar(20) NOT NULL,
  `first_name` varchar(20) NOT NULL,
  `last_name` varchar(20) NOT NULL,
  `phone_number` varchar(12) NOT NULL,
  PRIMARY KEY (`customer_id`),
  UNIQUE KEY `customer_id` (`customer_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `customer`
--

INSERT INTO `customer` (`customer_id`, `password`, `first_name`, `last_name`, `phone_number`) VALUES
('Customer01', '111Password', 'Bob', 'Smith', '123-456-7890'),
('Customer02', '222Password', 'Ann', 'Reynolds', '465-111-2222'),
('Customer03', '333Password', 'John', 'Doe', '133-555-4432'),
('Customer04', '444Password', 'Roger', 'Johnson', '772-156-3852'),
('Customer05', '555Password', 'Nada', 'Basit', '123-333-7777'),
('Customer06', '666Password', 'Hong', 'Moon', '888-888-9999'),
('Customer07', '777Password', 'James', 'Cohoon', '555-666-8888'),
('Customer08', '888Password', 'Nadadadad', 'Basitbasit', '12345678890');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE IF NOT EXISTS `orders` (
  `order_id` int(11) NOT NULL AUTO_INCREMENT,
  `customer_id` varchar(20) NOT NULL,
  `list_id` varchar(8) NOT NULL,
  `order_date` varchar(20) NOT NULL,
  `total_order_price` float NOT NULL,
  `order_status` varchar(180) NOT NULL,
  `payment_method_id` varchar(8) NOT NULL,
  PRIMARY KEY (`order_id`),
  KEY `Foreign` (`customer_id`),
  KEY `Foreign Product` (`list_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=41 ;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`order_id`, `customer_id`, `list_id`, `order_date`, `total_order_price`, `order_status`, `payment_method_id`) VALUES
(1, 'Customer01', '1', '2013-11-05', 225.35, 'Delivered', '1'),
(2, 'Customer02', '2', '2013-10-30', 168.68, 'Delivered', '2'),
(3, 'Customer03', '3', '2013-11-01', 286.78, 'Shipped', '3'),
(4, 'Customer04', '4', '2013-10-28', 43.52, 'Delivered', '4'),
(38, 'Customer07', '7', '2013-12-03', 394.03, 'Not shipped yet', '38'),
(39, 'Customer05', '8', '2013-12-03', 703.72, 'Not shipped yet', '39'),
(40, 'Customer08', '9', '2013-12-03', 535.04, 'Not shipped yet', '40');

--
-- Triggers `orders`
--
DROP TRIGGER IF EXISTS `AfterOrderUpdate`;
DELIMITER //
CREATE TRIGGER `AfterOrderUpdate` AFTER INSERT ON `orders`
 FOR EACH ROW BEGIN
	INSERT INTO order_history(customer_id, order_id) VALUES (NEW.customer_id, NEW.order_id);
	CALL RemoveProductsAfterOrder(NEW.list_id);
END
//
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `order_history`
--

CREATE TABLE IF NOT EXISTS `order_history` (
  `customer_id` varchar(20) NOT NULL DEFAULT '',
  `order_id` int(11) NOT NULL,
  PRIMARY KEY (`customer_id`,`order_id`),
  KEY `order_id` (`order_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `order_history`
--

INSERT INTO `order_history` (`customer_id`, `order_id`) VALUES
('Customer01', 1),
('Customer02', 2),
('Customer03', 3),
('Customer04', 4),
('Customer07', 38),
('Customer05', 39),
('Customer08', 40);

-- --------------------------------------------------------

--
-- Table structure for table `payment_method`
--

CREATE TABLE IF NOT EXISTS `payment_method` (
  `payment_method_id` int(11) NOT NULL AUTO_INCREMENT,
  `credit_card_num` varchar(16) NOT NULL,
  `credit_card_type` varchar(20) NOT NULL,
  PRIMARY KEY (`payment_method_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=41 ;

--
-- Dumping data for table `payment_method`
--

INSERT INTO `payment_method` (`payment_method_id`, `credit_card_num`, `credit_card_type`) VALUES
(1, '1111222233334444', 'Visa'),
(2, '2222333344445555', 'Visa'),
(3, '3333444455556666', 'MasterCard'),
(4, '4444555566667777', 'Discover'),
(37, '5555444433332222', 'Master Card'),
(38, '7777666655554444', 'Visa'),
(39, '5555444433332222', 'Visa'),
(40, '8888777766665555', 'Master Card');

-- --------------------------------------------------------

--
-- Table structure for table `prices`
--

CREATE TABLE IF NOT EXISTS `prices` (
  `product_name` varchar(50) NOT NULL DEFAULT '',
  `price` float NOT NULL,
  PRIMARY KEY (`product_name`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `prices`
--

INSERT INTO `prices` (`product_name`, `price`) VALUES
('D6 Tennis Racquet', 43.52),
('E3 Tennis Racquet', 156.22),
('Slidemaster 6000', 84.34),
('XL Pool 3000', 225.35);

-- --------------------------------------------------------

--
-- Table structure for table `product`
--

CREATE TABLE IF NOT EXISTS `product` (
  `product_id` varchar(8) NOT NULL DEFAULT '',
  `product_name` varchar(50) NOT NULL,
  `amount_left` int(11) NOT NULL,
  `vendor_name` varchar(20) NOT NULL,
  `supplied_date` varchar(20) NOT NULL,
  `product_description` varchar(180) NOT NULL,
  `product_url` varchar(200) NOT NULL,
  PRIMARY KEY (`product_id`),
  KEY `vendor_name` (`vendor_name`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `product`
--

INSERT INTO `product` (`product_id`, `product_name`, `amount_left`, `vendor_name`, `supplied_date`, `product_description`, `product_url`) VALUES
('BPC001', 'XL Pool 3000', 7, 'Backyard Pool Compan', '2013-10-06', 'Very large model of backyard pool', 'http://plato.cs.virginia.edu/~djk5as/Products/pool.png'),
('DSI001', 'Slidemaster 6000', 15, 'Dummet Slides Inc.', '2013-10-21', 'A fun and fast slide set', 'http://plato.cs.virginia.edu/~hsm5xw/Products/DSI001.jpg'),
('WCO001', 'E3 Tennis Racquet', 24, 'Wilson Company', '2013-11-01', 'Expert-level tennis racquet', 'http://plato.cs.virginia.edu/~hsm5xw/Products/WCO001.jpg'),
('WCO002', 'D6 Tennis Racquet', 12, 'Wilson Company', '2013-10-18', 'Beginner-level tennis racquet', 'http://plato.cs.virginia.edu/~hsm5xw/Products/WCO002.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `product_list`
--

CREATE TABLE IF NOT EXISTS `product_list` (
  `list_id` varchar(8) NOT NULL DEFAULT '',
  `product_id` varchar(8) NOT NULL DEFAULT '',
  `amount` int(11) NOT NULL,
  PRIMARY KEY (`list_id`,`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `product_list`
--

INSERT INTO `product_list` (`list_id`, `product_id`, `amount`) VALUES
('1', 'BPC001', 1),
('2', 'DSI001', 2),
('3', 'WCO001', 1),
('3', 'WCO002', 3),
('4', 'WCO002', 1),
('5', 'BPC001', 2),
('5', 'WCO002', 3),
('6', 'BPC001', 2),
('6', 'WCO002', 3),
('7', 'BPC001', 1),
('7', 'DSI001', 2),
('8', 'BPC001', 2),
('8', 'DSI001', 3),
('9', 'BPC001', 2),
('9', 'DSI001', 1);

-- --------------------------------------------------------

--
-- Table structure for table `product_review`
--

CREATE TABLE IF NOT EXISTS `product_review` (
  `product_review_id` int(8) NOT NULL AUTO_INCREMENT,
  `product_id` varchar(8) NOT NULL,
  `customer_id` varchar(20) NOT NULL,
  `date` varchar(20) NOT NULL,
  `review_content` varchar(450) NOT NULL,
  `star_rating` int(11) NOT NULL,
  PRIMARY KEY (`product_review_id`),
  KEY `customer_id` (`customer_id`),
  KEY `product_id` (`product_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=10 ;

--
-- Dumping data for table `product_review`
--

INSERT INTO `product_review` (`product_review_id`, `product_id`, `customer_id`, `date`, `review_content`, `star_rating`) VALUES
(1, 'BPC001', 'Customer01', '2013-11-28', 'The pool does not leak, but it is smaller than advertised!', 3),
(2, 'WCO002', 'Customer04', '2013-11-27', 'This racquet it fantastic for beginners. I would highly recommend it.', 5),
(5, 'WCO002', 'Customer01', '2013-12-03', ' This tennis racquet sucks', 2),
(6, 'DSI001', 'Customer02', '2013-12-03', ' My kids like them.', 5),
(7, 'DSI001', 'Customer03', '2013-12-03', 'fun! great for price', 4),
(8, 'BPC001', 'Customer03', '2013-12-03', ' good pool', 4),
(9, 'WCO002', 'Customer03', '2013-12-03', ' This is a good racquet', 4);

-- --------------------------------------------------------

--
-- Table structure for table `region`
--

CREATE TABLE IF NOT EXISTS `region` (
  `zip_code` varchar(5) NOT NULL DEFAULT '',
  `state` varchar(2) NOT NULL,
  PRIMARY KEY (`zip_code`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `region`
--

INSERT INTO `region` (`zip_code`, `state`) VALUES
('11111', 'CA'),
('22903', 'VA'),
('52173', 'OK'),
('55281', 'PA'),
('66234', 'TX'),
('83752', 'NV');

-- --------------------------------------------------------

--
-- Table structure for table `supply`
--

CREATE TABLE IF NOT EXISTS `supply` (
  `vendor_name` varchar(20) NOT NULL DEFAULT '',
  `product_id` varchar(8) NOT NULL DEFAULT '',
  `supplied_date` varchar(20) NOT NULL,
  `contact_personnel_email` varchar(45) NOT NULL,
  `supplied_amount` int(11) NOT NULL,
  PRIMARY KEY (`vendor_name`,`product_id`),
  KEY `product_id` (`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `supply`
--

INSERT INTO `supply` (`vendor_name`, `product_id`, `supplied_date`, `contact_personnel_email`, `supplied_amount`) VALUES
('Backyard Pool Compan', 'BPC001', '2013-10-06', 'contact@pools.com', 25),
('Dummet Slides Inc.', 'DSI001', '2013-10-21', 'contact@dummetslides.com', 25),
('Wilson Company', 'WCO001', '2013-11-01', 'contact@wilson.com', 25),
('Wilson Company', 'WCO002', '2013-10-18', 'contact@wilson.com', 25);

-- --------------------------------------------------------

--
-- Table structure for table `vendor`
--

CREATE TABLE IF NOT EXISTS `vendor` (
  `vendor_name` varchar(20) NOT NULL DEFAULT '',
  `phone_number` varchar(12) DEFAULT NULL,
  `contact_personnel_email` varchar(45) NOT NULL,
  PRIMARY KEY (`vendor_name`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `vendor`
--

INSERT INTO `vendor` (`vendor_name`, `phone_number`, `contact_personnel_email`) VALUES
('Backyard Pool Compan', '111-222-3333', 'contact@pools.com'),
('Dummet Slides Inc.', '222-333-4444', 'contact@dummetslides.com'),
('Wilson Company', '333-444-5555', 'contact@wilson.com');

--
-- Constraints for dumped tables
--

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `Customer Foreign Key` FOREIGN KEY (`customer_id`) REFERENCES `customer` (`customer_id`),
  ADD CONSTRAINT `Product Foreign key` FOREIGN KEY (`list_id`) REFERENCES `product_list` (`list_id`);

--
-- Constraints for table `order_history`
--
ALTER TABLE `order_history`
  ADD CONSTRAINT `order_history_ibfk_2` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`),
  ADD CONSTRAINT `order_history_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customer` (`customer_id`);

--
-- Constraints for table `product`
--
ALTER TABLE `product`
  ADD CONSTRAINT `product_ibfk_1` FOREIGN KEY (`vendor_name`) REFERENCES `vendor` (`vendor_name`);

--
-- Constraints for table `product_review`
--
ALTER TABLE `product_review`
  ADD CONSTRAINT `product_review_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `product` (`product_id`),
  ADD CONSTRAINT `product_review_ibfk_2` FOREIGN KEY (`customer_id`) REFERENCES `customer` (`customer_id`);

--
-- Constraints for table `supply`
--
ALTER TABLE `supply`
  ADD CONSTRAINT `supply_ibfk_1` FOREIGN KEY (`vendor_name`) REFERENCES `vendor` (`vendor_name`),
  ADD CONSTRAINT `supply_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `product` (`product_id`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
