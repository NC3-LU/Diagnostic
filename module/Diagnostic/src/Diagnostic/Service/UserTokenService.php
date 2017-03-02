<?php
namespace Diagnostic\Service;

use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;

/**
 * UserTokenService
 *
 * @package Diagnostic\Service
 * @author Jerome De Almeida <jerome.dealmeida@vesperiagroup.com>
 */
class UserTokenService implements ServiceLocatorAwareInterface
{
    use ServiceLocatorAwareTrait;

    /**
     * Save Entity
     *
     * @return \Zend\Db\ResultSet\ResultSet
     * @throws \Exception
     */
    public function saveEntity($email) {

        //record token
        $userToken = [
            'userEmail' => $email,
            'token' => uniqid(bin2hex(openssl_random_pseudo_bytes(rand(20, 40))), true),
            'limitTimestamp' => time() + (24 * 60 * 60),
        ];

        //set user information
        $userTokenEntity = $this->getServiceLocator()->get('Diagnostic\Model\UserTokenEntity');
        $userTokenEntity->exchangeArray($userToken);

        //insert user information in db
        $userTokenGateway = $this->getServiceLocator()->get('Diagnostic\Gateway\UserTokenGateway');
        $userTokenGateway->insert($userTokenEntity);

        //delete old tokens
        $userTokenGateway->deleteOld();

        return $userTokenEntity;
    }

    /**
     * Get by token
     *
     * @param $token
     */
    public function getByToken($token) {
        $userTokenGateway = $this->getServiceLocator()->get('Diagnostic\Gateway\UserTokenGateway');
        $userTokenEntity = $userTokenGateway->getByToken($token);

        return $userTokenEntity;
    }

    /**
     * Delete
     *
     * @param $token
     */
    public function delete($token) {
        $userTokenGateway = $this->getServiceLocator()->get('Diagnostic\Gateway\UserTokenGateway');
        $userTokenGateway->delete($token);
    }

    /**
     * Delete By Email
     *
     * @param $email
     */
    public function deleteByEmail($email) {
        $userTokenGateway = $this->getServiceLocator()->get('Diagnostic\Gateway\UserTokenGateway');
        $userTokenGateway->deleteByEmail($email);

    }
}