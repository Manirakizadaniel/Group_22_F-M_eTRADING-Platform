<?php
require_once 'config.php';

class SmsService {
    private $username;
    private $apiKey;
    private $senderId;

    public function __construct() {
        if (empty(AT_USERNAME) || empty(AT_API_KEY)) {
            throw new Exception("Africa's Talking credentials are not properly configured");
        }

        $this->username = AT_USERNAME;
        $this->apiKey = AT_API_KEY;
        $this->senderId = AT_SENDER_ID;
        
        error_log("SMS Service initialized successfully with username: " . $this->username);
    }

    public function sendSMS($message, $recipientPhone) {
        try {
            // Validate inputs
            if (empty($recipientPhone)) {
                throw new Exception("Phone number is required");
            }
            if (empty($message)) {
                throw new Exception("Message cannot be empty");
            }

            // Ensure phone number has country code
            if (!str_starts_with($recipientPhone, '+')) {
                $recipientPhone = '+250' . ltrim($recipientPhone, '0');
            }

            // Log attempt
            error_log("=== SMS Sending Attempt ===");
            error_log("To: " . $recipientPhone);
            error_log("From: " . $this->senderId);
            error_log("Message: " . $message);

            // Prepare data
            $data = [
                'username' => $this->username,
                'to' => $recipientPhone,
                'message' => $message
            ];

            if (!empty($this->senderId)) {
                $data['from'] = $this->senderId;
            }

            // Initialize cURL
            $ch = curl_init();
            
            // Set cURL options
            curl_setopt($ch, CURLOPT_URL, 'https://api.sandbox.africastalking.com/version1/messaging');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Accept: application/json',
                'Content-Type: application/x-www-form-urlencoded',
                'apikey: ' . $this->apiKey
            ]);
            
            // Disable SSL verification for testing
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

            // Execute request
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $error = curl_error($ch);
            
            curl_close($ch);

            // Log response
            error_log("=== SMS Send Result ===");
            error_log("HTTP Code: " . $httpCode);
            error_log("Response: " . $response);
            
            if ($error) {
                error_log("cURL Error: " . $error);
                throw new Exception("Failed to send SMS: " . $error);
            }

            // Parse response
            $result = json_decode($response, true);
            
            if ($httpCode >= 200 && $httpCode < 300) {
                error_log("SMS sent successfully!");
                return $result;
            } else {
                error_log("SMS send failed with HTTP code: " . $httpCode);
                throw new Exception("Failed to send SMS. HTTP Code: " . $httpCode);
            }
        } catch (Exception $e) {
            error_log("=== SMS Error ===");
            error_log("Error message: " . $e->getMessage());
            throw $e;
        }
    }
}
