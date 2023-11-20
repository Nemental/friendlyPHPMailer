<?php

function processForm() {
    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\SMTP;
    use PHPMailer\PHPMailer\Exception;
    
    try {
        $encryptionType     = $_ENV['SMTP_SECURE'] ?? 'SMTPS';
        $encryptionSettings = [
            'SMTPS' => ['method' => PHPMailer::ENCRYPTION_SMTPS, 'port' => '465'],
            'STARTTLS' => ['method' => PHPMailer::ENCRYPTION_STARTTLS, 'port' => '587'],
        ];

        $mail = new PHPMailer(true);
    
        $mail->isSMTP();
        $mail->Host       = $_ENV['SMTP_HOST'];
        $mail->SMTPAuth   = $_ENV['SMTP_AUTH'];
        $mail->Username   = $_ENV['SMTP_USERNAME'];
        $mail->Password   = $_ENV['SMTP_PASSWORD'];
        $mail->SMTPSecure = $encryptionSettings[$encryptionType]['method'];
        $mail->Port       = $encryptionSettings[$encryptionType]['port'];

        if ($encryption === 'SMTPS') {
             $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
             $mail->Port       = '465';
        } else {
             $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
             $mail->Port       = '587';
        };
    
        $mail->setFrom($mail->Username, $_ENV['SMTP_FROM']);
        $mail->addAddress($_ENV['SMTP_ADDRESS_MAIL'], $_ENV['SMTP_ADDRESS_NAME']);
    
        $mail->Subject = $_ENV['SMTP_SUBJECT'] ?? $_POST['subject'];
        $mail->CharSet = $_ENV['SMTP_CHARSET'] ?? 'UTF-8';
    
        $contactData = '';
        $skipKeys = ['frc-captcha-solution'];
        foreach ($_POST as $key => $value) {
            if (in_array($key, $skipKeys)) {
                continue;
            }
    
            if (is_array($value)) {
                $value = implode(', ', $value);
            }
            $contactData .= $key . ":\n" . $value . "\n\n";
        }
        $mail->Body = $contactData;
    
        $solution = $_POST['frc-captcha-solution'];
    
        $apiSecret = $_ENV['FRC_API_SECRET'];
    
        $verificationResult = verifyCaptcha($solution, $apiSecret);
    
        if ($verificationResult['success']) {
            $response = array();
    
            if ($mail->send()) {
                $response['success'] = true;
                $response['message'] = 'E-Mail erfolgreich versendet';
            } else {
                $response['success'] = false;
                $response['message'] = 'Fehler beim Versenden der E-Mail';
            }
        } else {
            $response = array(
                'success' => false,
                'message' => 'Captcha-Überprüfung fehlgeschlagen'
            );
        }
    } catch (Exception $e) {
        $response['success'] = false;
        $response['message'] = 'Fehler beim Versenden der E-Mail: ' . $e->getMessage();
    } finally {
        header('Content-Type: application/json');
        echo json_encode($response);
    }
}
