-- phpMyAdmin SQL Dump
-- version 4.6.6deb5
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Dec 17, 2019 at 03:47 PM
-- Server version: 5.7.28-0ubuntu0.18.04.4
-- PHP Version: 7.2.24-0ubuntu0.18.04.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `food_app`
--

-- --------------------------------------------------------

--
-- Table structure for table `email_format`
--

CREATE TABLE `email_format` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `body` text NOT NULL,
  `status` tinyint(4) NOT NULL DEFAULT '1' COMMENT '1=Active, 0=In-Active',
  `created_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `email_format`
--

INSERT INTO `email_format` (`id`, `title`, `subject`, `body`, `status`, `created_at`, `updated_at`) VALUES
(1, 'forgot_password', 'Forgot Password', '<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" width=\"100%\">\n    <tbody>\n        <tr>\n            <td style=\"padding:20px 0 20px 0\" align=\"center\" valign=\"top\"><!-- [ header starts here] -->\n            <table style=\"border:1px solid #E0E0E0;\" cellpadding=\"10\" cellspacing=\"0\" bgcolor=\"FFFFFF\" border=\"0\" width=\"650\">\n                <tbody>\n                    <tr>\n                        <td style=\"background: #444444; \" bgcolor=\"#EAEAEA\" valign=\"top\"><p style=\"color:#fff;display: inline-flex;\">&nbsp;&nbsp;Food App</p><p></p><p></p></td>\n                    </tr>\n                    <!-- [ middle starts here] -->\n                    <tr>\n                        <td valign=\"top\">\n                        <p>Dear  {username},</p>\n                        <p>Your New Password is :<br></p><p><strong>E-mail:</strong> {email}<br>\n                         </p><p><strong>Password:</strong> {password}<br>\n\n                        </p><p>&nbsp;</p>\n                        </td>\n                    </tr>\n                   <tr>\n                        <td style=\"background: #444444; text-align:center;color: white;\" align=\"center\" bgcolor=\"#EAEAEA\"><center>\n                        <p style=\"font-size:12px; margin:0;\">Food App team</p>\n                        </center></td>\n                    </tr>\n                </tbody>\n            </table>\n            </td>\n        </tr>\n    </tbody>\n</table>\n', 1, '2013-09-08 00:00:00', NULL),
(2, 'user_registration', 'Food App -New Account', '<table cellpadding=\"0\" cellspacing=\"0\" width=\"100%\" border=\"0\">\n    <tbody>\n        <tr>\n            <td style=\"padding:20px 0 20px 0\" valign=\"top\" align=\"center\"><!-- [ header starts here] -->\n            <table style=\"border:1px solid #E0E0E0;\" cellpadding=\"10\" cellspacing=\"0\" width=\"650\" bgcolor=\"FFFFFF\" border=\"0\">\n                <tbody>\n                    <tr>\n                        <td style=\"background: #444444; \" bgcolor=\"#EAEAEA\" valign=\"top\"><p style=\"color:#fff;display: inline-flex;\">&nbsp;&nbsp;Food App</p><p></p><p></p></td>\n                    </tr>\n                    <!-- [ middle starts here] -->\n                    <tr>\n                        <td valign=\"top\">\n                        <p>Dear  {username},</p>\n                        <p>Your account has been created.<br></p>\n                          <p>{loginurl}<br></p>\n                          <p><strong>E-mail:</strong> {email} <br></p>\n<p><strong>Password:</strong> {password} <br></p>\n                        <p></p><p>&nbsp;</p>\n                        </td>\n                    </tr>\n                   <tr>\n                        <td style=\"background: #444444; text-align:center;color: white;\" align=\"center\" bgcolor=\"#EAEAEA\"><center>\n                        <p style=\"font-size:12px; margin:0;\">Food App</p>\n                        </center></td>\n                    </tr>\n                </tbody>\n            </table>\n            </td>\n        </tr>\n    </tbody>\n</table>\n', 1, '2013-09-08 00:00:00', NULL),
(3, 'reset_password', 'Reset Password', '<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" width=\"100%\">\n    <tbody>\n        <tr>\n            <td style=\"padding:20px 0 20px 0\" align=\"center\" valign=\"top\"><!-- [ header starts here] -->\n            <table style=\"border:1px solid #E0E0E0;\" cellpadding=\"10\" cellspacing=\"0\" bgcolor=\"FFFFFF\" border=\"0\" width=\"650\">\n                <tbody>\n                   <tr>\n                        <td style=\"background: #444444; \" bgcolor=\"#EAEAEA\" valign=\"top\"><p style=\"color:#fff;display: inline-flex;\">&nbsp;&nbsp;Food App</p><p></p><p></p></td>\n                    </tr>\n                    <!-- [ middle starts here] -->\n                    <tr>\n                        <td valign=\"top\">\n                        <p>Dear  {username},</p>\n                        <p>Follow the link below to reset your password:</p>\n                        <p>{resetLink}</p>\n\n                        </p><p>&nbsp;</p>\n                        </td>\n                    </tr>\n                   <tr>\n                        <td style=\"background: #444444; text-align:center;color: white;\" align=\"center\" bgcolor=\"#EAEAEA\"><center>\n                        <p style=\"font-size:12px; margin:0;\">Food App</p>\n                        </center></td>\n                    </tr>\n                </tbody>\n            </table>\n            </td>\n        </tr>\n    </tbody>\n</table>\n', 1, '2013-09-08 00:00:00', NULL),
(4, 'contact_us', 'Food App Contact', '<table cellpadding=\"0\" cellspacing=\"0\" width=\"100%\" border=\"0\">\n    <tbody>\n        <tr>\n            <td style=\"padding:20px 0 20px 0\" valign=\"top\" align=\"center\"><!-- [ header starts here] -->\n            <table style=\"border:1px solid #E0E0E0;\" cellpadding=\"10\" cellspacing=\"0\" width=\"650\" bgcolor=\"FFFFFF\" border=\"0\">\n                <tbody>\n                     <tr>\n                        <td style=\"background: #444444; \" bgcolor=\"#EAEAEA\" valign=\"top\"><p style=\"color:#fff;display: inline-flex;\">&nbsp;&nbsp;Food App</p><p></p><p></p></td>\n                    </tr>\n                    <!-- [ middle starts here] -->\n                    <tr>\n                        <td valign=\"top\">\n                        <p>Hello  Food App Admin,\n                        <p>{message}<br></p>\n                        <p></p><p>&nbsp;</p>\n                        </td>\n                    </tr>\n                   <tr>\n                        <td style=\"background: #444444; text-align:center;color: white;\" align=\"center\" bgcolor=\"#EAEAEA\"><center>\n                        <p style=\"font-size:12px; margin:0;\">{name}</p>\n                        </center></td>\n                    </tr>\n                </tbody>\n            </table>\n            </td>\n        </tr>\n    </tbody>\n</table>\n', 1, '2013-09-08 00:00:00', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `migration`
--

CREATE TABLE `migration` (
  `version` varchar(180) NOT NULL,
  `apply_time` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `migration`
--

INSERT INTO `migration` (`version`, `apply_time`) VALUES
('m000000_000000_base', 1576498339),
('m130524_201442_init', 1576498345),
('m190124_110200_add_verification_token_column_to_user_table', 1576498347),
('m191216_132958_users', 1576566801),
('m191217_071531_user_roles', 1576567743),
('m191217_075341_users_update', 1576569569);

-- --------------------------------------------------------

--
-- Table structure for table `pages`
--

CREATE TABLE `pages` (
  `id` int(11) NOT NULL,
  `custom_url` varchar(255) NOT NULL,
  `page_title` varchar(255) NOT NULL,
  `page_content` text NOT NULL,
  `meta_title` varchar(255) DEFAULT NULL,
  `meta_keyword` varchar(255) DEFAULT NULL,
  `meta_description` text,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `pages`
--

INSERT INTO `pages` (`id`, `custom_url`, `page_title`, `page_content`, `meta_title`, `meta_keyword`, `meta_description`, `status`, `created_at`, `updated_at`) VALUES
(4, 'community-guidelines', 'Community Guidelines', '<p><em><strong>Community Guidelines1</strong></em></p>\r\n', 'Community Guidelines', 'Community Guidelines', '', 1, NULL, NULL),
(5, 'annoucements', 'Annoucements', '<p><em><strong>Annoucements</strong></em></p>\r\n', 'Annoucements', 'Annoucements', 'Annoucements', 1, NULL, NULL),
(6, 'terms-of-use', 'Terms of Use', '<p><em><strong>Terms of Use</strong></em></p>\r\n', 'Terms of Use', 'Terms of Use', 'Terms of Use', 1, NULL, NULL),
(7, 'about-bridge', 'About Bridge', '<p><strong>About Bridge</strong></p>\r\n\r\n<p>B4P.et &ndash; BRIDGE for participation &ndash; is a dedicated web-based application to bring individual parliamentarians and their constituents together as part of an inclusive, interactive online political community.</p>\r\n\r\n<p>The App will be accessible on tablets and smartphones, and its functionality will include creating and making visible parliamentarian profiles, and allowing parliamentarians and those they represent to post questions, comments and replies to one another.</p>\r\n\r\n<p>You can contact us through the below channels.</p>\r\n\r\n<p>Email: info@b4p.et<br />\r\nPhone: +251930294007</p>\r\n', 'About Bridge', 'About Bridge', 'About Bridge', 1, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `id` int(11) NOT NULL,
  `username` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `auth_key` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `password_hash` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `password_reset_token` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `status` smallint(6) NOT NULL DEFAULT '10',
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) NOT NULL,
  `verification_token` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id`, `username`, `auth_key`, `password_hash`, `password_reset_token`, `email`, `status`, `created_at`, `updated_at`, `verification_token`) VALUES
(1, 'rutusha', '9RVFrCft1DVvnHvt7bdSuQOFGOV2oE0L', '$2y$13$ypJyQCuCKVvpIDKMCdJdyOZ.7/z6TDkqqf6OOLG0enIEm3EZx/iHy', NULL, 'rutusha1212joshi@gmail.com', 9, 1576498801, 1576498801, 'dW2jJIJJJCpqHsW-RdSVGE6iPStmG2Mq_1576498801');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) NOT NULL,
  `role_id` int(11) DEFAULT NULL,
  `user_name` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `age` int(11) DEFAULT NULL,
  `gender` tinyint(5) DEFAULT NULL,
  `photo` varchar(255) DEFAULT NULL,
  `badge_count` int(11) DEFAULT NULL,
  `status` smallint(6) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `restaurant_id` bigint(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `role_id`, `user_name`, `email`, `password`, `age`, `gender`, `photo`, `badge_count`, `status`, `created_at`, `updated_at`, `restaurant_id`) VALUES
(1, 1, 'super_admin', 'rutusha1212joshi@gmail.com', '170c7cc2c55fdcb4c95b2a670035bf61', 29, 1, 'test.png', 0, 1, '2019-12-17 00:00:00', '2019-12-17 00:00:00', 1);

-- --------------------------------------------------------

--
-- Table structure for table `user_roles`
--

CREATE TABLE `user_roles` (
  `id` int(11) NOT NULL,
  `role_name` varchar(255) DEFAULT NULL,
  `role_description` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `user_roles`
--

INSERT INTO `user_roles` (`id`, `role_name`, `role_description`) VALUES
(1, 'super_admin', 'Super Admin'),
(2, 'admin', 'admin'),
(3, 'customer', 'customer');

-- --------------------------------------------------------

--
-- Table structure for table `user_rules`
--

CREATE TABLE `user_rules` (
  `id` int(11) NOT NULL,
  `role_id` int(11) NOT NULL,
  `privileges_controller` varchar(255) NOT NULL,
  `privileges_actions` text NOT NULL,
  `permission` enum('allow','deny') NOT NULL DEFAULT 'allow',
  `permission_type` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `user_rules`
--

INSERT INTO `user_rules` (`id`, `role_id`, `privileges_controller`, `privileges_actions`, `permission`, `permission_type`) VALUES
(1, 1, 'SiteController', 'index,logout,change-password,forgot-password', 'allow', 'super admin'),
(2, 2, 'SiteController', 'index,logout,change-password,forgot-password', 'allow', 'admin'),
(3, 1, 'UsersController', 'index,create,view,update,delete', 'allow', 'super admin'),
(4, 2, 'UsersController', 'index,create,view,update,delete', 'allow', 'admin'),
(5, 1, 'PagesController', 'index,create,update,delete,view,delete-all', 'allow', 'super admin'),
(6, 2, 'PagesController', 'index,create,update,delete,view,delete-all', 'allow', 'admin');

-- --------------------------------------------------------

--
-- Table structure for table `user_rules_menu`
--

CREATE TABLE `user_rules_menu` (
  `id` int(10) NOT NULL,
  `category` enum('admin','front-top','front-bottom','front-middle') NOT NULL DEFAULT 'admin',
  `parent_id` int(10) NOT NULL DEFAULT '0',
  `user_rules_id` int(10) NOT NULL,
  `label` varchar(255) NOT NULL,
  `class` varchar(255) DEFAULT NULL,
  `url` varchar(255) NOT NULL,
  `position` int(10) NOT NULL,
  `status` tinyint(4) NOT NULL DEFAULT '1' COMMENT '0 - inactive, 1 - active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `user_rules_menu`
--

INSERT INTO `user_rules_menu` (`id`, `category`, `parent_id`, `user_rules_id`, `label`, `class`, `url`, `position`, `status`) VALUES
(1, 'admin', 0, 1, 'Dashboard', 'icon-home', 'site/index', 1, 1),
(2, 'admin', 0, 2, 'Dashboard', 'icon-home', 'site/index', 1, 1),
(3, 'admin', 0, 3, 'Manage Users', 'icon-user', 'users/index', 2, 1),
(4, 'admin', 0, 4, 'Manage Users', 'icon-user', 'users/index', 2, 1),
(5, 'admin', 0, 5, 'Manage Pages', 'icon-user', 'pages/index', 3, 1),
(11, 'admin', 0, 6, 'Manage Pages', 'icon-user', 'pages/index', 3, 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `email_format`
--
ALTER TABLE `email_format`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `migration`
--
ALTER TABLE `migration`
  ADD PRIMARY KEY (`version`);

--
-- Indexes for table `pages`
--
ALTER TABLE `pages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `password_reset_token` (`password_reset_token`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx-users-role_id` (`role_id`),
  ADD KEY `role_id` (`role_id`);

--
-- Indexes for table `user_roles`
--
ALTER TABLE `user_roles`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user_rules`
--
ALTER TABLE `user_rules`
  ADD PRIMARY KEY (`id`),
  ADD KEY `role_id` (`role_id`);

--
-- Indexes for table `user_rules_menu`
--
ALTER TABLE `user_rules_menu`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_rules_id` (`user_rules_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `user_roles`
--
ALTER TABLE `user_roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT for table `user_rules`
--
ALTER TABLE `user_rules`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
--
-- AUTO_INCREMENT for table `user_rules_menu`
--
ALTER TABLE `user_rules_menu`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;
--
-- Constraints for dumped tables
--

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `fk_role_id` FOREIGN KEY (`role_id`) REFERENCES `user_roles` (`id`) ON DELETE SET NULL ON UPDATE SET NULL;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
