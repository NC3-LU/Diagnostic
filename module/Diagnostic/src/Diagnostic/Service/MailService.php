<?php
namespace Diagnostic\Service;

use Zend\Mail\Message;
use Zend\Mail\Transport\Sendmail;
use Zend\Mime\Part;

/**
 * MailService
 *
 * @package Diagnostic\Service
 * @author Jerome De Almeida <jerome.dealmeida@vesperiagroup.com>
 */
class MailService extends AbstractService
{
    protected $config;

    /**
     * Send
     *
     * @return \Zend\Db\ResultSet\ResultSet
     * @throws \Exception
     */
    public function send($email, $subject, $message)
    {

        $config = $this->get('config');

        $html = new Part($message);
        $html->type = "text/html";

        $body = new \Zend\Mime\Message();
        $body->setParts([$html]);

        $message = new Message();
        $message->setBody($body);
        $message->setFrom($config['mail'], $config['mail_name']);
        $message->addTo($email, $email);
        $message->setSubject($subject);

        $transport = new Sendmail();
        $transport->send($message);
    }

}