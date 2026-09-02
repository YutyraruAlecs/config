<?php
session_start();
#define('ENVIRONMENT', 'dev'); 			// Путь к этой папке

define("TODAY", date("Y-m-d")); 		/// Сегодняшняя дата
define("TIME", date("H:i:s")); 			/// Текущее время
define("TODAY_TIME", TODAY." ".TIME); 	/// Дата - время

// # IP - клиента
$client = @$_SERVER['HTTP_CLIENT_IP'];
$forward = @$_SERVER['HTTP_X_FORWARDED_FOR'];
$remote = @$_SERVER['REMOTE_ADDR'];

if(filter_var($client, FILTER_VALIDATE_IP)) $tip = $client;
elseif(filter_var($forward, FILTER_VALIDATE_IP)) $tip = $forward;
else $tip = $remote;
define("IP", $tip); /// конечный IP адресс клиента
// IP - клиента

// Определяем, используется ли HTTPS :: ($_SERVER['SERVER_PORT'] == 443) - port
if ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443) { define("SECURE", true); } else { define("SECURE", false); }

# подключение файла settings.ini и его обработка
$data_ini = parse_ini_file(ROOT_PATH . '/app/config/settings.ini', true);
if (!empty($data_ini['AdminSettings']['mKey'])) define("ADMIN_KEY", $data_ini['AdminSettings']['mKey']); # Ключ администратора
if (!empty($data_ini['AdminSettings']['mURL'])) define("ROOT_URL", $data_ini['AdminSettings']['mURL']); # Основыной URL сайта

try{
	define('DB_HOST_NAME', 'Admin');
	define('LINK', mysqli_connect(
		$data_ini[DB_HOST_NAME]['host'],
		$data_ini[DB_HOST_NAME]['login'],
		$data_ini[DB_HOST_NAME]['password'],
		$data_ini[DB_HOST_NAME]['database']
	)); # Подключение к Базе данных

	define('DB__NAME', $data_ini[DB_HOST_NAME]['database']); # Название базы данных
} catch(mysqli_sql_exception $e){
	// Выводим ошибку в случае отсутствия соединения с БД
	echo 'Ошибка соединения с БД: '. $e->getMessage();
	exit();
}

/* 
	В файл settings.ini можно добавлять свои параметры
	Пример: 
	[<название новых параметров>]
	<название параметра>=<содержимое параметра>

	Пример подключения: 
	if (!empty($data_ini['<название новых параметров>']['<название параметра>'])) define("ROOT_URL", $data_ini['<название новых параметров>']['<название параметра>']); 
*/

# Запись логов
# Для использовании функции ниже нужен log-master ищите в моих репозиториях!
/* function SetLog($text) {
	if (empty($text)) { return 'Введите текст лога!'; }

	$data = http_build_query([
		"IP" => IP,
		"h" => $_SERVER['SERVER_NAME'],
		"t" => $text
	]);

	$options = array(
		'http' => array(
			'method' => "POST",
			'header' => [
				"Content-type: application/x-www-form-urlencoded",
				"Content-Length: " . strlen($data),
				"Nps-Games-Privat-Admin-Key: " . ADMIN_KEY
			],
			'content' => $data
		)
	);

	file_get_contents(ROOT_URL . '/log-master/set', false, stream_context_create($options));

	switch (json_last_error()) {
		case JSON_ERROR_NONE:
			return true;
			break;
		case JSON_ERROR_DEPTH:
			return 'Достигнута максимальная глубина стека';
			break;
		case JSON_ERROR_STATE_MISMATCH:
			return 'Некорректные разряды или несоответствие режимов';
			break;
		case JSON_ERROR_CTRL_CHAR:
			return 'Некорректный управляющий символ';
			break;
		case JSON_ERROR_SYNTAX:
			return 'Синтаксическая ошибка, некорректный JSON';
			break;
		case JSON_ERROR_UTF8:
			return 'Некорректные символы UTF-8, возможно неверно закодирован';
			break;
		default:
			return 'Неизвестная ошибка';
			break;
	}

	return false;
} */