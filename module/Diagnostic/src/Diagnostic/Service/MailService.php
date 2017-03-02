<?php
namespace Diagnostic\Service;

use Zend\Mail\Message;
use Zend\Mail\Transport\Sendmail;
use Zend\Mime\Part;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;

/**
 * MailService
 *
 * @package Diagnostic\Service
 * @author Jerome De Almeida <jerome.dealmeida@vesperiagroup.com>
 */
class MailService implements ServiceLocatorAwareInterface
{
    use ServiceLocatorAwareTrait;

    /**
     * Send
     *
     * @return \Zend\Db\ResultSet\ResultSet
     * @throws \Exception
     */
    public function send($email, $subject, $message)
    {

        $config = $this->getServiceLocator()->get('Config');

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