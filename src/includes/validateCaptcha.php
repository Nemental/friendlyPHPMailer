<?php

function validateCaptcha() {
    $url = getenv('FRC_URL') ?: 'https://api.friendlycaptcha.com/api/v1/siteverify';
    $solutionKey = getenv('FRC_SOLUTION_KEY') ?: 'frc-captcha-solution';
    $apiSecret = getenv('FRC_API_SECRET') ?: '';

    $solution = $_POST[$solutionKey] ?? '';

    $data = [
        'solution' => $solution,
        'secret' => $apiSecret,
    ];

    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => $url,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => http_build_query($data),
        CURLOPT_RETURNTRANSFER => true,
    ]);

    try {
        $result = curl_exec($ch);

        if ($result === false) {
            throw new Exception('Failed to validate captcha: ' . curl_error($ch));
        }

        $response = json_decode($result, true);

        if (!empty($response['success']) && $response['success'] === true) {
            return ['success' => true];
        } else {
            throw new Exception('Failed to validate captcha: ' . implode(', ', $response['errors']));
        }
    } catch (Exception $e) {
        return ['success' => false, 'message' => $e->getMessage()];
    } finally {
        curl_close($ch);
    }
}
