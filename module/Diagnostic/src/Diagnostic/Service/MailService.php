<?php
namespace Diagnostic\Service;

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
    public function send($email, $subject, $message) {

        $config = $this->getServiceLocator()->get('Config');

/*
        $html = $this->getServiceLocator()->get('Diagnostic\Service\Mime\Part');
        $html->type = "text/html";

        $body = $this->getServiceLocator()->get('Diagnostic\Service\Mime\message');

        $options = $this->getServiceLocator()->get('Diagnostic\Service\Mail\Transport\SmtpOptions');
        $options->setFromArray($config['smtp']);


        $html->setContent($message);

        $body->setParts(array($html));

        $mailMessage = $this->getServiceLocator()->get('Diagnostic\Service\Mail\Message');
        $mailMessage->addFrom($config['mail'], $config['mail_name'])
            ->addTo($email)
            ->setSubject($subject)
            ->setBody($body);


        //smtp transport
        $transport = $this->getServiceLocator()->get('Diagnostic\Service\Mail\Transport\Smtp');
        $transport->setOptions($options);
        $transport->send($mailMessage);

        //echo $mailMessage->toString();
*/

        $headers  = 'MIME-Version: 1.0' . "\r\n";
        $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
        $headers .= 'To: ' . $email . "\r\n";
        $headers .= 'From: ' . $config['mail_name'] . '<' . $config['mail'] . '>' . "\r\n";

        // Envoi
        mail($email, $subject, $message, $headers);
    }

}