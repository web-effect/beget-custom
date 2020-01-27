# Установка:

1. Копируем папку в корень хостинга.
2. Создаём в панели в пункте 'CronTab' задачу вида:
/usr/local/php-cgi/7.1/bin/php ~/.custom/db_pass_change.php
3. Запуск задачи настраиваем по потребностям например раз в месяц,
либо запускаем вручную по кнопке вида play справа от задачи
4. Пароли обновляются только у баз данных, поэтому для входа в phpmyadmin из панели нужно будет вводить пароль

# Описание файлов и структуры:
- Скрипты обработки располагаются в корне:
    - [batch_changes.php](#batch_changesphp---массовое-применение-изменений) - массовое применение изменений
    - [db_pass_change.php](#db_pass_changephp---массовое-изменение-паролей-к-бд) - массовое изменение паролей к БД
    - [generator.php](#generatorphp---генерация-различных-переменных) - генерация различных переменных (в частности для MODX - пароли, префиксы, sessionname, uuid)
- Классы располагаются в папке classes
    - [classes/cms.iterator.class.php](#cmsiterator-classescmsiteratorclassphp) - класс для итерации и применения callback функции к CMS
    - [classes/cms/_base.class.php](#base_controller-classescms_baseclassphp) - основной класс контроллера CMS
    - [classes/cms/\<CMS\>/cms.class.php](#cms_controller-classescmscmscmsclassphp) - класс конретной cms, обязательно расширяет оснвной класс classes/cms/_base.class.php
- examples - примеры скриптов
- updater - скрипты и данные для массового изменения CMS

# Описание скриптов:
## batch_changes.php - массовое применение изменений
Пробегает по сайтам и обновляет их используя скрипты и данные в папке updater/\<CMS\>/

Основоной скрипт updater/\<CMS\>/update.php, вызывается в консольном режиме, чтобы не мешать основному скрипту, где работает CMSIterator. В первом параметре скрипта передаётся html_query, содержащая параметры для работы, в частности $path - путь к текущей диретории. Чтобы извлечь параметры можно использовать например ```parse_str($argv[1],$_REQUEST);```

## db_pass_change.php - массовое изменение паролей к БД
Пробегает по сайтам и обновляет пароли к базам данных.

Содежрдит функцию **changeDBPass** осушществляющую смену пароля и **$CMSChangePass** - callback для CMSIterator, который изменяет пароль через **changeDBPass** и пытается записать новый парольв конфигурацию CMS, а в случае неудачи меняет пароль обртано.

## generator.php - генерация различных переменных
Геренрирует различные строки на основе шаблонов и функции **generate**

**generate** принимает 2 параметра:  
**$chars_set** - массив символов для генерации или список алиасов наборов через запятую. Доступные наборы:
 - **al** латинский алфавит
 - **lower** - строчные буквы
 - **upper** - заглавные буквы
 - **num** - цифры
 - **spec** - спецсимволы

**$length** - длинна строки для генерации

Примеры:
 - ```generate('al,num,spec',8)``` создаст строку в 8 символов из алфавита, цифр и спецсимволов
 - ```generate('lower,num',14)``` создаст строку в 14 символов из строчных букв и цифр


# Описание классов:
## CMSIterator (classes/cms.iterator.class.php)
Класс для итерации и применения callback функции к CMS
### Свойства
```php
private $root=''; //Путь к директории в которой находятся директории сайтов для обхода
private $classmap=array(); //Список классов CMS для обхода
```
### Методы
```php
public function __construct(string $root)
```
Конструктор класса.

**$root** - Путь к директории в которой находятся директории сайтов для обхода.  
<br>
```php
public function loadClasses() : void
```
Загружает классы CMS и заполняет $classmap для обхода.  
<br>
```php
public function apply(callable $callback,array $params=array()) : void
```
Применяет функцию $callback к каждой обнаруженной CMS. Проходит по дирекориям в $root, проверяет наличие папки public_html, после чего  ищет подходящий класс CMS вызывая статический метод getFromPath для каждого класса из $classmap, пока не будет возвращён экзэмпляр  класса контролера CMS.

**$callback** - функция, принимающая 2 параметра: $CMS - экзэмпляр класса контроллера CMS, $params - массив дополнительных параметров.  
**$params** - массив дополнительных параметров.  
### Пример
```php
$callback=function(&$CMS,$params){
    $config = $CMS->getConfig();
    if(!$config){
        echo $CMS->getErrors()."\n";
        return;
    }
    var_dump($config);
    var_dump($params);
};
include_once(__DIR__.'/classes/cms.iterator.class.php');
$iterator = new CMSIterator(dirname(__DIR__));
$iterator->apply($callback,array('key'=>'value'));
```

## BASE_Controller (classes/cms/_base.class.php)
Основной абстрактный класс контроллера CMS
### Свойства
```php
const CMS=null; //Название CMS
public $CMSkey='' //Ключь CMS, если не задан, будет устанавлен итератором на основании директории класса
protected $path = '';//Директория CMS
protected $errors = array();//Массив ошибок
```
### Методы
```php
public function __construct(string $path)
```
Конструктор класса.

**$path** - Директория CMS  
<br>
```php
static function getFromPath(string $path) : void
```
Определяет находится ли CMS по указанному пути и если находится то возвращает экзэмпляр класса CMS. Если CMS не найдена возвращает false

**$path** - Директория CMS  
<br>
```php
public function getPath() : string
```
Возвращает путь к директории CMS  
<br>
```php
public function getConfig() : array
```
Пытается получить конфигурацию CMS. Возвращает false в случае неудачи. Иначе возвращает массив с ключами: 'db_type', 'db_user', 'db_host', 'db_pwd'  
<br>
```php
public function setConfig(array $config) : boolean
```
Пытается записать конфигурацию CMS. Возвращает false в случае неудачи.  

**$config** - Массив параметров для записи. Поддерживаются ключи: 'db_type', 'db_user', 'db_host', 'db_pwd'   
<br>
```php
public function getErrors() : string
```
Возвращает список ошибок, разделённый переводом строки

## \<CMS\>_Controller (classes/cms/\<CMS\>/cms.class.php)
Класс конкретной CMS, расширяет BASE_Controller. Обязательно должен быть описан метод getFromPath. Файл должен возвращать имя класса контролера.
