<?php
namespace Diagnostic\Service;
use Diagnostic\Gateway\UserTokenGateway;

/**
 * UserTokenService
 *
 * @package Diagnostic\Service
 * @author Jerome De Almeida <jerome.dealmeida@vesperiagroup.com>
 */
class UserTokenService extends AbstractService
{
    /**
     * Save Entity
     *
     * @return \Zend\Db\ResultSet\ResultSet
     * @throws \Exception
     */
    public function saveEntity($email)
    {
        //record token
        $userToken = [
            'userEmail' => $email,
            'token' => uniqid(bin2hex(openssl_random_pseudo_bytes(rand(20, 40))), true),
            'limitTimestamp' => time() + (24 * 60 * 60),
        ];

        //set user information
        $userTokenEntity = $this->get('entity');
        $userTokenEntity->exchangeArray($userToken);

        //insert user information in db
        /** @var UserTokenGateway $userTokenGateway */
        $userTokenGateway = $this->get('gateway');
        $userTokenGateway->insert($userTokenEntity);

        //delete old tokens
        $userTokenGateway->deleteOld();

        return $userTokenEntity;
    }

    /**
     * Get By Token
     *
     * @param $token
     * @return null|\Zend\Db\ResultSet\ResultSetInterface
     */
    public function getByToken($token)
    {
        /** @var UserTokenGateway $userTokenGateway */
        $userTokenGateway = $this->get('gateway');
        $userTokenEntity = $userTokenGateway->getByToken($token);

        return $userTokenEntity;
    }

    /**
     * Delete
     *
     * @param $token
     */
    public function delete($token)
    {
        /** @var UserTokenGateway $userTokenGateway */
        $userTokenGateway = $this->get('gateway');
        $userTokenGateway->delete($token);
    }

    /**
     * Delete By Email
     *
     * @param $email
     */
    public function deleteByEmail($email)
    {
        /** @var UserTokenGateway $userTokenGateway */
        $userTokenGateway = $this->get('gateway');
        $userTokenGateway->deleteByEmail($email);
    }
}