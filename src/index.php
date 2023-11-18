<?php

session_start();

require __DIR__ . '/vendor/autoload.php';

$response = ['success' => false, 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require __DIR__ . '/includes/validateCsrfToken.php';

    if (validateCsrfToken()) {
        $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
        $dotenv->load();

        require __DIR__ . '/includes/verifyCaptcha.php';
        require __DIR__ . '/includes/processForm.php';

        processForm();
    } else {
        $response['success'] = false;
        $response['message'] = 'Invalid CSRF token!';
    }
} else {
    $response['success'] = false;
    $response['message'] = 'Invalid request method!';
}

header('Content-Type: application/json');
echo json_encode($response);
