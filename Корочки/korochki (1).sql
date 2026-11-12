-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Хост: localhost
-- Время создания: Ноя 12 2026 г., 08:06
-- Версия сервера: 10.4.32-MariaDB
-- Версия PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `korochki`
--

-- --------------------------------------------------------

--
-- Структура таблицы `kursi`
--

CREATE TABLE `kursi` (
  `id_kursi` int(1) NOT NULL,
  `nazvanie_kursa` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Дамп данных таблицы `kursi`
--

INSERT INTO `kursi` (`id_kursi`, `nazvanie_kursa`) VALUES
(1, 'Основы алгоритмизации и программирования'),
(2, 'Основы веб-дизайна'),
(3, 'Основы проектирования баз данных');

-- --------------------------------------------------------

--
-- Структура таблицы `oplati`
--

CREATE TABLE `oplati` (
  `id_oplati` int(1) NOT NULL,
  `sposob_oplati` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Дамп данных таблицы `oplati`
--

INSERT INTO `oplati` (`id_oplati`, `sposob_oplati`) VALUES
(1, 'Наличные'),
(2, 'Перевод по номеру телефона');

-- --------------------------------------------------------

--
-- Структура таблицы `otzivi`
--

CREATE TABLE `otzivi` (
  `id_otziv` int(1) NOT NULL,
  `komentariy` varchar(255) DEFAULT NULL,
  `kolichestvo_zvezd` int(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Дамп данных таблицы `otzivi`
--

INSERT INTO `otzivi` (`id_otziv`, `komentariy`, `kolichestvo_zvezd`) VALUES
(1, 'Прошел курс «Основы алгоритмизации и программирования». Понравилось, что с первой недели заставляют думать именно как программист: строить блок-схемы перед написанием программы. Задачи приближены к реальным кейсам.', 5),
(2, 'Ну вроде крутяк, учат неплохо. К сожалению я даун и ничего не запомнил', 3);

-- --------------------------------------------------------

--
-- Структура таблицы `polzovateli`
--

CREATE TABLE `polzovateli` (
  `id_polzovateli` int(1) NOT NULL,
  `login` varchar(255) NOT NULL,
  `parol` varchar(255) NOT NULL,
  `fio` varchar(255) NOT NULL,
  `telefon` varchar(15) NOT NULL,
  `pochta` varchar(255) NOT NULL,
  `roli_id` int(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Дамп данных таблицы `polzovateli`
--

INSERT INTO `polzovateli` (`id_polzovateli`, `login`, `parol`, `fio`, `telefon`, `pochta`, `roli_id`) VALUES
(1, 'polzovatel001', '084b3e281f911131817fb32df08357ce', 'Сергеев Александр Сильвесторович', '8(980)788-55-26', 'polzov.koroknet@mail.ru', 1),
(2, 'Admin', '000411fda44dd428aced9d60cf228b77', 'Бусыгин Алексей Николаевич', '8(999)122-13-26', 'admin.koroknet@mail.ru', 2),
(3, 'polzovatel002', '23a5a6aa557ffbc6a7f0a9e823ddfcdd', 'Антилов Анатолий Рустамович', '8(967)067-67-67', 'cymka67@gmail.ru', 1),
(4, 'z1dipex', '25d55ad283aa400af464c76d713c07ad', 'Бубылдов Никита Сергеевич', '8(676)787-20-00', 'gujhdkiaw@mail.ru', 1),
(5, '676767', 'd483e733f36be959c8517f1f5fbec30f', 'бибу буба дабу', '8(890)907-89-78', 'ghudw@gmail.com', 1),
(6, 'testov1337', '56a9745ecf9a74312e9af447a7dc15f5', 'Тестов-Тевич Тест Тестович', '8(987)654-32-10', 'test@mail.ru', 1);

-- --------------------------------------------------------

--
-- Структура таблицы `roli`
--

CREATE TABLE `roli` (
  `id_roli` int(1) NOT NULL,
  `nazvanie_roli` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Дамп данных таблицы `roli`
--

INSERT INTO `roli` (`id_roli`, `nazvanie_roli`) VALUES
(1, 'Пользователь'),
(2, 'Администратор');

-- --------------------------------------------------------

--
-- Структура таблицы `statusi`
--

CREATE TABLE `statusi` (
  `id_statusi` int(1) NOT NULL,
  `nazvanie_status` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Дамп данных таблицы `statusi`
--

INSERT INTO `statusi` (`id_statusi`, `nazvanie_status`) VALUES
(1, 'Новая'),
(2, 'Идет обучение'),
(3, 'Обучение завершено');

-- --------------------------------------------------------

--
-- Структура таблицы `zayavki`
--

CREATE TABLE `zayavki` (
  `id_zayavki` int(1) NOT NULL,
  `kursi_id` int(1) DEFAULT NULL,
  `data_nach` varchar(10) DEFAULT NULL,
  `kommentariy` text DEFAULT NULL,
  `oplati_id` int(1) DEFAULT NULL,
  `statysi_id` int(1) DEFAULT NULL,
  `polzovateli_id` int(1) DEFAULT NULL,
  `otziv_id` int(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Дамп данных таблицы `zayavki`
--

INSERT INTO `zayavki` (`id_zayavki`, `kursi_id`, `data_nach`, `kommentariy`, `oplati_id`, `statysi_id`, `polzovateli_id`, `otziv_id`) VALUES
(1, 1, '11.09.2025', NULL, 2, 3, 2, 1),
(2, 3, '06.07.1967', NULL, 2, 2, 2, NULL),
(3, 1, '14.09.2025', NULL, 1, 3, 1, 2),
(4, 2, '06.07.1967', NULL, 1, 1, 4, NULL),
(5, 2, '2026-10-24', NULL, 2, 1, 5, NULL),
(6, 1, '2026-10-31', NULL, 1, 1, 5, NULL),
(7, 1, '2026-10-29', NULL, 1, 1, 5, NULL),
(8, 2, '2026-10-26', NULL, 2, 1, 5, NULL);

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `kursi`
--
ALTER TABLE `kursi`
  ADD PRIMARY KEY (`id_kursi`);

--
-- Индексы таблицы `oplati`
--
ALTER TABLE `oplati`
  ADD PRIMARY KEY (`id_oplati`);

--
-- Индексы таблицы `otzivi`
--
ALTER TABLE `otzivi`
  ADD PRIMARY KEY (`id_otziv`);

--
-- Индексы таблицы `polzovateli`
--
ALTER TABLE `polzovateli`
  ADD PRIMARY KEY (`id_polzovateli`),
  ADD KEY `roli_id` (`roli_id`);

--
-- Индексы таблицы `roli`
--
ALTER TABLE `roli`
  ADD PRIMARY KEY (`id_roli`);

--
-- Индексы таблицы `statusi`
--
ALTER TABLE `statusi`
  ADD PRIMARY KEY (`id_statusi`);

--
-- Индексы таблицы `zayavki`
--
ALTER TABLE `zayavki`
  ADD PRIMARY KEY (`id_zayavki`),
  ADD KEY `oplati_id` (`oplati_id`),
  ADD KEY `statysi_id` (`statysi_id`),
  ADD KEY `polzovateli_id` (`polzovateli_id`),
  ADD KEY `otziv_id` (`otziv_id`),
  ADD KEY `kursi_id` (`kursi_id`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `kursi`
--
ALTER TABLE `kursi`
  MODIFY `id_kursi` int(1) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT для таблицы `oplati`
--
ALTER TABLE `oplati`
  MODIFY `id_oplati` int(1) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT для таблицы `otzivi`
--
ALTER TABLE `otzivi`
  MODIFY `id_otziv` int(1) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT для таблицы `polzovateli`
--
ALTER TABLE `polzovateli`
  MODIFY `id_polzovateli` int(1) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT для таблицы `roli`
--
ALTER TABLE `roli`
  MODIFY `id_roli` int(1) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT для таблицы `statusi`
--
ALTER TABLE `statusi`
  MODIFY `id_statusi` int(1) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT для таблицы `zayavki`
--
ALTER TABLE `zayavki`
  MODIFY `id_zayavki` int(1) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Ограничения внешнего ключа сохраненных таблиц
--

--
-- Ограничения внешнего ключа таблицы `polzovateli`
--
ALTER TABLE `polzovateli`
  ADD CONSTRAINT `polzovateli_ibfk_1` FOREIGN KEY (`roli_id`) REFERENCES `roli` (`id_roli`);

--
-- Ограничения внешнего ключа таблицы `zayavki`
--
ALTER TABLE `zayavki`
  ADD CONSTRAINT `zayavki_ibfk_1` FOREIGN KEY (`kursi_id`) REFERENCES `kursi` (`id_kursi`),
  ADD CONSTRAINT `zayavki_ibfk_2` FOREIGN KEY (`otziv_id`) REFERENCES `otzivi` (`id_otziv`),
  ADD CONSTRAINT `zayavki_ibfk_3` FOREIGN KEY (`oplati_id`) REFERENCES `oplati` (`id_oplati`),
  ADD CONSTRAINT `zayavki_ibfk_4` FOREIGN KEY (`statysi_id`) REFERENCES `statusi` (`id_statusi`),
  ADD CONSTRAINT `zayavki_ibfk_5` FOREIGN KEY (`polzovateli_id`) REFERENCES `polzovateli` (`id_polzovateli`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
