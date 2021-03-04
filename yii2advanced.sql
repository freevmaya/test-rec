-- phpMyAdmin SQL Dump
-- version 5.0.4
-- https://www.phpmyadmin.net/
--
-- Хост: 127.0.0.1:3306
-- Время создания: Мар 02 2021 г., 18:13
-- Версия сервера: 10.4.12-MariaDB
-- Версия PHP: 7.1.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `yii2advanced`
--

-- --------------------------------------------------------

--
-- Структура таблицы `ingredients`
--

CREATE TABLE `ingredients` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `author_id` bigint(20) UNSIGNED NOT NULL,
  `unit_id` int(10) UNSIGNED DEFAULT 5,
  `name` char(128) NOT NULL,
  `active` tinyint(1) UNSIGNED NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `ingredients`
--

INSERT INTO `ingredients` (`id`, `author_id`, `unit_id`, `name`, `active`) VALUES
(2, 2, 4, 'Яйца', 0),
(3, 2, 5, 'Сахар', 0),
(4, 2, 5, 'Соль', 0),
(5, 2, 1, 'Вода', 0),
(6, 2, 5, 'Мука', 0),
(7, 2, 4, 'Чеснок', 0),
(9, 2, 5, 'Рис', 0),
(10, 2, 4, 'Баклажан', 0),
(11, 2, 5, 'Помидоры', 0),
(12, 2, 5, 'Дыня', 0),
(14, 2, 5, 'Петрушка', 0),
(15, 2, 5, 'Салат', 0),
(16, 2, 5, 'Сметана', 0),
(19, 2, 1, 'Молоко', 0),
(20, 2, 1, 'Сливки', 0),
(21, 2, 5, 'Капуста', 0),
(22, 2, 5, 'Морковка', 0),
(23, 2, 8, 'Лук репчатый', 0),
(29, 2, 4, 'Куриные окорочка', 0),
(30, 2, 5, 'Куриная грудинка', 0),
(31, 2, 4, 'Кетчуп шашлычный', 0);

-- --------------------------------------------------------

--
-- Структура таблицы `ingredients_to_recipe`
--

CREATE TABLE `ingredients_to_recipe` (
  `recipe_id` bigint(20) UNSIGNED NOT NULL,
  `ingredient_id` bigint(20) UNSIGNED NOT NULL,
  `unit_id` int(10) UNSIGNED DEFAULT NULL,
  `value` float NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `ingredients_to_recipe`
--

INSERT INTO `ingredients_to_recipe` (`recipe_id`, `ingredient_id`, `unit_id`, `value`) VALUES
(28, 10, 5, 5),
(28, 14, 5, 25),
(28, 15, 5, 24),
(28, 16, 6, 2),
(28, 19, 6, 40),
(28, 20, 6, 100),
(28, 21, 4, 1),
(28, 22, 5, 20),
(28, 23, 5, 30),
(32, 29, 4, 1),
(32, 30, 5, 200),
(32, 31, 4, 1);

-- --------------------------------------------------------

--
-- Структура таблицы `languages`
--

CREATE TABLE `languages` (
  `id` char(2) NOT NULL,
  `name` char(32) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `languages`
--

INSERT INTO `languages` (`id`, `name`) VALUES
('ru', 'Русский');

-- --------------------------------------------------------

--
-- Структура таблицы `migration`
--

CREATE TABLE `migration` (
  `version` varchar(180) NOT NULL,
  `apply_time` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `migration`
--

INSERT INTO `migration` (`version`, `apply_time`) VALUES
('m000000_000000_base', 1613054789),
('m130524_201442_init', 1613054799),
('m190124_110200_add_verification_token_column_to_user_table', 1613054799);

-- --------------------------------------------------------

--
-- Структура таблицы `parser`
--

CREATE TABLE `parser` (
  `id` char(128) NOT NULL,
  `scheme` char(32) NOT NULL,
  `version` int(11) NOT NULL,
  `_url` tinytext NOT NULL,
  `last` datetime DEFAULT NULL,
  `result` text NOT NULL,
  `state` enum('active','processed','archived','removed') NOT NULL DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `recipes`
--

CREATE TABLE `recipes` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `created` datetime DEFAULT NULL,
  `lang` char(2) NOT NULL DEFAULT 'ru',
  `author_id` bigint(20) UNSIGNED DEFAULT NULL,
  `name` char(128) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `image` char(64) DEFAULT NULL,
  `active` tinyint(1) NOT NULL DEFAULT 1,
  `rate` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `cook_time` time DEFAULT NULL,
  `cook_level` tinyint(3) UNSIGNED NOT NULL DEFAULT 0,
  `portion` tinyint(3) UNSIGNED NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `recipes`
--

INSERT INTO `recipes` (`id`, `created`, `lang`, `author_id`, `name`, `description`, `image`, `active`, `rate`, `cook_time`, `cook_level`, `portion`) VALUES
(28, '2021-02-14 18:25:10', 'ru', 2, 'Куриный суп с рисом', 'Куриный суп с рисом – один из самых популярных домашних супов, но, несмотря на практический одинаковый состав, каждая хозяйка придаёт ему свою изюминку. Ри Драммонд, например, перед добавлением измельчённой куриной грудки в суп натирает её пряностями и запекает в духовке. Так вкус и курицы, и супа получится более насыщенным, богатым и согревающим. Длиннозёрный рис также отваривается отдельно и добавляется лишь в конце, чтобы вы могли насладиться прозрачным бульоном, наполненным ароматом овощей и пряностей', '1591809169_kurinyj-sup-s-risom[2].jpg', 1, 0, '00:03:00', 1, 2),
(29, '2021-02-14 18:25:17', 'ru', 2, 'еуые', 'rewrewrew', '1606131505_sup-pyure-iz-pechenyh-tomatov[1].jpg', 1, 0, '00:03:00', 2, 2),
(32, '2021-02-21 09:44:41', 'ru', 2, 'Стью из тилапии и морепродуктов', 'Филе тилапии и мидии в раковинах тушатся в густом соусе из перетёртых помидоров с сельдереем и луком. Немного сухого белого вина и соки, выделившиеся из мидий, наполняют стью волшебным вкусом и ароматом. Не забудьте промыть мидии от песка и удалить им бороды, а также удалите нераскрывшиеся ракушки из кастрюли, чтобы ничто не омрачило ваш ужин. Подавайте стью в глубоких чашах, посыпав свежей зеленью, с ломтиком поджаренной чиабатты, чтобы макать в соус и наслаждаться.', '1607883946_styu-iz-tilapii-i-moreproduktov[1].jpg', 1, 0, '00:35:00', 3, 4),
(33, '2021-02-21 10:12:27', 'ru', 2, 'Чили с чёрной фасолью и стейком филе-миньон', 'Нежное, тающее во рту филе-миньон превращает техасско-мексиканское блюдо чили-кон-карне (чили с мясом) в великолепное угощение для особого случая. Стейк нарезается небольшими кусочками, слегка обжаривается и добавляется в готовое чили, густое и ароматное, наполненное говяжьим фаршем, двумя видами фасоли (пинто и чёрной фасолью), перцами (сладкими и острыми) и порошком чили, который и дал блюду своё название. А для подачи приготовьте быстрые сырные криспы, свежую сальсу пико-де-гальо и пюре из авокадо с зелёным луком. Все вкусы превосходно сочетаются друг с другом, согревают и дарят настоящее удовольствие.', '1608139572_chili-s-chernoj-fasolyu-i-stejkom-file-minon[3].jpg', 1, 0, '01:30:01', 1, 6);

-- --------------------------------------------------------

--
-- Структура таблицы `recipes_cats`
--

CREATE TABLE `recipes_cats` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `parent_id` bigint(20) UNSIGNED DEFAULT NULL,
  `name` char(64) NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT 1,
  `sort` tinyint(3) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `recipes_cats`
--

INSERT INTO `recipes_cats` (`id`, `parent_id`, `name`, `active`, `sort`) VALUES
(1, NULL, 'Супы', 1, 1),
(2, 1, 'Заправочные супы', 1, 0),
(3, 1, 'Прозрачные супы', 1, 0),
(6, 1, 'Супы-пюре', 1, 0),
(7, 1, 'Крем-супы', 1, 0),
(8, 1, 'Овощные супы', 1, 0),
(9, NULL, 'Основные блюда', 1, 2),
(10, 9, 'Мясо', 1, 0),
(11, 9, 'Птица, дичь', 1, 0),
(12, 9, 'Овощи, грибы', 1, 0),
(13, 9, 'Рыба, морепродукты', 1, 0),
(14, 9, 'Крупы, бобовые', 1, 0),
(15, 9, 'Яйца', 1, 0),
(16, 9, 'Молочные продукты', 1, 0),
(17, 9, 'Макаронные изделия', 1, 0),
(18, NULL, 'Здоровое питание', 1, 1),
(19, NULL, 'Все рецепты', 1, 0);

-- --------------------------------------------------------

--
-- Структура таблицы `recipes_rates`
--

CREATE TABLE `recipes_rates` (
  `recipe_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `value` tinyint(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `recipes_rates`
--

INSERT INTO `recipes_rates` (`recipe_id`, `user_id`, `value`) VALUES
(28, 2, 3),
(28, 2, 3),
(28, 2, 2),
(28, 2, 5),
(29, 2, 4),
(32, 2, 4);

-- --------------------------------------------------------

--
-- Структура таблицы `recipes_to_cats`
--

CREATE TABLE `recipes_to_cats` (
  `recipe_id` bigint(20) UNSIGNED NOT NULL,
  `recipe_cat_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `recipes_to_cats`
--

INSERT INTO `recipes_to_cats` (`recipe_id`, `recipe_cat_id`) VALUES
(28, 7),
(28, 8),
(29, 2),
(29, 6),
(32, 11),
(32, 12),
(32, 14),
(33, 10),
(33, 11);

-- --------------------------------------------------------

--
-- Структура таблицы `stages`
--

CREATE TABLE `stages` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `recipe_id` bigint(20) UNSIGNED NOT NULL,
  `name` char(128) NOT NULL,
  `image` char(64) NOT NULL,
  `text` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `stages`
--

INSERT INTO `stages` (`id`, `recipe_id`, `name`, `image`, `text`) VALUES
(1, 28, 'Начало', '', 'Таким образом реализация намеченных плановых заданий позволяет оценить значение новых предложений. Равным образом консультация с широким активом требуют определения и уточнения модели развития. С другой стороны рамки и место обучения кадров способствует подготовки и реализации модели развития.\r\nИдейные соображения высшего порядка, а также укрепление и развитие структуры играет важную роль в формировании существенных финансовых и административных условий. С другой стороны постоянное информационно-пропагандистское обеспечение нашей деятельности обеспечивает широкому кругу (специалистов) участие в формировании позиций, занимаемых участниками в отношении поставленных задач.'),
(2, 28, 'Продолжение', '1606131505_sup-pyure-iz-pechenyh-tomatov[2].jpg', 'Если у вас есть какие то интересные предложения, обращайтесь! Студия Web-Boss всегда готова решить любую задачу.\r\nТоварищи! сложившаяся структура организации представляет собой интересный эксперимент проверки направлений прогрессивного развития.');

-- --------------------------------------------------------

--
-- Структура таблицы `units`
--

CREATE TABLE `units` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` char(32) NOT NULL,
  `short` char(12) NOT NULL,
  `lang_id` char(2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `units`
--

INSERT INTO `units` (`id`, `name`, `short`, `lang_id`) VALUES
(1, 'литр', 'л.', 'ru'),
(2, 'килограмм', 'кг.', 'ru'),
(3, 'cтол. ложка', 'cт. лож.', 'ru'),
(4, 'штук', 'шт.', 'ru'),
(5, 'грамм', 'г.', 'ru'),
(6, 'миллилитр', 'мл.', 'ru'),
(7, 'чайная ложка', 'чай. лож.', 'ru'),
(8, 'миллиграмм', 'мг.', 'ru'),
(9, 'щепотка', 'щеп.', 'ru');

-- --------------------------------------------------------

--
-- Структура таблицы `user`
--

CREATE TABLE `user` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `username` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `auth_key` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `password_hash` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `password_reset_token` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `status` smallint(6) NOT NULL DEFAULT 10,
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) NOT NULL,
  `verification_token` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `aliase` char(128) COLLATE utf8_unicode_ci DEFAULT NULL,
  `role` enum('admin','partner','manager','user') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'user'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Дамп данных таблицы `user`
--

INSERT INTO `user` (`id`, `username`, `auth_key`, `password_hash`, `password_reset_token`, `email`, `status`, `created_at`, `updated_at`, `verification_token`, `aliase`, `role`) VALUES
(1, 'vmaya', 'sNrPwBytR0nrjVD5ZMFVaXgkY-gkcOxD', '$2y$13$T17XcF3qJEzDTCqqubxivul8pI5RfejzO5T8qKnuxz53cpextukie', NULL, 'fwadim@mail.ru', 9, 1613056256, 1613056256, 'zcqQFk5D6tRXoTbe6LJ2vbFgfB5toSzh_1613056256', NULL, 'user'),
(2, 'freevmaya', 'sNrPwBytR0nrjVD5ZMFVaXgkY-gkcOxD', '$2y$13$T17XcF3qJEzDTCqqubxivul8pI5RfejzO5T8qKnuxz53cpextukie', NULL, 'freevmaya@gmail.com', 10, 1613189214, 1613898439, 'Fl2YJC0XJ3CJcNCgEbUI_Lc0OKO8vi1r_1613189214', NULL, 'admin');

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `ingredients`
--
ALTER TABLE `ingredients`
  ADD PRIMARY KEY (`id`),
  ADD KEY `active` (`active`),
  ADD KEY `author_id` (`author_id`),
  ADD KEY `unit_id` (`unit_id`);

--
-- Индексы таблицы `ingredients_to_recipe`
--
ALTER TABLE `ingredients_to_recipe`
  ADD PRIMARY KEY (`recipe_id`,`ingredient_id`),
  ADD KEY `ingredientByIngredient` (`ingredient_id`),
  ADD KEY `unit_id` (`unit_id`);

--
-- Индексы таблицы `languages`
--
ALTER TABLE `languages`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `migration`
--
ALTER TABLE `migration`
  ADD PRIMARY KEY (`version`);

--
-- Индексы таблицы `parser`
--
ALTER TABLE `parser`
  ADD PRIMARY KEY (`id`,`state`) USING BTREE,
  ADD KEY `scheme` (`scheme`);

--
-- Индексы таблицы `recipes`
--
ALTER TABLE `recipes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `byAutor` (`author_id`),
  ADD KEY `ByLevel` (`cook_level`),
  ADD KEY `rate` (`rate`),
  ADD KEY `active` (`active`),
  ADD KEY `lang` (`lang`);

--
-- Индексы таблицы `recipes_cats`
--
ALTER TABLE `recipes_cats`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ByParent` (`parent_id`),
  ADD KEY `ByActive` (`active`);

--
-- Индексы таблицы `recipes_rates`
--
ALTER TABLE `recipes_rates`
  ADD KEY `recipe_id` (`recipe_id`,`user_id`) USING BTREE;

--
-- Индексы таблицы `recipes_to_cats`
--
ALTER TABLE `recipes_to_cats`
  ADD PRIMARY KEY (`recipe_id`,`recipe_cat_id`),
  ADD KEY `ByRecipesCat` (`recipe_cat_id`);

--
-- Индексы таблицы `stages`
--
ALTER TABLE `stages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `recipe_id` (`recipe_id`);

--
-- Индексы таблицы `units`
--
ALTER TABLE `units`
  ADD PRIMARY KEY (`id`),
  ADD KEY `lang` (`lang_id`),
  ADD KEY `short` (`short`);

--
-- Индексы таблицы `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `password_reset_token` (`password_reset_token`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `ingredients`
--
ALTER TABLE `ingredients`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT для таблицы `recipes`
--
ALTER TABLE `recipes`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT для таблицы `recipes_cats`
--
ALTER TABLE `recipes_cats`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT для таблицы `recipes_rates`
--
ALTER TABLE `recipes_rates`
  MODIFY `recipe_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT для таблицы `stages`
--
ALTER TABLE `stages`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT для таблицы `units`
--
ALTER TABLE `units`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT для таблицы `user`
--
ALTER TABLE `user`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Ограничения внешнего ключа сохраненных таблиц
--

--
-- Ограничения внешнего ключа таблицы `ingredients`
--
ALTER TABLE `ingredients`
  ADD CONSTRAINT `ingrByUnit` FOREIGN KEY (`unit_id`) REFERENCES `units` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Ограничения внешнего ключа таблицы `ingredients_to_recipe`
--
ALTER TABLE `ingredients_to_recipe`
  ADD CONSTRAINT `ingredientByIngredient` FOREIGN KEY (`ingredient_id`) REFERENCES `ingredients` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `ingredientByRecipe` FOREIGN KEY (`recipe_id`) REFERENCES `recipes` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `ingredientByUnit` FOREIGN KEY (`unit_id`) REFERENCES `units` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Ограничения внешнего ключа таблицы `recipes`
--
ALTER TABLE `recipes`
  ADD CONSTRAINT `ByAutor` FOREIGN KEY (`author_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ограничения внешнего ключа таблицы `recipes_cats`
--
ALTER TABLE `recipes_cats`
  ADD CONSTRAINT `ByParentCat` FOREIGN KEY (`parent_id`) REFERENCES `recipes_cats` (`id`);

--
-- Ограничения внешнего ключа таблицы `recipes_to_cats`
--
ALTER TABLE `recipes_to_cats`
  ADD CONSTRAINT `ByRecipe` FOREIGN KEY (`recipe_id`) REFERENCES `recipes` (`id`),
  ADD CONSTRAINT `ByRecipesCat` FOREIGN KEY (`recipe_cat_id`) REFERENCES `recipes_cats` (`id`);

--
-- Ограничения внешнего ключа таблицы `stages`
--
ALTER TABLE `stages`
  ADD CONSTRAINT `stageByRecipe` FOREIGN KEY (`recipe_id`) REFERENCES `recipes` (`id`) ON UPDATE NO ACTION;

--
-- Ограничения внешнего ключа таблицы `units`
--
ALTER TABLE `units`
  ADD CONSTRAINT `unitByLang` FOREIGN KEY (`lang_id`) REFERENCES `languages` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
