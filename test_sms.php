<?php
require_once 'sms.php';
require_once 'config.php';

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Set SSL configuration
ini_set('curl.cainfo', __DIR__ . '/cacert.pem');

echo "=== SMS Test Starting ===\n";
echo "Username: " . AT_USERNAME . "\n";
echo "Sender ID: " . AT_SENDER_ID . "\n";
echo "SSL Certificate: " . __DIR__ . '/cacert.pem' . "\n";

try {
    // Create SMS service
    $smsService = new SmsService();

    // Test phone number
    $testPhone = "+250873158697";
    echo "Testing with phone: " . $testPhone . "\n";

    // Send test message
    $message = "This is a test message from F&I Trading Platform";
    echo "Sending message: " . $message . "\n";

    $result = $smsService->sendSMS($message, $testPhone);
    echo "\nSMS Test Result:\n";
    print_r($result);
} catch (Exception $e) {
    echo "\nError occurred:\n";
    echo $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
} 