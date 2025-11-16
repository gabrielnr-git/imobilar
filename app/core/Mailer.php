<?php

namespace Core;

if (!defined("ROOTPATH")) die("Access Denied");

require_once "PHPMailer/vendor/autoload.php";

use \PHPMailer\PHPMailer\PHPMailer;
use \PHPMailer\PHPMailer\Exception;

/**
 * Mailer class
 */
class Mailer
{
    private $mailer;

    public function __construct()
    {
        $this->mailer = new PHPMailer(true);

        // Additional configuration settings can be added here
        $this->mailer->SMTPDebug = 0;
        $this->mailer->isSMTP();
        $this->mailer->Host = SMTPHOST;
        $this->mailer->SMTPAuth = true;
        $this->mailer->Username = SMTPUSERNAME;
        $this->mailer->Password = SMTPPASSWORD;
        $this->mailer->SMTPSecure = SMTPCONNECTION;
        $this->mailer->Port = SMTPPORT;
    }

    public function sendMail($to, $subject, $body)
    {
        try {
            $this->mailer->setFrom(SMTPUSERNAME, 'Imobilar');
            $this->mailer->addAddress($to);
            $this->mailer->isHTML(true);
            $this->mailer->Subject = $subject;
            $this->mailer->Body = $body;

            $this->mailer->send();
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    public function sendCode($to, $subject, $code)
    {
        try {
            $this->mailer->setFrom(SMTPUSERNAME, 'Imobilar');
            $this->mailer->addAddress($to);
            $this->mailer->isHTML(true);
            $this->mailer->addEmbeddedImage("assets/images/logo.png","logo");
            $this->mailer->CharSet = "UTF-8";
            $this->mailer->Subject = $subject;
            $this->mailer->Body = "
                <html lang='pt-br'>
                <head>
                    <meta charset='UTF-8'>
                </head>
                <body style='width: 100vw; height: fit-content; background-color: #eeeeee; margin: 0; padding: 0; font-family: monospace;'>
                    <main style='width: 100%; height: 100%;margin: 0; padding: 0;'>
                        <table style='width: 100%;height: fit-content;margin: 0;padding: 50px 0;background: linear-gradient(90deg, rgba(81,75,219,1) 50%, rgba(21,67,96,1) 100%);'>
                            <tr style='width: 100%;height: fit-content;text-align: center;'>
                                <td><img src='cid:logo' alt='logo-imobilar' style='width: 30vh;'></td>
                            </tr>
                        </table>
                        <table class='code' style='width: 100%;height: fit-content;background-color: white;margin: 0;padding: 50px 0;color: rgba(0, 0, 0, 0.8);border-bottom: 2px solid #154360;border-top: 2px solid #154360;'>
                            <tr style='width: 100%;height: fit-content;text-align: center;'>
                                <td style='width: 100%;height: fit-content;text-align: center;color: #154360;font-size: 1.7rem;'>SEU CÓDIGO É: </td>
                            </tr>
                            <tr style='width: 100%;height: fit-content;text-align: center;'>
                                <td style='width: 100%;height: fit-content;text-align: center;font-size: 1.6rem;'>".$code."</td>
                            </tr>
                        </table>
                    </main>
                </body>
                </html>
            ";

            $this->mailer->send();
            return true;
        } catch (Exception $e) {
            return false;
        }
    }
}
