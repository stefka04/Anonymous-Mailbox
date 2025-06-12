-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Хост: 127.0.0.1
-- Време на генериране: 12 юни 2025 в 21:42
-- Версия на сървъра: 10.4.32-MariaDB
-- Версия на PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данни: `secretunitine`
--

-- --------------------------------------------------------

--
-- Структура на таблица `messages`
--

CREATE TABLE `messages` (
  `id` int(11) NOT NULL,
  `senderId` int(11) NOT NULL,
  `sentAt` datetime NOT NULL DEFAULT current_timestamp(),
  `topic` varchar(1024) DEFAULT NULL,
  `content` text DEFAULT NULL,
  `chainNumber` int(11) DEFAULT NULL,
  `isAnonymous` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Схема на данните от таблица `messages`
--

INSERT INTO `messages` (`id`, `senderId`, `sentAt`, `topic`, `content`, `chainNumber`, `isAnonymous`) VALUES
(1, 1, '2025-06-12 21:40:45', 'From me to me', 'Hello!', 0, 0);

-- --------------------------------------------------------

--
-- Структура на таблица `message_folders`
--

CREATE TABLE `message_folders` (
  `id` int(11) NOT NULL,
  `folderName` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Схема на данните от таблица `message_folders`
--

INSERT INTO `message_folders` (`id`, `folderName`) VALUES
(1, 'Inbox'),
(2, 'SentMessages'),
(3, 'Deleted');

-- --------------------------------------------------------

--
-- Структура на таблица `message_note`
--

CREATE TABLE `message_note` (
  `messageId` int(11) NOT NULL,
  `noteId` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Структура на таблица `message_recipients`
--

CREATE TABLE `message_recipients` (
  `messageId` int(11) NOT NULL,
  `recipientId` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Схема на данните от таблица `message_recipients`
--

INSERT INTO `message_recipients` (`messageId`, `recipientId`) VALUES
(1, 1);

-- --------------------------------------------------------

--
-- Структура на таблица `notes`
--

CREATE TABLE `notes` (
  `id` int(11) NOT NULL,
  `content` text NOT NULL,
  `posX` int(11) NOT NULL,
  `posY` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Структура на таблица `roles`
--

CREATE TABLE `roles` (
  `id` int(11) NOT NULL,
  `type` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Структура на таблица `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `fn` varchar(10) DEFAULT NULL,
  `email` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `username` varchar(20) NOT NULL,
  `name` varchar(10) NOT NULL,
  `surname` varchar(10) NOT NULL,
  `role` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Схема на данните от таблица `users`
--

INSERT INTO `users` (`id`, `fn`, `email`, `password`, `username`, `name`, `surname`, `role`) VALUES
(1, '1MI0600100', 'vankata@gmail.com', '$2y$10$pE1pXay.x1tpMYbyMJNZDuhr0bVmClyqvWxEQFRZ7viH0xXHms9JC', 'ivan123', 'ivan', 'Ivanov', 'student');

-- --------------------------------------------------------

--
-- Структура на таблица `user_messages_status`
--

CREATE TABLE `user_messages_status` (
  `messageId` int(11) NOT NULL,
  `userId` int(11) NOT NULL,
  `messageFolderId` int(11) NOT NULL,
  `isRead` tinyint(1) NOT NULL DEFAULT 0,
  `isStarred` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Схема на данните от таблица `user_messages_status`
--

INSERT INTO `user_messages_status` (`messageId`, `userId`, `messageFolderId`, `isRead`, `isStarred`) VALUES
(1, 1, 1, 0, 0),
(1, 1, 2, 0, 0);

--
-- Indexes for dumped tables
--

--
-- Индекси за таблица `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `senderId_FK` (`senderId`);

--
-- Индекси за таблица `message_folders`
--
ALTER TABLE `message_folders`
  ADD PRIMARY KEY (`id`);

--
-- Индекси за таблица `message_note`
--
ALTER TABLE `message_note`
  ADD PRIMARY KEY (`messageId`,`noteId`),
  ADD KEY `noteId` (`noteId`);

--
-- Индекси за таблица `message_recipients`
--
ALTER TABLE `message_recipients`
  ADD PRIMARY KEY (`messageId`,`recipientId`),
  ADD KEY `recipientId_FK` (`recipientId`);

--
-- Индекси за таблица `notes`
--
ALTER TABLE `notes`
  ADD PRIMARY KEY (`id`);

--
-- Индекси за таблица `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`);

--
-- Индекси за таблица `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Индекси за таблица `user_messages_status`
--
ALTER TABLE `user_messages_status`
  ADD PRIMARY KEY (`messageId`,`userId`,`messageFolderId`),
  ADD KEY `messageFolderId_FK` (`messageFolderId`),
  ADD KEY `userId_FK` (`userId`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `message_folders`
--
ALTER TABLE `message_folders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `notes`
--
ALTER TABLE `notes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `user_messages_status`
--
ALTER TABLE `user_messages_status`
  MODIFY `messageId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Ограничения за дъмпнати таблици
--

--
-- Ограничения за таблица `messages`
--
ALTER TABLE `messages`
  ADD CONSTRAINT `senderId_FK` FOREIGN KEY (`senderId`) REFERENCES `users` (`id`);

--
-- Ограничения за таблица `message_note`
--
ALTER TABLE `message_note`
  ADD CONSTRAINT `messageId` FOREIGN KEY (`messageId`) REFERENCES `messages` (`id`),
  ADD CONSTRAINT `noteId` FOREIGN KEY (`noteId`) REFERENCES `notes` (`id`);

--
-- Ограничения за таблица `message_recipients`
--
ALTER TABLE `message_recipients`
  ADD CONSTRAINT `receivedMessageId_FK` FOREIGN KEY (`messageId`) REFERENCES `messages` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `recipientId_FK` FOREIGN KEY (`recipientId`) REFERENCES `users` (`id`);

--
-- Ограничения за таблица `user_messages_status`
--
ALTER TABLE `user_messages_status`
  ADD CONSTRAINT `messageFolderId_FK` FOREIGN KEY (`messageFolderId`) REFERENCES `message_folders` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `messageId_FK` FOREIGN KEY (`messageId`) REFERENCES `messages` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `userId_FK` FOREIGN KEY (`userId`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
