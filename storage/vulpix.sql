-- phpMyAdmin SQL Dump
-- version 4.8.5
-- https://www.phpmyadmin.net/
--
-- Хост: 127.0.0.1:3306
-- Время создания: Апр 23 2020 г., 07:25
-- Версия сервера: 8.0.15
-- Версия PHP: 7.3.2

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `vulpix`
--

-- --------------------------------------------------------

--
-- Структура таблицы `permissions`
--

CREATE TABLE `permissions` (
  `id` int(10) UNSIGNED NOT NULL COMMENT 'id - записи',
  `permission_name` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT 'имя правила доступа',
  `permission_description` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT 'описание правила доступа'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Таблица с правилами доступа пользователей';

--
-- Дамп данных таблицы `permissions`
--

INSERT INTO `permissions` (`id`, `permission_name`, `permission_description`) VALUES
(1, 'patientCardAccess', 'Просмотр карты пациента'),
(2, 'patientCardEdit', 'Редактирование карты пациента');

-- --------------------------------------------------------

--
-- Структура таблицы `refresh_tokens`
--

CREATE TABLE `refresh_tokens` (
  `id` int(10) UNSIGNED NOT NULL COMMENT 'id - записи',
  `token` char(36) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT 'токен',
  `user_id` int(10) UNSIGNED NOT NULL COMMENT 'id - пользователя',
  `created` int(10) UNSIGNED NOT NULL COMMENT 'дата создания',
  `expires` int(10) UNSIGNED NOT NULL COMMENT 'дата окончания'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Таблица хранящая в себе refresh token''s';

--
-- Дамп данных таблицы `refresh_tokens`
--

INSERT INTO `refresh_tokens` (`id`, `token`, `user_id`, `created`, `expires`) VALUES
(4, '618b85c2-6e9f-4039-a274-7a68dc4d7601', 1, 1587378979, 1589970979);

-- --------------------------------------------------------

--
-- Структура таблицы `roles`
--

CREATE TABLE `roles` (
  `id` int(10) UNSIGNED NOT NULL COMMENT 'id - записи',
  `role_name` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT 'название роли',
  `role_description` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT 'описание роли'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Таблица с пользовательскими ролями';

--
-- Дамп данных таблицы `roles`
--

INSERT INTO `roles` (`id`, `role_name`, `role_description`) VALUES
(1, 'administrator', 'Роль администратора'),
(2, 'registrator', 'Роль регистратора');

-- --------------------------------------------------------

--
-- Структура таблицы `role_permission`
--

CREATE TABLE `role_permission` (
  `id` int(10) UNSIGNED NOT NULL COMMENT 'id - записи',
  `role_id` int(10) UNSIGNED NOT NULL COMMENT 'ссылка на роль',
  `permission_id` int(10) UNSIGNED NOT NULL COMMENT 'ссылка на правило доступа'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Таблица со связями ролей и правил доступа';

--
-- Дамп данных таблицы `role_permission`
--

INSERT INTO `role_permission` (`id`, `role_id`, `permission_id`) VALUES
(1, 1, 1),
(2, 1, 2),
(38, 2, 1),
(39, 2, 2);

-- --------------------------------------------------------

--
-- Структура таблицы `user_accounts`
--

CREATE TABLE `user_accounts` (
  `id` int(10) UNSIGNED NOT NULL COMMENT 'id - записи',
  `user_name` char(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT 'имя пользователя',
  `password_hash` char(60) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT 'hash пароля',
  `secret_key` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT 'секретный ключ'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Таблица с учетными записями пользователей системы';

--
-- Дамп данных таблицы `user_accounts`
--

INSERT INTO `user_accounts` (`id`, `user_name`, `password_hash`, `secret_key`) VALUES
(1, 'Mikki', '$2y$12$6fqLeiNwWGZcVBUIa2G3n.H56eDtuvhu44QABz0PvsPD/qtbfTxFm', 'e47f1df3803a2ea87c0c9857d571a05ceba034f7');

-- --------------------------------------------------------

--
-- Структура таблицы `user_role`
--

CREATE TABLE `user_role` (
  `id` int(10) UNSIGNED NOT NULL COMMENT 'id - записи',
  `user_id` int(10) UNSIGNED NOT NULL COMMENT 'ссылка на пользователя',
  `role_id` int(10) UNSIGNED NOT NULL COMMENT 'ссылка на роль'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Таблица со связями ролей и пользователей';

--
-- Дамп данных таблицы `user_role`
--

INSERT INTO `user_role` (`id`, `user_id`, `role_id`) VALUES
(1, 1, 1);

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `refresh_tokens`
--
ALTER TABLE `refresh_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `token` (`token`),
  ADD KEY `user_id` (`user_id`);

--
-- Индексы таблицы `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `role_name` (`role_name`);

--
-- Индексы таблицы `role_permission`
--
ALTER TABLE `role_permission`
  ADD PRIMARY KEY (`id`),
  ADD KEY `permission` (`permission_id`),
  ADD KEY `role` (`role_id`);

--
-- Индексы таблицы `user_accounts`
--
ALTER TABLE `user_accounts`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `user_role`
--
ALTER TABLE `user_role`
  ADD PRIMARY KEY (`id`),
  ADD KEY `role_id` (`role_id`),
  ADD KEY `user_id` (`user_id`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'id - записи', AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT для таблицы `refresh_tokens`
--
ALTER TABLE `refresh_tokens`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'id - записи', AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT для таблицы `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'id - записи', AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT для таблицы `role_permission`
--
ALTER TABLE `role_permission`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'id - записи', AUTO_INCREMENT=40;

--
-- AUTO_INCREMENT для таблицы `user_accounts`
--
ALTER TABLE `user_accounts`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'id - записи', AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT для таблицы `user_role`
--
ALTER TABLE `user_role`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'id - записи', AUTO_INCREMENT=2;

--
-- Ограничения внешнего ключа сохраненных таблиц
--

--
-- Ограничения внешнего ключа таблицы `refresh_tokens`
--
ALTER TABLE `refresh_tokens`
  ADD CONSTRAINT `refresh_tokens_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user_accounts` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ограничения внешнего ключа таблицы `role_permission`
--
ALTER TABLE `role_permission`
  ADD CONSTRAINT `role_permission_ibfk_1` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `role_permission_ibfk_2` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ограничения внешнего ключа таблицы `user_role`
--
ALTER TABLE `user_role`
  ADD CONSTRAINT `user_role_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `user_role_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `user_accounts` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
