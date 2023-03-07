<?php

class MailSender 
{
    protected $fromName         = 'admin';
    
    protected $fromEmail        = 'admin@admina.net';
    
    protected $subject          = '';
    
    protected $body             = '';
    
    protected $adresses         = array();
    
    protected $SMTP             = array();
    

    public function __construct($fromName, $fromEmail, $subject, $body)
    {
        $this->fromName = $fromName;
        $this->fromEmail = $fromEmail;
        $this->subject = $subject;
        $this->body = $body;        
    }

    public function addAddress($email)
    {
        $this->adresses[] = $email;
    }
    
    public function sendMail()
    {        
        if (! empty($this->SMTP)) {
            return $this->sendMailByDemon();
        } else {
            return $this->sendMailByHost();            
        }        
    } 

    public function setSMTP($security, $host, $port, $user, $pass)
    {
        $this->SMTP['auth'] = true;
        $this->SMTP['security']  = $security;
        $this->SMTP['host'] = $host;
        $this->SMTP['port'] = $port;
        $this->SMTP['user'] = $user;
        $this->SMTP['pass'] = $pass;                    
    }

    // yandex каверкает тему и отправителя
    private function adopt($text)
    {
        return '=?UTF-8?B?'.base64_encode($text).'?=';
    }

    private function sendMailByHost()
    {
        //TODO Через SMTP    
        if (! empty($this->fromEmail)) {
            return mail($this->getAdresses(), $this->adopt($this->subject), $this->body, $this->getHeader(), "-f " . $this->fromEmail);
        } else {
            return mail($this->getAdresses(), $this->adopt($this->subject), $this->body, $this->getHeader());
        }
    }
    
    private function sendMailByDemon()
    {
        require_once CFG_APP_ROOT . '/lib/SwiftMailer/swift_required.php';
        $transport = Swift_SmtpTransport::newInstance($this->SMTP['host'], $this->SMTP['port'])
            ->setUsername($this->SMTP['user'])
            ->setPassword($this->SMTP['pass'])
        ;
        if ($this->SMTP['security'] == 'ssl') {
            $transport->setEncryption('ssl');
        }
        if ($this->SMTP['security'] == 'tls') {
            $transport->setEncryption('tls');
        }
        $message = Swift_Message::newInstance()
            ->setCharset('utf-8')
            ->setContentType('text/html')
            ->setSubject($this->subject)
            ->setFrom(array($this->fromEmail => $this->fromName))
            ->setReturnPath($this->fromEmail)
        ;
        foreach ($this->adresses as $item) {
            $message->addTo($item);
        }
        $message->setBody($this->body);
        
        $mailer = Swift_Mailer::newInstance($transport);
        return $mailer->send($message);
        
    }

    private function getHeader()
    {
        $headers = "MIME-Version: 1.0\r\n";
        $headers .= "Content-type: text/html; charset=utf-8\r\n";
        $headers .= "From: " . $this->adopt($this->fromName) . " <" . $this->fromEmail . ">\r\n";
        $headers .= "Return-Path: " . $this->fromName . "\r\n";
        
        return $headers;
    }
    
    private function getAdresses()
    {
        return implode(", ", $this->adresses);
    }     
}