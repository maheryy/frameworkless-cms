<?php

namespace App\Core\Utils;

use App\Lib\Vendor\PHPMailer\PHPMailer\Src\PHPMailer;
use App\Lib\Vendor\PHPMailer\PHPMailer\Src\SMTP;
use App\Lib\Vendor\PHPMailer\PHPMailer\Src\Exception;
use App\Core\Utils\Repository;

class Mailer
{
    public static function connect(string $host, int $port, string $user, string $password) {
        $mailer = new PHPMailer(true);
        $mailer->isSMTP();
        $mailer->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mailer->SMTPAuth = true;
        $mailer->Host = $host;
        $mailer->Port = $port;
        $mailer->Username = $user;
        $mailer->Password = $password;
        $mailer->CharSet = 'UTF-8';

        try {
            $is_valid = $mailer->smtpConnect();
        } catch (Exception $e) {
            $is_valid = false;
        }
        return $is_valid;
    }

    public static function send(array $data, $debug = false)
    {
        $data['to'] = !isset($data['to']) ? [] : (is_array($data['to']) ? $data['to'] : [$data['to']]);
        $data['cc'] = !isset($data['cc']) ? [] : (is_array($data['cc']) ? $data['cc'] : [$data['cc']]);
        $data['bcc'] = !isset($data['bcc']) ? [] : (is_array($data['bcc']) ? $data['bcc'] : [$data['bcc']]);
        $data['attachment'] = !isset($data['attachment']) ? [] : (is_array($data['attachment']) ? $data['attachment'] : [$data['attachment']]);
        $data['multiple'] = $data['multiple'] ?? false;

        $mailer = new PHPMailer(true);
        $mailer->isSMTP();
        $mailer->setLanguage('fr', '../vendor/phpmailer/phpmailer/language/');
        $mailer->SMTPDebug = $debug ? SMTP::DEBUG_SERVER : SMTP::DEBUG_OFF;
        $mailer->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mailer->SMTPAuth = true;
        $mailer->Host = SMTP_HOST;
        $mailer->Port = SMTP_PORT;
        $mailer->Username = SMTP_USERNAME;
        $mailer->Password = SMTP_PASSWORD;
        $mailer->isHTML(true);
        $mailer->CharSet = 'UTF-8';

        $res = [];
        try {
            if (empty($data['to']))
                throw new Exception($mailer->ErrorInfo = 'Vous devez fournir au moins une adresse de destinataire.');
            if (empty($data['subject']))
                throw new Exception($mailer->ErrorInfo = 'Le sujet ne peut pas être vide.');
            if (empty($data['content']))
                throw new Exception($mailer->ErrorInfo = 'Le corps du message ne peut pas être vide.');

            $mailer->setFrom('from@example.com', 'Mailer');
            $mailer->Subject = $data['subject'];
            $mailer->Body = $data['content'];

            $user_repository = (new Repository)->user;
            foreach ($data['cc'] as $uid) {
                if (is_numeric($uid)) {
                    $email = $user_repository->find($uid)['email'];
                    $mailer->addCC($email);
                } else {
                    $mailer->addCC($uid);
                }
            }

            foreach ($data['bcc'] as $uid) {
                if (is_numeric($uid)) {
                    $email = $user_repository->find($uid)['email'];
                    $mailer->addBCC($email);
                } else {
                    $mailer->addBCC($uid);
                }
            }

            foreach ($data['attachment'] as $file_path) {
                $mailer->addAttachment($file_path);
            }

            foreach ($data['to'] as $index => $uid) {
                if (is_numeric($uid)) {
                    $email = $user_repository->find($uid)['email'];
                    $mailer->addAddress($email);
                } else {
                    $mailer->addAddress($uid);
                }

                if ($data['multiple']) {
                    $mailer->send();

                    if (!isset($data['to'][$index + 1])) {
                        $res = [
                            'success' => true,
                            'message' => "Email envoyé avec succès"
                        ];
                    }
                    $mailer->clearAddresses();
                }
            }

            if (!$data['multiple']) {
                $mailer->send();
                $res = [
                    'success' => true,
                    'message' => "Email envoyé avec succès"
                ];
            }
        } catch (Exception $e) {
            $res = [
                'success' => false,
                'message' => "L'envoi a échoué : {$mailer->ErrorInfo}"
            ];
        }

        return $res;
    }

}
