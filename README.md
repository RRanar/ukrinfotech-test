                                 Инструкция 
1.Первые шаги.
Клонируем репозиторий.
Перед использованием библиотеки необходимо для начала настроить файл  /config/db.php.
(Для работы используется БД MySQL ) 
DB_USER - имя пользователя для доступа к MySQL
DB_PASS - пароль для доступа к MySQL
DB_NAME - имёя базы данных которая будет использована для создания таблицы бинарного дерева.
Также необходимо не забыть подключить файл /vendor/autoload.php для использования автозагрузки классов 
В файле index.php продемонстрированы основные методы для работы с классами.