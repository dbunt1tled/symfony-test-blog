<?php
/**
 * Created by PhpStorm.
 * User: sid
 * Date: 18.07.18
 * Time: 12:04
 */

namespace App\Services\Mailer;

use Psr\Log\LoggerInterface;

class Emailer
{
    private $myparam;
    private $logger;
    private $mailer;
    private $message = null;

    /**
     * Emailer constructor.
     *
     * @param string          $myparam
     * @param \Swift_Mailer   $mailer
     * @param LoggerInterface $logger
     */
    public function __construct(string $myparam,\Swift_Mailer $mailer,  LoggerInterface $logger)
    {
        $this->myparam = $myparam;
        $this->mailer = $mailer;
        $this->logger = $logger;

        //$this->logger->info('Create Emailer');
        //$this->logger->debug($this->myparam);
        //dump($this->myparam);
    }
    public function create($from,$to,$subject)
    {
        $this->message = (new \Swift_Message($subject))
            ->setFrom($from)
            ->setTo($to);
    }
    public function sendMessage(string $text)
    {
        if(!($this->message instanceof \Swift_Message)){
            throw new \DomainException('Email data not initialized');
        }
        $this->message->setBody($text,'text/plain');
        return $this->mailer->send($this->message);

    }
}