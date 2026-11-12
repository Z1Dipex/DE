-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Хост: localhost
-- Время создания: Ноя 12 2026 г., 08:29
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
-- База данных: `dogprak`
--

-- --------------------------------------------------------

--
-- Структура таблицы `organizacii`
--

CREATE TABLE `organizacii` (
  `inn` varchar(12) NOT NULL,
  `nazvanie_organizacii` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Дамп данных таблицы `organizacii`
--

INSERT INTO `organizacii` (`inn`, `nazvanie_organizacii`) VALUES
('5034567890', 'ООО «Люберецкий завод»'),
('7712345678', 'ООО «ТехноПортал»'),
('7723456789', 'АО «Россети»'),
('7734567890', 'ПАО «Сбербанк»');

-- --------------------------------------------------------

--
-- Структура таблицы `otzivi`
--

CREATE TABLE `otzivi` (
  `id_otziv` int(1) NOT NULL,
  `komentariy` text DEFAULT NULL,
  `kolichestvo_zvezd` int(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Дамп данных таблицы `otzivi`
--

INSERT INTO `otzivi` (`id_otziv`, `komentariy`, `kolichestvo_zvezd`) VALUES
(1, 'Проходил практику в ООО «ТехноПортал». Организация отличная: наставник помогал, задачи давали по специальности. Узнал много нового по backend-разработке.', 5),
(2, '2', 2);

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
(1, 'Admin', '000411fda44dd428aced9d60cf228b77', 'Администратов Админ Администраторович', '8(999)122-13-26', 'admin.koroknet@mail.ru', 2),
(2, 'student001', '824682b7c01d5c745bb87d6b1b4308c1', 'Студентов Студент Студентович', '8(980)788-55-26', 'polzov.koroknet@mail.ru', 1),
(3, 'student002', '5addd024b657cbf17ca9a3bcbb437484', 'Иванова Мария Петровна', '8(915)334-77-19', 'ivanova.m@mail.ru', 1);

-- --------------------------------------------------------

--
-- Структура таблицы `roli`
--

CREATE TABLE `roli` (
  `id_roli` int(1) NOT NULL,
  `nazvanie_roli` varchar(15) DEFAULT NULL
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
  `status_name` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Дамп данных таблицы `statusi`
--

INSERT INTO `statusi` (`id_statusi`, `status_name`) VALUES
(1, 'Заявка'),
(2, 'Договор заключён'),
(3, 'Практика завершена'),
(4, 'Расторгнут');

-- --------------------------------------------------------

--
-- Структура таблицы `tipi_praktiki`
--

CREATE TABLE `tipi_praktiki` (
  `id_tip` int(1) NOT NULL,
  `nazvanie_tipa` varchar(16) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Дамп данных таблицы `tipi_praktiki`
--

INSERT INTO `tipi_praktiki` (`id_tip`, `nazvanie_tipa`) VALUES
(1, 'Учебная'),
(2, 'Производственная'),
(3, 'Преддипломная');

-- --------------------------------------------------------

--
-- Структура таблицы `zayavki`
--

CREATE TABLE `zayavki` (
  `id_zayavki` int(1) NOT NULL,
  `inn` varchar(12) NOT NULL,
  `period_nachalo` date NOT NULL,
  `period_konec` date NOT NULL,
  `tip_id` int(1) NOT NULL,
  `dolzhnost` varchar(255) NOT NULL,
  `statysi_id` int(1) NOT NULL,
  `hodataistvo` text NOT NULL,
  `polzovateli_id` int(1) NOT NULL,
  `otziv_id` int(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Дамп данных таблицы `zayavki`
--

INSERT INTO `zayavki` (`id_zayavki`, `inn`, `period_nachalo`, `period_konec`, `tip_id`, `dolzhnost`, `statysi_id`, `hodataistvo`, `polzovateli_id`, `otziv_id`) VALUES
(1, '7712345678', '2026-02-01', '2026-03-01', 1, 'Программист', 3, '/uploads/hodataystva/zayavka_1.pdf', 2, 1),
(2, '7723456789', '2026-03-15', '2026-04-15', 2, 'Аналитик', 1, '/uploads/hodataystva/zayavka_2.jpg', 3, NULL),
(3, '5034567890', '2026-10-01', '2026-11-01', 2, 'Фрезер-стажор', 3, '/uploads/hodataystva/hod_6adc7a7f1c17a.jpg', 2, 2),
(4, '7723456789', '2026-10-02', '2026-11-02', 1, 'Инженер', 2, '/uploads/hodataystva/hod_6adc7d13bd53c.jpg', 2, NULL),
(5, '7723456789', '2026-10-10', '2026-11-07', 2, 'Фрезер-стажор', 1, '/uploads/hodataystva/hod_6adc83aaccee6.jpg', 2, NULL);

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `organizacii`
--
ALTER TABLE `organizacii`
  ADD PRIMARY KEY (`inn`);

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
  ADD UNIQUE KEY `login` (`login`),
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
-- Индексы таблицы `tipi_praktiki`
--
ALTER TABLE `tipi_praktiki`
  ADD PRIMARY KEY (`id_tip`);

--
-- Индексы таблицы `zayavki`
--
ALTER TABLE `zayavki`
  ADD PRIMARY KEY (`id_zayavki`),
  ADD KEY `polzovateli_id` (`polzovateli_id`),
  ADD KEY `otziv_id` (`otziv_id`),
  ADD KEY `statysi_id` (`statysi_id`),
  ADD KEY `tip_id` (`tip_id`),
  ADD KEY `inn` (`inn`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `otzivi`
--
ALTER TABLE `otzivi`
  MODIFY `id_otziv` int(1) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT для таблицы `polzovateli`
--
ALTER TABLE `polzovateli`
  MODIFY `id_polzovateli` int(1) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT для таблицы `roli`
--
ALTER TABLE `roli`
  MODIFY `id_roli` int(1) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT для таблицы `statusi`
--
ALTER TABLE `statusi`
  MODIFY `id_statusi` int(1) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT для таблицы `tipi_praktiki`
--
ALTER TABLE `tipi_praktiki`
  MODIFY `id_tip` int(1) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT для таблицы `zayavki`
--
ALTER TABLE `zayavki`
  MODIFY `id_zayavki` int(1) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

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
  ADD CONSTRAINT `zayavki_ibfk_1` FOREIGN KEY (`polzovateli_id`) REFERENCES `polzovateli` (`id_polzovateli`),
  ADD CONSTRAINT `zayavki_ibfk_2` FOREIGN KEY (`tip_id`) REFERENCES `tipi_praktiki` (`id_tip`),
  ADD CONSTRAINT `zayavki_ibfk_3` FOREIGN KEY (`otziv_id`) REFERENCES `otzivi` (`id_otziv`),
  ADD CONSTRAINT `zayavki_ibfk_4` FOREIGN KEY (`statysi_id`) REFERENCES `statusi` (`id_statusi`),
  ADD CONSTRAINT `zayavki_ibfk_5` FOREIGN KEY (`inn`) REFERENCES `organizacii` (`inn`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
