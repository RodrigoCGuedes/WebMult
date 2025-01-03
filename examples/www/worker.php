<?php
#worker.php

header('Content-Type: application/json'); // Confirms the content type as JSON

// Adds CORS headers
header('Access-Control-Allow-Origin: *'); // Allows requests from any origin
header('Access-Control-Allow-Methods: GET, POST, OPTIONS'); // Allows GET, POST, and OPTIONS methods
header('Access-Control-Allow-Headers: Content-Type'); // Allows the Content-Type header

// Captures parameters sent via the query string
$apiKey = $_GET['api_key'] ?? null;
$user = $_GET['user'] ?? null;

// Simple API key validation
if ($apiKey !== '123456') {
    echo json_encode([
        'status' => 'error',
        'message' => 'Invalid API Key',
    ]);
    exit;
}

// Simulated response
echo json_encode([
    'status' => 'success',
    'message' => 'Hello, ' . ($user ?? 'Guest'),
    'time' => date('Y-m-d H:i:s'),
]);