<?php
// Shim for missing apache_request_headers
if (!function_exists('apache_request_headers')) {
    function apache_request_headers() {
        $headers = [];
        foreach ($_SERVER as $name => $value) {
            if (substr($name, 0, 5) == 'HTTP_') {
                $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
            }
        }
        return $headers;
    }
}

require_once __DIR__ . '/../../env.php';

spl_autoload_register(function($class_name){ 
		if(file_exists('../../core/Models/'.$class_name.'.php')){
			require_once '../../core/Models/'.$class_name.'.php';
		}
		elseif(file_exists('../../../core/Models/'.$class_name.'.php')){
			require_once '../../../core/Models/'.$class_name.'.php';
		}
		elseif (file_exists('../../core/Controllers/'.$class_name.'.php')) {
			require_once '../../core/Controllers/'.$class_name.'.php';
		}
		elseif (file_exists('../../../core/Controllers/'.$class_name.'.php')) {
			require_once '../../../core/Controllers/'.$class_name.'.php';
		}
});

?>