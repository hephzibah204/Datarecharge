<?php

class ProviderAPI {
    private $db;
    private $config;

    public function __construct($dbConfig) {
        $this->db = $this->connect($dbConfig);
        $this->config = $this->loadConfig();
    }

    private function connect($dbConfig) {
        $host = $dbConfig['host'] ?? 'localhost';
        $port = $dbConfig['port'] ?? '3306';
        $dbname = $dbConfig['database'] ?? 'vnplanners';
        $user = $dbConfig['username'] ?? 'root';
        $pass = $dbConfig['password'] ?? '';

        try {
            $dsn = "mysql:host=$host;port=$port;dbname=$dbname;"
                . "charset=utf8mb4;connect_timeout=30;"
                . "read_timeout=60;write_timeout=60";
            return new PDO($dsn, $user, $pass, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);
        } catch (PDOException $e) {
            error_log("Database connection failed: " . $e->getMessage());
            throw $e;
        }
    }

    private function loadConfig() {
        $configFile = __DIR__ . '/config.json';
        if (file_exists($configFile)) {
            $config = json_decode(file_get_contents($configFile), true);
            return $config;
        }
        
        // Default configuration
        return [
            'default_provider' => 'dataverify',
            'timeout' => 30,
            'retry_attempts' => 3,
            'log_enabled' => true,
            'debug_mode' => false
        ];
    }

    public function validateAccessToken($token) {
        $sql = "SELECT * FROM api_configs WHERE token = :token AND is_active = 1";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':token', $token, PDO::PARAM_STR);
        $stmt->execute();
        
        $result = $stmt->fetch(PDO::FETCH_OBJ);
        if ($result) {
            $stmt = $this->db->prepare("UPDATE api_configs SET last_used = NOW() WHERE token = :token");
            $stmt->bindValue(':token', $token, PDO::PARAM_STR);
            $stmt->execute();
            
            return [
                'status' => 'success',
                'userid' => $result->user_id,
                'usertype' => $result->user_type,
                'balance' => (float)$result->balance,
                'refearedby' => $result->refearedby,
                'phone' => $result->phone,
                'name' => $result->name,
                'provider' => $result->provider
            ];
        }
        
        return ['status' => 'fail', 'message' => 'Invalid token'];
    }

    public function getProviderById($providerId) {
        $sql = "SELECT * FROM providers WHERE id = :id AND is_active = 1";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $providerId, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_OBJ);
    }

    public function getAllProviders($action = null, $isActive = true) {
        $sql = "SELECT * FROM providers WHERE 1=1";
        if ($action) {
            $sql .= " AND FIND_IN_SET(:action, supported_actions)";
        }
        if ($isActive !== null) {
            $sql .= " AND is_active = :isActive";
        }
        $sql .= " ORDER BY priority DESC, name ASC";
        
        $stmt = $this->db->prepare($sql);
        if ($action) {
            $stmt->bindValue(':action', $action, PDO::PARAM_STR);
        }
        if ($isActive !== null) {
            $stmt->bindValue(':isActive', $isActive ? 1 : 0, PDO::PARAM_BOOL);
        }
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    public function getProviderByName($name) {
        $sql = "SELECT * FROM providers WHERE name = :name AND is_active = 1";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':name', $name, PDO::PARAM_STR);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_OBJ);
    }

    public function loadProviderScripts($provider) {
        $scripts = [];
        if (isset($provider->js_file) && $provider->js_file) {
            $scripts[] = $provider->js_file;
        }
        if (isset($provider->external_script) && $provider->external_script) {
            $scripts[] = $provider->external_script;
        }
        return $scripts;
    }

    public function validateProvider($providerId, $action) {
        $sql = "SELECT * FROM providers WHERE id = :id AND is_active = 1";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $providerId, PDO::PARAM_INT);
        $stmt->execute();
        
        $provider = $stmt->fetch(PDO::FETCH_OBJ);
        
        if (!$provider) {
            return ['status' => 'fail', 'message' 'Provider not found or inactive'];
        }
        
        if (!empty($provider->supported_actions) && !is_null($action)) {
            $actions = explode(',', $provider->supported_actions);
            if (!in_array($action, $actions)) {
                return ['status' => 'fail', 'message' => "Provider does not support action: $action"];
            }
        }
        
        $startDate = strtotime($provider->start_date ?? '1970-01-01');
        $endDate = $provider->end_date ? strtotime($provider->end_date) : PHP_INT_MAX;
        $now = time();
        
        if ($now < $startDate || $now > $endDate) {
            return ['status' => 'fail', 'message' => 'Provider is not active at this time'];
        }
        
        return ['status' => 'success', 'provider' => $provider];
    }

    public function logProviderRequest($providerId, $action, $requestData, $response, $status, $userId = null) {
        $sql = "INSERT INTO provider_logs (
                    provider_id, action, request_data, response, status,
                    user_id, ip_address, user_agent, created_at
                ) VALUES (
                    :provider_id, :action, :request_data, :response, :status,
                    :user_id, :ip_address, :user_agent, NOW()
                )";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':provider_id', $providerId, PDO::PARAM_INT);
        $stmt->bindValue(':action', $action, PDO::PARAM_STR);
        $stmt->bindValue(':request_data', json_encode($requestData), PDO::PARAM_STR);
        $stmt->bindValue(':response', json_encode($response), PDO::PARAM_STR);
        $stmt->bindValue(':status', $status, PDO::PARAM_STR);
        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $stmt->bindValue(':ip_address', $_SERVER['REMOTE_ADDR'] ?? null, PDO::PARAM_STR);
        $stmt->bindValue(':user_agent', $_SERVER['HTTP_USER_AGENT'] ?? null, PDO::PARAM_STR);
        $stmt->execute();
    }

    public function processProviderRequest($providerId, $action, $requestData, $userId = null) {
        $validation = $this->validateProvider($providerId, $action);
        if ($validation['status'] == 'fail') {
            return $validation;
        }
        
        $provider = $validation['provider'];
        $this->logProviderRequest($providerId, $action, $requestData, ['status' => 'processing'], 'processing', $userId);
        
        $response = $this->callProviderAPI($provider, $action, $requestData);
        
        $this->logProviderRequest($providerId, $action, $requestData, $response, $response['status'], $userId);
        
        return $response;
    }

    private function callProviderAPI($provider, $action, $requestData) {
        $endpoint = $provider->endpoint_url;
        $apiKey = $provider->api_key;
        $timeout = $provider->timeout ?? $this->config['timeout'];
        
        $data = [
            'action' => $action,
            'data' => $requestData,
            'timestamp' => date('Y-m-d H:i:s'),
            'provider_version' => $provider->api_version ?? '1.0'
        ];
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $endpoint);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $apiKey,
            'X-Provider-ID: ' . $provider->id,
            'X-Provider-Action: ' . $action
        ]);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);
        
        if ($curlError) {
            return [
                'status' => 'fail',
                'message' => 'Provider API error: ' . $curlError,
                'http_code' => $httpCode
            ];
        }
        
        $decodedResponse = json_decode($response, true);
        
        if (JSON_ERROR_NONE !== json_last_error()) {
            return [
                'status' => 'fail',
                'message' => 'Invalid JSON response from provider API',
                'http_code' => $httpCode,
                'raw_response' => $response
            ];
        }
        
        $decodedResponse['http_code'] = $httpCode;
        return $decodedResponse;
    }

    public function getProviderHealth($providerId) {
        $sql = "SELECT id, name, endpoint_url, is_active, last_check, ping_count, created_at FROM providers WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $providerId, PDO::PARAM_INT);
        $stmt->execute();
        
        $provider = $stmt->fetch(PDO::FETCH_OBJ);
        
        if (!$provider) {
            return null;
        }
        
        $now = new DateTime();
        $lastCheck = new DateTime($provider->last_check);
        $diff = $now->diff($lastCheck)->h;
        
        $health = [
            'id' => $provider->id,
            'name' => $provider->name,
            'endpoint' => $provider->endpoint_url,
            'is_active' => (bool)$provider->is_active,
            'last_check' => $provider->last_check,
            'ping_count' => (int)$provider->ping_count,
            'uptime_percentage' => $this->calculateUptimePercentage($provider),
            'needs_check' => $diff > 60,
            'error_count' => (int)$provider->error_count ?? 0
        ];
        
        return $health;
    }

    public function pingProvider($providerId) {
        $provider = $this->getProviderById($providerId);
        if (!$provider) {
            return ['status' => 'fail', 'message' => 'Provider not found'];
        }
        
        $url = $provider->endpoint_url . '?health_check=' . time();
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);
        
        $success = ($httpCode >= 200 && $httpCode < 300) && empty($curlError);
        
        $sql = "UPDATE providers SET last_check = NOW(), ping_count = ping_count + 1, is_active = :isActive, error_count = error_count + :errorCount WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':isActive', $success ? 1 : 0, PDO::PARAM_BOOL);
        $stmt->bindValue(':errorCount', $success ? 0 : 1, PDO::PARAM_INT);
        $stmt->bindValue(':id', $providerId, PDO::PARAM_INT);
        $stmt->execute();
        
        return [
            'status' вещейar 'success',
            'provider' => $provider->name,
            'http_code' => $httpCode,
            'curl_error' => $curlError,
            'success' => $success
        ];
    }

    private function calculateUptimePercentage($provider) {
        $totalTime = 24 * 60 * 60 * 30; // 30 days in seconds
        $pingCount = (int)$provider->ping_count;
        $lastCheck = new DateTime($provider->last_check);
        $now = new DateTime();
        $minutesSinceLastCheck = $lastCheck->diff($now)->i;
        
        $estimatedPings = $pingCount + ($minutesSinceLastCheck / 60);
        $uptimePercentage = ($estimatedPings / $totalTime) * 100;
        
        return min(100, max(0, round($uptimePercentage, 2)));
    }
}

function createProviderAPI($configFilePath = null) {
    $dbConfig = [
        'host' => getenv('DB_HOST') ?? 'localhost',
        'database' => getenv('DB_NAME') ?? 'vnplanners',
        'username' => getenv('DB_USER') ?? 'root',
        'password' => getenv('DB_PASS') ?? '',
        'port' => getenv('DB_PORT') ?? '3306'
    ];
    
    return new ProviderAPI($dbConfig);
}


// Create default provider API instance
$providerAPI = createProviderAPI();

// API handler function
function handleProviderRequest($action, $providerId, $requestData, $token, $userId) {
    global $providerAPI;
    
    // Validate token
    $auth = $providerAPI->validateAccessToken($token);
    if ($auth['status'] == 'fail') {
        return ['status' => 'fail', 'message' => $auth['message']];
    }
    
    // Override user ID if provided in token
    $userId = $auth['userid'];
    
    // Process request through provider API
    $result = $providerAPI->processProviderRequest($providerId, $action, $requestData, $userId);
    
    return $result;
}

// Main API endpoint
if (php_sapi_name() !== 'cli') {
    header('Content-Type: application/json');
    
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);
    
    if (json_last_error() !== JSON_ERROR_NONE) {
        echo json_encode(['status' => 'fail', 'message' => 'Invalid JSON input']);
        exit;
    }
    
    $action = $data['action'] ?? null;
    $providerId = $data['provider_id'] ?? null;
    $requestData = $data['data'] ?? [];
    $token = $data['token'] ?? ($_SERVER['HTTP_X_API_KEY'] ?? null);
    $userId = $data['user_id'] ?? null;
    
    if (!$action || !$providerId) {
        echo json_encode(['status' => 'fail', 'message' => 'Missing required parameters: action and provider_id']);
        exit;
    }
    
    try {
        $result = handleProviderRequest($action, $providerId, $requestData, $token, $userId);
        echo json_encode($result);
    } catch (Exception $e) {
        error_log('Provider API error: ' . $e->getMessage());
        echo json_encode([
            'status' => 'fail',
            'message' => 'Internal server error',
            'debug' => getEnv('APP_DEBUG') ? $e->getMessage() : null
        ]);
    }
}

?>
