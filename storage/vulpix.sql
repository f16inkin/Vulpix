-- phpMyAdmin SQL Dump
-- version 5.0.2
-- https://www.phpmyadmin.net/
--
-- Хост: 127.0.0.1:3306
-- Время создания: Май 17 2020 г., 13:21
-- Версия сервера: 8.0.19
-- Версия PHP: 7.3.17

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
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
  `id` int UNSIGNED NOT NULL COMMENT 'id - записи',
  `permission_name` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT 'имя правила доступа',
  `permission_description` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT 'описание правила доступа',
  `permission_group` int UNSIGNED NOT NULL DEFAULT '1' COMMENT 'id - группы привелегий'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Таблица с правилами доступа пользователей';

--
-- Дамп данных таблицы `permissions`
--

INSERT INTO `permissions` (`id`, `permission_name`, `permission_description`, `permission_group`) VALUES
(1, 'RBAC_ROLE_CREATE', 'Создание роли', 2),
(2, 'RBAC_ROLE_EDIT', 'Редактирование роли', 2),
(3, 'RBAC_ROLE_DELETE', 'Удаление роли', 2),
(4, 'RBAC_ROLE_GET', 'Получить данные о роли', 2),
(5, 'RBAC_ROLES_GET_ALL', 'Получить список всех ролей', 2),
(6, 'PERMISSIONS_ADD', 'Добавить привелегии', 2),
(7, 'PERMISSIONS_DELETE', 'Удалить привелегии', 2),
(8, 'PERMISSIONS_GET_ALL', 'Получить список всех привелегий', 2),
(9, 'PERMISSIONS_GET_DIFFERENT', 'Получить список отсутсвующих у роли привелегий', 2),
(10, 'AAIS_ACCOUNT_CREATE', 'Создание учетной записи', 3),
(11, 'AAIS_ACCOUNT_EDIT', 'Редактирование учетной записи', 3),
(12, 'AAIS_ACCOUNT_DELETE', 'Удаление учетной записи', 3),
(13, 'AAIS_ACCOUNT_GET', 'Получить информацию по учетной записи', 3),
(14, 'AAIS_ACCOUNTS_GET', 'Получить список учетных записей', 3),
(15, 'AAIS_ACCOUNT_PASSWORD_CHANGE', 'Сменить пароль', 3),
(16, 'AAIS_ACCOUNT_PASSWORD_RESET', 'Сбросить пароль', 3);

-- --------------------------------------------------------

--
-- Структура таблицы `permission_groups`
--

CREATE TABLE `permission_groups` (
  `id` int UNSIGNED NOT NULL COMMENT 'id - записи',
  `group_name` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT 'название группы',
  `group_description` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT 'описание группы'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Группы для разрешений. Удобство в сортировке и группировке.';

--
-- Дамп данных таблицы `permission_groups`
--

INSERT INTO `permission_groups` (`id`, `group_name`, `group_description`) VALUES
(1, 'common', 'Общая группа'),
(2, 'rbac', 'Роли и привилегии'),
(3, 'aais', 'Аккаунты, аутентификация, пользователи');

-- --------------------------------------------------------

--
-- Структура таблицы `refresh_tokens`
--

CREATE TABLE `refresh_tokens` (
  `id` int UNSIGNED NOT NULL COMMENT 'id - записи',
  `token` char(36) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT 'токен',
  `user_id` int UNSIGNED NOT NULL COMMENT 'id - пользователя',
  `created` int UNSIGNED NOT NULL COMMENT 'дата создания',
  `expires` int UNSIGNED NOT NULL COMMENT 'дата окончания'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Таблица хранящая в себе refresh token''s';

--
-- Дамп данных таблицы `refresh_tokens`
--

INSERT INTO `refresh_tokens` (`id`, `token`, `user_id`, `created`, `expires`) VALUES
(122, '939ecc43-0022-4349-a796-a52e173dfeaa', 3, 1589635864, 1592227864),
(126, '348ab931-8d74-43a6-960c-4003ae7c7f59', 1, 1589700016, 1592292016);

-- --------------------------------------------------------

--
-- Структура таблицы `roles`
--

CREATE TABLE `roles` (
  `id` int UNSIGNED NOT NULL COMMENT 'id - записи',
  `role_name` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT 'название роли',
  `role_description` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT 'описание роли'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Таблица с пользовательскими ролями';

--
-- Дамп данных таблицы `roles`
--

INSERT INTO `roles` (`id`, `role_name`, `role_description`) VALUES
(1, 'administrator', 'Администратор '),
(2, 'registrator', 'Роль регистратора'),
(13, 'user3', 'Рандомный пользователь');

-- --------------------------------------------------------

--
-- Структура таблицы `role_permission`
--

CREATE TABLE `role_permission` (
  `id` int UNSIGNED NOT NULL COMMENT 'id - записи',
  `role_id` int UNSIGNED NOT NULL COMMENT 'ссылка на роль',
  `permission_id` int UNSIGNED NOT NULL COMMENT 'ссылка на правило доступа'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Таблица со связями ролей и правил доступа';

--
-- Дамп данных таблицы `role_permission`
--

INSERT INTO `role_permission` (`id`, `role_id`, `permission_id`) VALUES
(1, 1, 1),
(2, 1, 2),
(3, 1, 3),
(4, 1, 4),
(5, 1, 5),
(6, 1, 6),
(7, 1, 7),
(8, 1, 8),
(9, 1, 9),
(10, 1, 10),
(11, 1, 11),
(12, 1, 12),
(13, 1, 13),
(14, 1, 14),
(15, 1, 15),
(16, 1, 16);

-- --------------------------------------------------------

--
-- Структура таблицы `user_accounts`
--

CREATE TABLE `user_accounts` (
  `id` int UNSIGNED NOT NULL COMMENT 'id - записи',
  `user_name` char(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT 'имя пользователя',
  `password_hash` char(96) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT 'hash пароля'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Таблица с учетными записями пользователей системы';

--
-- Дамп данных таблицы `user_accounts`
--

INSERT INTO `user_accounts` (`id`, `user_name`, `password_hash`) VALUES
(1, 'Mikki', '$argon2i$v=19$m=65536,t=4,p=1$eUg2LzJLRnBCWTRCVHhuTQ$j8acXNikLkhvDgDW7awZ5lo5f4djCcmWpAE7QG9D9sw'),
(2, 'Rain', '$argon2i$v=19$m=65536,t=4,p=1$L2hTRFcubFQ1TEFFN2JaZw$ayERyMt+Vfmg4po8Cg7aqwH2xCarK4Ho98p25TwQsSE'),
(3, 'Finking', '$argon2i$v=19$m=65536,t=4,p=1$aWJPR3hvbm5oMFF2bkd3Yg$LAslvkrvAR0TN15jq8aspHgc4Mctn58iqK6s399QLwA'),
(8, 'User', '$argon2i$v=19$m=65536,t=4,p=1$ckw5R21xR1I0SjNILml4NQ$cmqvv/W05j5CkFKw96AEemN54GuKL6UvCasdqV27wcs');

-- --------------------------------------------------------

--
-- Структура таблицы `user_role`
--

CREATE TABLE `user_role` (
  `id` int UNSIGNED NOT NULL COMMENT 'id - записи',
  `user_id` int UNSIGNED NOT NULL COMMENT 'ссылка на пользователя',
  `role_id` int UNSIGNED NOT NULL COMMENT 'ссылка на роль'
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
  ADD PRIMARY KEY (`id`),
  ADD KEY `permission_group` (`permission_group`);

--
-- Индексы таблицы `permission_groups`
--
ALTER TABLE `permission_groups`
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
  ADD PRIMARY KEY (`id`);

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
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'id - записи', AUTO_INCREMENT=123;

--
-- AUTO_INCREMENT для таблицы `permission_groups`
--
ALTER TABLE `permission_groups`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'id - записи', AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT для таблицы `refresh_tokens`
--
ALTER TABLE `refresh_tokens`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'id - записи', AUTO_INCREMENT=127;

--
-- AUTO_INCREMENT для таблицы `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'id - записи', AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT для таблицы `role_permission`
--
ALTER TABLE `role_permission`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'id - записи', AUTO_INCREMENT=42;

--
-- AUTO_INCREMENT для таблицы `user_accounts`
--
ALTER TABLE `user_accounts`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'id - записи', AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT для таблицы `user_role`
--
ALTER TABLE `user_role`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'id - записи', AUTO_INCREMENT=2;

--
-- Ограничения внешнего ключа сохраненных таблиц
--

--
-- Ограничения внешнего ключа таблицы `permissions`
--
ALTER TABLE `permissions`
  ADD CONSTRAINT `permissions_ibfk_1` FOREIGN KEY (`permission_group`) REFERENCES `permission_groups` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

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
