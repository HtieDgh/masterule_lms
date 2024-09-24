-- phpMyAdmin SQL Dump
-- version 5.1.0
-- https://www.phpmyadmin.net/
--
-- Хост: 127.0.0.1
-- Время создания: Май 31 2021 г., 10:00
-- Версия сервера: 10.4.18-MariaDB
-- Версия PHP: 8.0.3

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `trzbd_practicum`
--

-- --------------------------------------------------------

--
-- Структура таблицы `comments`
--

CREATE TABLE `comments` (
  `id` int(5) NOT NULL,
  `note_id` int(5) UNSIGNED NOT NULL,
  `author_id` int(5) UNSIGNED NOT NULL,
  `created` date NOT NULL,
  `comment` varchar(256) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `comments`
--

INSERT INTO `comments` (`id`, `note_id`, `author_id`, `created`, `comment`) VALUES
(5, 2, 3, '2021-05-10', 'тест');

-- --------------------------------------------------------

--
-- Структура таблицы `courses`
--

CREATE TABLE `courses` (
  `id` int(10) UNSIGNED NOT NULL,
  `author_id` int(10) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `article` varchar(511) DEFAULT NULL,
  `ava` varchar(511) DEFAULT 'img/course_avas/default_ava.png',
  `private` tinyint(1) NOT NULL DEFAULT 0,
  `created` date NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `courses`
--

INSERT INTO `courses` (`id`, `author_id`, `title`, `article`, `ava`, `private`, `created`) VALUES
(1, 3, 'Аппликация', 'В ходе этого курса мы научимся делать аппликации', 'img/course_avas/default_ava.png', 1, '2021-05-06'),
(4, 3, 'Поделки из ниток', 'В данном курсе мы научимся создавать поделки из ниток', 'img/course_avas/default_ava.png', 0, '2021-05-14');

-- --------------------------------------------------------

--
-- Структура таблицы `course_subs`
--

CREATE TABLE `course_subs` (
  `sub_id` int(10) UNSIGNED NOT NULL,
  `course_id` int(10) UNSIGNED NOT NULL,
  `confirmed` tinyint(1) NOT NULL DEFAULT 0,
  `created` date NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `course_subs`
--

INSERT INTO `course_subs` (`sub_id`, `course_id`, `confirmed`, `created`) VALUES
(1, 1, 0, '2021-05-28'),
(2, 1, 0, '2021-05-06'),
(4, 1, 1, '0000-00-00');

-- --------------------------------------------------------

--
-- Структура таблицы `notes`
--

CREATE TABLE `notes` (
  `id` int(10) UNSIGNED NOT NULL,
  `author_id` int(10) UNSIGNED NOT NULL,
  `course_id` int(10) UNSIGNED DEFAULT NULL,
  `created` date DEFAULT NULL,
  `views` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `title` varchar(50) DEFAULT NULL,
  `article` varchar(16000) DEFAULT NULL,
  `tags` varchar(100) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `notes`
--

INSERT INTO `notes` (`id`, `author_id`, `course_id`, `created`, `views`, `title`, `article`, `tags`) VALUES
(1, 3, 1, '2021-05-06', 1, 'Первая заметка нового автора', 'Тест создания заметка', 'Первая'),
(2, 3, 1, '2021-05-10', 1, 'Картиночки', 'Тест добавления нового урока в курс<br><img src=\"img/uploaded_photos/fad58de7366495db4650cfefac2fcd61/backgrnd2.jpg\" alt=\"\">', 'тест'),
(3, 3, NULL, '0000-00-00', 11, 'Третья заметка', 'Тест вывода заметок без курса', 'тест');

-- --------------------------------------------------------

--
-- Структура таблицы `rasp`
--

CREATE TABLE `rasp` (
  `id` int(10) UNSIGNED NOT NULL,
  `author_id` int(10) UNSIGNED NOT NULL DEFAULT 2,
  `created` date DEFAULT NULL,
  `title` varchar(50) DEFAULT NULL,
  `article` varchar(256) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `subs`
--

CREATE TABLE `subs` (
  `sub_id` int(5) UNSIGNED NOT NULL,
  `author_id` int(5) UNSIGNED NOT NULL,
  `created` date NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `subs`
--

INSERT INTO `subs` (`sub_id`, `author_id`, `created`) VALUES
(1, 3, '2021-05-06');

-- --------------------------------------------------------

--
-- Структура таблицы `s_a`
--

CREATE TABLE `s_a` (
  `id` int(10) UNSIGNED NOT NULL,
  `login` varchar(50) DEFAULT NULL,
  `pass` char(32) NOT NULL,
  `access` tinyint(3) UNSIGNED NOT NULL,
  `name` varchar(24) DEFAULT NULL,
  `status` varchar(500) DEFAULT '',
  `ava` varchar(200) NOT NULL DEFAULT 'img/user_avas/default_ava.png',
  `created` date DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `s_a`
--

INSERT INTO `s_a` (`id`, `login`, `pass`, `access`, `name`, `status`, `ava`, `created`) VALUES
(1, 'test1', '098f6bcd4621d373cade4e832627b4f6', 1, 'Ванюша', '', 'img/user_avas/default_ava.png', '2021-05-06'),
(2, 'test2', '098f6bcd4621d373cade4e832627b4f6', 1, 'test2', '', 'img/user_avas/default_ava.png', '2021-05-06'),
(3, 'main', '098f6bcd4621d373cade4e832627b4f6', 2, 'Юля', '', 'img/user_avas/u_id_3_20210514104518.png', '2021-05-06'),
(4, 'test3', '098f6bcd4621d373cade4e832627b4f6', 3, 'ПКЦ', 'Менеджер ПКЦ', 'img/user_avas/u_id_4_20210512112940.png', '0000-00-00');

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `author` (`author_id`),
  ADD KEY `notes_comments` (`note_id`);

--
-- Индексы таблицы `courses`
--
ALTER TABLE `courses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `author_id` (`author_id`);

--
-- Индексы таблицы `course_subs`
--
ALTER TABLE `course_subs`
  ADD PRIMARY KEY (`sub_id`,`course_id`),
  ADD KEY `sub_id` (`sub_id`),
  ADD KEY `course_id` (`course_id`);

--
-- Индексы таблицы `notes`
--
ALTER TABLE `notes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `aut_id` (`author_id`),
  ADD KEY `course_id` (`course_id`);

--
-- Индексы таблицы `rasp`
--
ALTER TABLE `rasp`
  ADD PRIMARY KEY (`id`),
  ADD KEY `a_id` (`author_id`);

--
-- Индексы таблицы `subs`
--
ALTER TABLE `subs`
  ADD PRIMARY KEY (`sub_id`,`author_id`),
  ADD KEY `sub_id` (`sub_id`),
  ADD KEY `author_id` (`author_id`);

--
-- Индексы таблицы `s_a`
--
ALTER TABLE `s_a`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `login` (`login`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `comments`
--
ALTER TABLE `comments`
  MODIFY `id` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT для таблицы `courses`
--
ALTER TABLE `courses`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT для таблицы `notes`
--
ALTER TABLE `notes`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT для таблицы `rasp`
--
ALTER TABLE `rasp`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT для таблицы `s_a`
--
ALTER TABLE `s_a`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Ограничения внешнего ключа сохраненных таблиц
--

--
-- Ограничения внешнего ключа таблицы `comments`
--
ALTER TABLE `comments`
  ADD CONSTRAINT `notes_comments` FOREIGN KEY (`note_id`) REFERENCES `notes` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `s_a_comments` FOREIGN KEY (`author_id`) REFERENCES `s_a` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ограничения внешнего ключа таблицы `courses`
--
ALTER TABLE `courses`
  ADD CONSTRAINT `s_a_course` FOREIGN KEY (`author_id`) REFERENCES `s_a` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ограничения внешнего ключа таблицы `course_subs`
--
ALTER TABLE `course_subs`
  ADD CONSTRAINT `course_c_suns` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `s_a_c_subs` FOREIGN KEY (`sub_id`) REFERENCES `s_a` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ограничения внешнего ключа таблицы `notes`
--
ALTER TABLE `notes`
  ADD CONSTRAINT `course_notes` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `s_a_notes` FOREIGN KEY (`author_id`) REFERENCES `s_a` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE;

--
-- Ограничения внешнего ключа таблицы `rasp`
--
ALTER TABLE `rasp`
  ADD CONSTRAINT `s_a_rasp` FOREIGN KEY (`author_id`) REFERENCES `s_a` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ограничения внешнего ключа таблицы `subs`
--
ALTER TABLE `subs`
  ADD CONSTRAINT `s_a_subs_author` FOREIGN KEY (`author_id`) REFERENCES `s_a` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `s_a_subs_sub` FOREIGN KEY (`sub_id`) REFERENCES `s_a` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
