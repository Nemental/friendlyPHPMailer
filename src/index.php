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

    $validateCsrfToken = false;
    $verifyCaptcha = false;

    if (!$skipCsrf) {
        require __DIR__ . '/includes/validateCsrfToken.php';
        $validateCsrfToken = validateCsrfToken();

        if (!$validateCsrfToken['success']) {
            handleResponse($validateCsrfToken);
        }
    }

    if (!$skipCaptcha) {
        require __DIR__ . '/includes/verifyCaptcha.php';
        $verifyCaptcha = verifyCaptcha();

        if (!$verificationResult['success']) {
            handleResponse($verifyCaptcha);
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
