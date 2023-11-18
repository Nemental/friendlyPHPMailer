<?php

function verifyCaptcha($solution, $apiSecret) {
    $url = $_ENV['FRC_URL'] ?? 'https://api.friendlycaptcha.com/api/v1/siteverify';

    $data = array(
        'solution' => $solution,
        'secret' => $apiSecret
    );

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    try {
        $result = curl_exec($ch);

        if ($result === false) {
            throw new Exception('Fehler bei der Captcha-Überprüfung: ' . curl_error($ch));
        }

        $response = json_decode($result, true);

        if (isset($response['success']) && $response['success'] === true) {
            return array(
                'success' => true,
                'message' => 'Captcha erfolgreich verifiziert'
            );
        } else {
            return array(
                'success' => false,
                'message' => 'Captcha-Überprüfung fehlgeschlagen'
            );
        }
    } catch (Exception $e) {
        return array(
            'success' => false,
            'message' => 'Fehler bei der Captcha-Überprüfung: ' . $e->getMessage()
        );
    } finally {
        curl_close($ch);
    }
}
