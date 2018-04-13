<?php

namespace KresdStatsZabbix\Application\Logger;

use PHPMailer\PHPMailer\PHPMailer;

abstract class AbstractMailLogger
{

    protected $mailBody;

    /**
     * Add message to email body
     * @param $message
     * @return mixed
     */
    abstract protected function addMailBody(string $message);

    public function __construct()
    {
        $this->mailBody = false;
    }

    public function isMail()
    {
        return ($this->mailBody ? true : false);
    }

    public function send($emailFrom, $emailTo)
    {
        $mail = new PHPMailer (true);
        try {
            $host = trim(gethostname());
            $mail->CharSet = "UTF-8";
            $mail->setFrom($emailFrom);
            $mail->addAddress($emailTo);
            $mail->isHTML(true);
            $mail->Subject = 'Knot statistics for zabbix: ERROR: (host: ' . $host .')';
            $mail->Body = str_replace("\n", '<br />', $this->mailBody);
            $mail->AltBody = $this->mailBody;
            $mail->send();
            return true;
        }
        catch (\Exception $exception) {
            echo 'Message could not be sent. Mailer Error: ', $mail->ErrorInfo;
            return false;
        }
    }
}

