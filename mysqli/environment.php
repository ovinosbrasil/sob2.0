<?php
// Keep the existing hosting defaults when no environment is supplied.
define('DB_HOSTNAME', getenv('DB_HOST') !== false ? getenv('DB_HOST') : 'localhost');
define('DB_USERNAME', getenv('DB_USER') !== false ? getenv('DB_USER') : 'siste870_sob');
define('DB_PASSWORD', getenv('DB_PASSWORD') !== false ? getenv('DB_PASSWORD') : 'sob123');
define('DB_DATABASE', isset($databaseName) ? $databaseName : (getenv('DB_DATABASE') ?: 'siste870_sob'));
define('DB_CHARSET', getenv('DB_CHARSET') ?: 'latin1');
