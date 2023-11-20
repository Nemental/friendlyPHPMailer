<?php

require __DIR__ . '/../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

function sendMail() {
    try {
        $encryptionType     = getenv('SMTP_SECURE') !== false ? getenv('SMTP_SECURE') : 'SMTPS';;
        $encryptionSettings = [
            'SMTPS' => ['method' => PHPMailer::ENCRYPTION_SMTPS, 'port' => '465'],
            'STARTTLS' => ['method' => PHPMailer::ENCRYPTION_STARTTLS, 'port' => '587'],
        ];

        $mail = new PHPMailer(true);

        $mail->isSMTP();
        $mail->Host       = getenv('SMTP_HOST');
        $mail->SMTPAuth   = getenv('SMTP_AUTH');
        $mail->Username   = getenv('SMTP_USERNAME');
        $mail->Password   = getenv('SMTP_PASSWORD');
        $mail->SMTPSecure = $encryptionSettings[$encryptionType]['method'];
        $mail->Port       = $encryptionSettings[$encryptionType]['port'];

        if ($encryptionType === 'SMTPS') {
             $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
             $mail->Port       = '465';
        } else {
             $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
             $mail->Port       = '587';
        };

        $mail->setFrom($mail->Username, getenv('SMTP_FROM'));
        $mail->addAddress(getenv('SMTP_ADDRESS_MAIL'), getenv('SMTP_ADDRESS_NAME'));

        $mail->Subject = getenv('SMTP_SUBJECT') !== false ? getenv('SMTP_SUBJECT') : $_POST['subject'];
        $mail->CharSet = getenv('SMTP_CHARSET') !== false ? getenv('SMTP_CHARSET') : 'UTF-8';

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

        if ($mail->send()) {
            return array(
                'success' => true,
                'message' => 'Email was successfully sent!'
            );
        }
    } catch (Exception $e) {
        return array(
            'success' => false,
            'message' => 'Failed to send email: ' . $e->getMessage()
        );
    }
}
