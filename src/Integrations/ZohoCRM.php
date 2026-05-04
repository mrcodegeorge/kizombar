<?php

class ZohoCRM {
    private $config;
    private $access_token;

    public function __construct() {
        $this->config = require __DIR__ . '/../../config/zoho_config.php';
    }

    private function logError($message) {
        $logFile = __DIR__ . '/../../logs/zoho_errors.log';
        if (!is_dir(dirname($logFile))) {
            mkdir(dirname($logFile), 0777, true);
        }
        $timestamp = date('Y-m-d H:i:s');
        file_put_contents($logFile, "[$timestamp] $message\n", FILE_APPEND);
    }

    private function getAccessToken() {
        if ($this->access_token) {
            return $this->access_token;
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->config['accounts_url']);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
            'refresh_token' => $this->config['refresh_token'],
            'client_id' => $this->config['client_id'],
            'client_secret' => $this->config['client_secret'],
            'grant_type' => 'refresh_token'
        ]));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        // Extremely short timeout to prevent UI blocking
        curl_setopt($ch, CURLOPT_TIMEOUT, 3);

        $response = curl_exec($ch);
        $error = curl_error($ch);
        curl_close($ch);

        if ($error) {
            $this->logError("OAuth cURL Error: $error");
            return false;
        }

        $data = json_decode($response, true);
        if (isset($data['access_token'])) {
            $this->access_token = $data['access_token'];
            return $this->access_token;
        } else {
            $this->logError("OAuth Error Response: $response");
            return false;
        }
    }

    private function makeApiRequest($endpoint, $method = 'POST', $payload = []) {
        $token = $this->getAccessToken();
        if (!$token) {
            return false;
        }

        $url = $this->config['api_domain'] . '/crm/v2/' . $endpoint;
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5); // 5 seconds max
        
        $headers = [
            'Authorization: Zoho-oauthtoken ' . $token,
            'Content-Type: application/json'
        ];
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        if ($method === 'POST' || $method === 'PUT') {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        }

        $response = curl_exec($ch);
        $error = curl_error($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($error) {
            $this->logError("API cURL Error on $endpoint: $error");
            return false;
        }

        if ($this->config['debug_mode']) {
            $this->logError("API Response [$httpCode] on $endpoint: $response");
        }

        return json_decode($response, true);
    }

    public function createRecord($moduleName, $fields) {
        $payload = [
            'data' => [$fields]
        ];
        return $this->makeApiRequest($moduleName, 'POST', $payload);
    }

    public function createTask($subject, $description, $extraFields = []) {
        $fields = array_merge([
            'Subject' => $subject,
            'Description' => $description,
            '$se_module' => 'Tasks' // Required by Zoho for task differentiation sometimes, though module is usually sufficient
        ], $extraFields);

        return $this->createRecord('Tasks', $fields);
    }

    public function logSOPCompletion($sopName, $staffName, $status) {
        $subject = "SOP Completed: $sopName";
        $date = date('Y-m-d H:i:s');
        
        $description = "Staff: $staffName\nStatus: $status\nDate: $date";
        
        return $this->createTask($subject, $description);
    }
}
