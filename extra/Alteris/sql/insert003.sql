INSERT INTO `group` (`id`, `name`, `parent_id`, `deep`, `hierarchy`) VALUES
(4, 'Materiały eksploatacyjne', 0, 1, '00001'),
(5, 'Oryginalne', 4, 2, '00001-00003'),
(6, 'Alternatywne', 4, 2, '00001-00002'),
(7, 'Folie do faksów', 5, 3, '00001-00003-00001'),
(8, 'Folie do faksów', 6, 3, '00001-00002-00001'),
(9, 'Taśmy do drukarek igłowych', 6, 3, '00001-00002-00002'),
(10, 'Taśmy do drukarek igłowych', 5, 3, '00001-00003-00002'),
(11, 'Tusze i tonery do faksów', 6, 3, '00001-00002-00003'),
(12, 'Tusze i tonery do faksów', 5, 3, '00001-00003-00003'),
(13, 'Meble biurowe', 0, 1, '00002'),
(14, 'Krzesła i fotele biurowe', 13, 2, '00002-00002'),
(15, 'Biurka, szafy, kontenery', 13, 2, '00002-00001'),
(16, 'Meble metalowe', 13, 2, '00002-00003');