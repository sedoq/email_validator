Проверка существования Email
================================

Установка
------

Для добавления пакета в проект необходимо запустить

~~~
composer require maximishchenko/email_validator "dev-master"
~~~

Либо добавить 

~~~
"maximishchenko/email_validator": "dev-master"
~~~

в секцию ```require``` файла ```composer.json```

Использование
------

Объявить класс ```img\mail\EmailService```.

Передать значения email и выходного формата (json или xml). В случае если не объявлен формат - будет использован формат XML



~~~
require '../vendor/autoload.php';

use img\mail\EmailService;

$email = isset($_GET['mail']) ? $_GET['mail'] : null;
$format = isset($_GET['format']) ? $_GET['format'] : null;

new EmailService(['email' => $email, 'format' => $format]);
~~~