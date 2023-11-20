<?php

session_start();

require __DIR__ . '/vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

$response = ['success' => false, 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $response['message'] = 'Invalid request method!';
    handleResponse($response);
} else {
    $skipCsrf = filter_var(getenv('SKIP_CSRF'), FILTER_VALIDATE_BOOLEAN);
    $skipCaptcha = filter_var(getenv('SKIP_CAPTCHA'), FILTER_VALIDATE_BOOLEAN);

    $validateCsrf = false;
    $validateCaptcha = false;

    if (!$skipCsrf) {
        require __DIR__ . '/includes/validateCsrf.php';
        $validateCsrf = validateCsrf();

        if (!$validateCsrf['success']) {
            handleResponse($validateCsrf);
        }
    }

    if (!$skipCaptcha) {
        require __DIR__ . '/includes/validateCaptcha.php';
        $validateCaptcha = validateCaptcha();

        if (!$validateCaptcha['success']) {
            handleResponse($validateCaptcha);
        }
    }

    require __DIR__ . '/includes/sendMail.php';
    handleResponse(sendMail());
}

function handleResponse($response) {
    header('Content-Type: application/json');
    echo json_encode($response);
    exit();
}
