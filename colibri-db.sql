{%begin%}
  SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
{%end%}

{%begin%}
  SET time_zone = "+00:00";
{%end%}

{%begin%}
  CREATE TABLE `cl_bookmarks` (
    `id` int(11) NOT NULL,
    `publication_id` int(11) NOT NULL DEFAULT '0',
    `user_id` int(11) NOT NULL DEFAULT '0',
    `time` varchar(25) NOT NULL DEFAULT '0'
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
{%end%}

{%begin%}
  CREATE TABLE `cl_chats` (
    `id` int(11) NOT NULL,
    `user_one` int(11) NOT NULL DEFAULT '0',
    `user_two` int(11) NOT NULL DEFAULT '0',
    `time` varchar(25) NOT NULL DEFAULT '0'
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
{%end%}

{%begin%}
  CREATE TABLE `cl_configs` (
    `id` int(11) NOT NULL,
    `title` varchar(120) NOT NULL DEFAULT '',
    `name` varchar(120) NOT NULL DEFAULT '',
    `value` varchar(3000) NOT NULL DEFAULT '',
    `regex` varchar(120) NOT NULL DEFAULT ''
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
{%end%}

{%begin%}
  INSERT INTO `cl_configs` (`id`, `title`, `name`, `value`, `regex`) VALUES
    (1, 'Theme', 'theme', 'default', ''),
    (2, 'Site name', 'name', 'SMColibri', '/^(.){0,50}$/'),
    (3, 'Site title', 'title', 'Colibri Social Media', '/^(.){0,150}$/'),
    (4, 'Site description', 'description', 'ColibriSM - The Ultimate Modern Social Media Sharing Platform', '/^(.){0,350}$/'),
    (5, 'SEO keywords', 'keywords', 'social,socialnetwork', ''),
    (6, 'Site logo', 'site_logo', 'statics/img/logo.png', ''),
    (7, 'Site favicon', 'site_favicon', 'statics/img/favicon.png', ''),
    (8, 'Chat wallpaper', 'chat_wp', 'statics/img/chatwp/default.png', ''),
    (9, 'Account activation', 'acc_validation', 'off', '/^(on|off)$/'),
    (10, 'Default language', 'language', 'english', ''),
    (11, 'AS3 storage', 'as3_storage', 'off', '/^(on|off)$/'),
    (12, 'E-mail address', 'email', '', ''),
    (13, 'SMTP server', 'smtp_or_mail', 'smtp', '/^(smtp|mail)$/'),
    (14, 'SMTP host', 'smtp_host', '', ''),
    (15, 'SMTP password', 'smtp_password', '', '/^(.){0,50}$/'),
    (16, 'SMTP encryption', 'smtp_encryption', 'tls', '/^(ssl|tls)$/'),
    (17, 'SMTP port', 'smtp_port', '587', '/^[0-9]{1,11}$/'),
    (18, 'SMTP username', 'smtp_username', '', ''),
    (19, 'FFMPEG binary', 'ffmpeg_binary', 'core/libs/ffmpeg/ffmpeg', '/^(.){0,550}$/'),
    (20, 'Giphy api', 'giphy_api_key', 'EEoFiCosGuyEIWlXnRuw4McTLxfjCrl1', '/^(.){0,150}$/'),
    (21, 'Google analytics', 'google_analytics', '', ''),
    (22, 'Facebook API ID', 'facebook_api_id', '', '/^(.){0,150}$/'),
    (23, 'Facebook API Key', 'facebook_api_key', '', '/^(.){0,150}$/'),
    (24, 'Twitter API ID', 'twitter_api_id', '', '/^(.){0,150}$/'),
    (25, 'Twitter API Key', 'twitter_api_key', '', '/^(.){0,150}$/'),
    (26, 'Google API ID', 'google_api_id', '', '/^(.){0,150}$/'),
    (27, 'Google API Key', 'google_api_key', '', '/^(.){0,150}$/'),
    (28, 'Script version', 'version', '1.0.7', ''),
    (29, 'Last backup', 'last_backup', '0', '');
{%end%}

{%begin%}
  CREATE TABLE `cl_connections` (
    `id` int(11) NOT NULL,
    `follower_id` int(11) NOT NULL DEFAULT '0',
    `following_id` int(11) NOT NULL DEFAULT '0',
    `time` varchar(25) NOT NULL DEFAULT '25'
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
{%end%}

{%begin%}
  CREATE TABLE `cl_hashtags` (
    `id` int(11) NOT NULL,
    `posts` int(11) NOT NULL DEFAULT '0',
    `tag` varchar(200) CHARACTER SET utf8mb4 NOT NULL DEFAULT '',
    `time` varchar(25) NOT NULL DEFAULT '0'
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
{%end%}

{%begin%}
  CREATE TABLE `cl_messages` (
    `id` int(11) NOT NULL,
    `sent_by` int(11) NOT NULL DEFAULT '0',
    `sent_to` int(11) NOT NULL DEFAULT '0',
    `owner` int(11) NOT NULL DEFAULT '0',
    `message` varchar(3000) NOT NULL DEFAULT '',
    `media_file` varchar(1000) NOT NULL DEFAULT '',
    `media_type` varchar(25) NOT NULL DEFAULT 'none',
    `seen` varchar(25) NOT NULL DEFAULT '0',
    `deleted_fs1` enum('Y','N') NOT NULL DEFAULT 'N',
    `deleted_fs2` enum('Y','N') NOT NULL DEFAULT 'N',
    `time` varchar(25) NOT NULL DEFAULT '0'
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
{%end%}

{%begin%}
  CREATE TABLE `cl_notifications` (
    `id` int(11) NOT NULL,
    `notifier_id` int(11) NOT NULL DEFAULT '0',
    `recipient_id` int(11) NOT NULL DEFAULT '0',
    `status` enum('0','1') NOT NULL DEFAULT '0',
    `subject` varchar(32) NOT NULL DEFAULT 'none',
    `entry_id` int(11) NOT NULL DEFAULT '0',
    `json` varchar(1200) NOT NULL DEFAULT '[]',
    `time` varchar(25) NOT NULL DEFAULT '0'
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
{%end%}

{%begin%}
  CREATE TABLE `cl_posts` (
    `id` int(11) NOT NULL,
    `user_id` int(11) NOT NULL DEFAULT '0',
    `publication_id` int(11) NOT NULL DEFAULT '0',
    `type` enum('post','repost') NOT NULL DEFAULT 'post',
    `time` varchar(25) NOT NULL DEFAULT '0'
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
{%end%}

{%begin%}
  CREATE TABLE `cl_publications` (
    `id` int(11) NOT NULL,
    `user_id` int(11) NOT NULL DEFAULT '0',
    `text` varchar(600) CHARACTER SET utf8mb4 NOT NULL DEFAULT '',
    `type` enum('text','video','image','gif') NOT NULL DEFAULT 'text',
    `replys_count` int(11) NOT NULL DEFAULT '0',
    `reposts_count` int(11) NOT NULL DEFAULT '0',
    `likes_count` int(11) NOT NULL DEFAULT '0',
    `status` enum('active','inactive','deleted','orphan') NOT NULL DEFAULT 'active',
    `thread_id` int(11) NOT NULL DEFAULT '0',
    `target` enum('publication','pub_reply') NOT NULL DEFAULT 'publication',
    `time` varchar(25) NOT NULL DEFAULT '0'
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
{%end%}

{%begin%}
  CREATE TABLE `cl_publikes` (
    `id` int(11) NOT NULL,
    `pub_id` int(11) NOT NULL DEFAULT '0',
    `user_id` int(11) NOT NULL DEFAULT '0',
    `time` varchar(25) NOT NULL DEFAULT '0'
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
{%end%}

{%begin%}
  CREATE TABLE `cl_pubmedia` (
    `id` int(11) NOT NULL,
    `pub_id` int(11) NOT NULL DEFAULT '0',
    `type` enum('image','video','gif') NOT NULL,
    `src` varchar(1200) NOT NULL DEFAULT '',
    `json_data` varchar(3000) NOT NULL DEFAULT '[]',
    `time` varchar(25) NOT NULL DEFAULT '0'
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
{%end%}

{%begin%}
  CREATE TABLE `cl_sessions` (
    `id` int(11) NOT NULL,
    `session_id` varchar(120) NOT NULL DEFAULT '',
    `user_id` varchar(11) NOT NULL DEFAULT '0',
    `platform` varchar(15) NOT NULL DEFAULT 'web',
    `time` varchar(25) NOT NULL DEFAULT '0'
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
{%end%}

{%begin%}
  CREATE TABLE `cl_users` (
    `id` int(11) NOT NULL,
    `username` varchar(30) NOT NULL DEFAULT '',
    `fname` varchar(30) NOT NULL DEFAULT '',
    `lname` varchar(30) NOT NULL DEFAULT '',
    `about` varchar(150) NOT NULL DEFAULT '',
    `gender` enum('M','F') NOT NULL DEFAULT 'M',
    `email` varchar(60) NOT NULL DEFAULT '',
    `em_code` varchar(100) NOT NULL DEFAULT '',
    `password` varchar(140) NOT NULL DEFAULT '',
    `joined` varchar(20) NOT NULL DEFAULT '0',
    `last_active` varchar(20) NOT NULL DEFAULT '0',
    `ip_address` varchar(140) NOT NULL DEFAULT '0.0.0.0',
    `language` varchar(32) NOT NULL DEFAULT 'default',
    `avatar` varchar(300) NOT NULL DEFAULT 'upload/default/avatar.png',
    `cover` varchar(300) NOT NULL DEFAULT 'upload/default/cover.png',
    `cover_orig` varchar(300) NOT NULL DEFAULT '',
    `active` enum('0','1') NOT NULL DEFAULT '0',
    `admin` enum('0','1') NOT NULL DEFAULT '0',
    `posts` int(11) NOT NULL DEFAULT '0',
    `followers` int(11) NOT NULL DEFAULT '0',
    `following` int(11) NOT NULL DEFAULT '0',
    `website` varchar(120) NOT NULL DEFAULT '',
    `country_id` int(3) NOT NULL DEFAULT '1',
    `last_post` int(11) NOT NULL DEFAULT '0'
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
{%end%}

{%begin%}
  ALTER TABLE `cl_bookmarks`
    ADD PRIMARY KEY (`id`);
{%end%}

{%begin%}
  ALTER TABLE `cl_chats`
    ADD PRIMARY KEY (`id`);
{%end%}

{%begin%}
  ALTER TABLE `cl_configs`
    ADD PRIMARY KEY (`id`);
{%end%}

{%begin%}
  ALTER TABLE `cl_connections`
    ADD PRIMARY KEY (`id`);
{%end%}

{%begin%}
  ALTER TABLE `cl_hashtags`
    ADD PRIMARY KEY (`id`);
{%end%}

{%begin%}
  ALTER TABLE `cl_messages`
    ADD PRIMARY KEY (`id`);
{%end%}

{%begin%}
  ALTER TABLE `cl_notifications`
    ADD PRIMARY KEY (`id`);
{%end%}

{%begin%}
  ALTER TABLE `cl_posts`
    ADD PRIMARY KEY (`id`);
{%end%}

{%begin%}
  ALTER TABLE `cl_publications`
    ADD PRIMARY KEY (`id`);
{%end%}

{%begin%}
  ALTER TABLE `cl_publikes`
    ADD PRIMARY KEY (`id`);
{%end%}

{%begin%}
  ALTER TABLE `cl_pubmedia`
    ADD PRIMARY KEY (`id`);
{%end%}

{%begin%}
  ALTER TABLE `cl_sessions`
    ADD PRIMARY KEY (`id`);
{%end%}

{%begin%}
  ALTER TABLE `cl_users`
    ADD PRIMARY KEY (`id`),
    ADD KEY `posts` (`posts`);
{%end%}

{%begin%}
  ALTER TABLE `cl_bookmarks`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
{%end%}

{%begin%}
  ALTER TABLE `cl_chats`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
{%end%}

{%begin%}
  ALTER TABLE `cl_configs`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
{%end%}

{%begin%}
  ALTER TABLE `cl_connections`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
{%end%}

{%begin%}
  ALTER TABLE `cl_hashtags`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
{%end%}

{%begin%}
  ALTER TABLE `cl_messages`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
{%end%}

{%begin%}
  ALTER TABLE `cl_notifications`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
{%end%}

{%begin%}
  ALTER TABLE `cl_posts`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
{%end%}

{%begin%}
  ALTER TABLE `cl_publications`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
{%end%}

{%begin%}
  ALTER TABLE `cl_publikes`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
{%end%}

{%begin%}
  ALTER TABLE `cl_pubmedia`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
{%end%}

{%begin%}
  ALTER TABLE `cl_sessions`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
{%end%}

{%begin%}
  ALTER TABLE `cl_users`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
{%end%}

{%begin%}
  CREATE TABLE `cl_verifications` ( `id` INT(11) NOT NULL AUTO_INCREMENT,  
    `user_id` INT(11) NOT NULL DEFAULT '0',  
    `full_name` VARCHAR(120) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',  
    `text_message` VARCHAR(1200) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',  
    `video_message` VARCHAR(300) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',  
    `status` ENUM('pending','rejected') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'pending',  
    `time` INT(25) NOT NULL DEFAULT '0',    
  PRIMARY KEY  (`id`)) ENGINE = InnoDB CHARSET=utf8 COLLATE utf8_general_ci;
{%end%}

{%begin%}
  ALTER TABLE `cl_users` ADD `verified` ENUM('0','1') 
    CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '0' AFTER `active`;
{%end%}

{%begin%}
  ALTER TABLE `cl_verifications` DROP `status`;
{%end%}

{%begin%}
  ALTER TABLE `cl_users` CHANGE `verified` `verified` ENUM('0','1','2') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '0';
{%end%}

{%begin%}
  ALTER TABLE `cl_users` ADD `profile_privacy` ENUM('everyone','followers','nobody') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'everyone' AFTER `last_post`;
{%end%}

{%begin%}
  ALTER TABLE `cl_users` ADD `contact_privacy` ENUM('everyone','followed') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'everyone' AFTER `profile_privacy`;
{%end%}

{%begin%}
  ALTER TABLE `cl_users` ADD `index_privacy` ENUM('Y','N') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'Y' AFTER `contact_privacy`;
{%end%}

{%begin%}
  INSERT INTO `cl_configs` (`id`, `title`, `name`, `value`, `regex`) VALUES (NULL, 'Sitemap last update', 'sitemap_update', '', '');
{%end%}

{%begin%}
  ALTER TABLE `cl_messages` CHANGE `message` `message` VARCHAR(3000) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '';
{%end%}

{%begin%}
  ALTER TABLE `cl_users` CHANGE `about` `about` VARCHAR(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '';
{%end%}

{%begin%}
  ALTER TABLE `cl_users` CHANGE `active` `active` ENUM('0','1','2') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '0';
{%end%}

{%begin%}
  CREATE TABLE `cl_profile_reports` ( `id` INT(11) NOT NULL AUTO_INCREMENT , `user_id` INT(11) NOT NULL DEFAULT '0' , `profile_id` INT(11) NOT NULL DEFAULT '0' , `reason` INT(3) NOT NULL DEFAULT '0' ,  `seen` ENUM('0','1') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '0' ,  `time` VARCHAR(25) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '0' ,  PRIMARY KEY  (`id`)) ENGINE = InnoDB CHARSET=utf8 COLLATE utf8_general_ci;
{%end%}

{%begin%}
  ALTER TABLE `cl_profile_reports` ADD `comment` VARCHAR(3000) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' AFTER `reason`;
{%end%}

{%begin%}
  CREATE TABLE `cl_blocks` ( `id` int(11) NOT NULL, `user_id` int(11) NOT NULL DEFAULT '0', `profile_id` int(11) NOT NULL DEFAULT '0', `time` varchar(25) NOT NULL DEFAULT '0' ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
{%end%}

{%begin%}
  ALTER TABLE `cl_blocks` ADD PRIMARY KEY(`id`);
{%end%}

{%begin%}
  ALTER TABLE `cl_blocks` CHANGE `id` `id` INT(11) NOT NULL AUTO_INCREMENT;
{%end%}

{%begin%}
  CREATE TABLE `cl_affiliate_payouts` ( `id` INT(11) NOT NULL AUTO_INCREMENT ,  `user_id` INT(11) NOT NULL DEFAULT '0' ,  `amount` VARCHAR(25) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '0.00' ,  `bonuses` INT(11) NOT NULL DEFAULT '0' ,  `status` ENUM('pending','paid','declined') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'pending' ,  `time` VARCHAR(25) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '0' ,    PRIMARY KEY  (`id`)) ENGINE = InnoDB CHARSET=utf8 COLLATE utf8_general_ci;
{%end%}

{%begin%}
  ALTER TABLE `cl_users` ADD `aff_bonuses` INT(11) NOT NULL DEFAULT '0' AFTER `index_privacy`;
{%end%}

{%begin%}
  INSERT INTO `cl_configs` (`id`, `title`, `name`, `value`, `regex`) VALUES (NULL, 'Affiliate bonus rate', 'aff_bonus_rate', '0.10', '/^([0-9]{1,3}\\.[0-9]{1,3}|[0-9]{1,3})$/');
{%end%}

{%begin%}
  INSERT INTO `cl_configs` (`id`, `title`, `name`, `value`, `regex`) VALUES (NULL, 'Affiliates System', 'affiliates_system', 'on', '/^(on|off)$/');
{%end%}

{%begin%}
  ALTER TABLE `cl_affiliate_payouts` ADD `email` VARCHAR(120) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' AFTER `user_id`;
{%end%}

{%begin%}
  ALTER TABLE `cl_affiliate_payouts` CHANGE `status` `status` ENUM('pending','paid') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'pending';
{%end%}

{%begin%}
  CREATE TABLE `cl_ads` ( `id` int(11) NOT NULL AUTO_INCREMENT, `user_id` int(11) NOT NULL DEFAULT '0', `company` varchar(120) NOT NULL DEFAULT '', `target_url` varchar(1200) NOT NULL DEFAULT '', `status` enum('orphan','active','inactive') NOT NULL DEFAULT 'orphan', `audience` varchar(3000) NOT NULL DEFAULT '0', `description` varchar(600) NOT NULL DEFAULT '', `cta` varchar(300) NOT NULL DEFAULT '', `budget` varchar(15) NOT NULL DEFAULT '0.00', `clicks` int(11) NOT NULL DEFAULT '0', `views` int(11) NOT NULL DEFAULT '0', `time` varchar(25) NOT NULL DEFAULT '0', PRIMARY KEY (`id`)) ENGINE=InnoDB DEFAULT CHARSET=utf8;
{%end%}

{%begin%}
  ALTER TABLE `cl_users` ADD `last_ad` INT(11) NOT NULL DEFAULT '0' AFTER `last_post`;
{%end%}

{%begin%}
  ALTER TABLE `cl_ads` CHANGE `audience` `audience` VARCHAR(3000) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '[]';
{%end%}

{%begin%}
  ALTER TABLE `cl_ads` ADD `cover` VARCHAR(3000) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' AFTER `user_id`;
{%end%}

{%begin%}
  ALTER TABLE `cl_users` ADD `wallet` VARCHAR(25) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '0.00' AFTER `aff_bonuses`;
{%end%}

{%begin%}
  INSERT INTO `cl_configs` (`id`, `title`, `name`, `value`, `regex`) VALUES (NULL, 'PayPal API Public key', 'paypal_api_key', '', ''), (NULL, 'PayPal API Secret key', 'paypal_api_pass', '', '');
{%end%}

{%begin%}
  INSERT INTO `cl_configs` (`id`, `title`, `name`, `value`, `regex`) VALUES (NULL, 'PayPal Payment Mode', 'paypal_mode', 'sandbox', '/^(sandbox|live)$/');
{%end%}

{%begin%}
  INSERT INTO `cl_configs` (`id`, `title`, `name`, `value`, `regex`) VALUES (NULL, 'Site currency', 'site_currency', 'usd', ' \r\n/^([a-zA-Z]){2,7}$/');
{%end%}

{%begin%}
  CREATE TABLE `cl_wallet_history` ( `id` INT(11) NOT NULL AUTO_INCREMENT ,  `user_id` INT(11) NOT NULL DEFAULT '0' ,  `operation` VARCHAR(60) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' ,  `amount` VARCHAR(25) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '0.00' ,  `json_data` VARCHAR(3000) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '[]' ,  `time` VARCHAR(25) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '0' ,    PRIMARY KEY  (`id`)) ENGINE = InnoDB CHARSET=utf8 COLLATE utf8_general_ci;
{%end%}

{%begin%}
  INSERT INTO `cl_configs` (`id`, `title`, `name`, `value`, `regex`) VALUES (NULL, 'Advertising system', 'advertising_system', 'on', '/^(on|off)$/');
{%end%}

{%begin%}
  INSERT INTO `cl_configs` (`id`, `title`, `name`, `value`, `regex`) VALUES (NULL, 'Ad conversion rate', 'ad_conversion_rate', '0.05', '/^([0-9]{1,3}\\.[0-9]{1,3}|[0-9]{1,3})$/');
{%end%}

{%begin%}
  ALTER TABLE `cl_publications` ADD `og_data` VARCHAR(3000) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' AFTER `target`;
{%end%}
