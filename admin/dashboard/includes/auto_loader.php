<?php

$baseDir = __DIR__ . '/../../..';

spl_autoload_register(function($class_name) use ($baseDir){ 
		if(file_exists($baseDir . '/core/Models/'.$class_name.'.php')){
			require_once $baseDir . '/core/Models/'.$class_name.'.php';
		}
		elseif (file_exists($baseDir . '/core/Controllers/'.$class_name.'.php')) {
			require_once $baseDir . '/core/Controllers/'.$class_name.'.php';
		}
});

if(file_exists($baseDir . '/core/helpers/vendor/site.php')){require_once $baseDir . '/core/helpers/vendor/site.php';}
if(file_exists($baseDir . '/core/helpers/vendor/autoload.php')){require_once $baseDir . '/core/helpers/vendor/autoload.php';}

?>