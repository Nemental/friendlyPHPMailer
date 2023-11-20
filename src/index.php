<?php

session_start();

require __DIR__ . '/vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

$response = ['success' => false, 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $skipCsrf = filter_var(getenv('CSRF_SKIP'), FILTER_VALIDATE_BOOLEAN);
    $validateCsrfToken = false;

    if (!$skipCsrf) {
        require __DIR__ . '/includes/validateCsrfToken.php';
        $validateCsrfToken = validateCsrfToken();
    }

    if ($validateCsrfToken || $skipCsrf) {
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
