<?php
namespace Diagnostic\Service;

use Diagnostic\Gateway\UserGateway;
use Zend\Crypt\Password\Bcrypt;
use Zend\Session\Container;

/**
 * UserService
 *
 * @package Diagnostic\Service
 * @author Jerome De Almeida <jerome.dealmeida@vesperiagroup.com>
 */
class UserService extends AbstractService
{

    /**
     * Get user by email
     *
     * @return \Zend\Db\ResultSet\ResultSet
     * @throws \Exception
     */
    public function getUserByEmail($email)
    {
        /** @var UserGateway $tableGateway */
        $tableGateway = $this->get('gateway');

        return $tableGateway->getUserByEmail($email);
    }

    /**
     * Get users
     *
     * @return \Zend\Db\ResultSet\ResultSet
     * @throws \Exception
     */
    public function getUsers()
    {
        /** @var UserGateway $tableGateway */
        $tableGateway = $this->get('gateway');

        return $tableGateway->getUsers();
    }

    /**
     * Get user by id
     *
     * @return \Zend\Db\ResultSet\ResultSet
     * @throws \Exception
     */
    public function getUserById($id)
    {
        /** @var UserGateway $tableGateway */
        $tableGateway = $this->get('gateway');

        return $tableGateway->getUserById($id);
    }

    /**
     * Update password
     *
     * @param $email
     * @param $password
     */
    public function updatePassword($email, $password)
    {
        //encryption
        $bcrypt = new Bcrypt();
        $passwordCrypt = $bcrypt->create($password);

        //update password
        /** @var UserGateway $tableGateway */
        $userGateway = $this->get('gateway');
        $userGateway->updatePassword($email, $passwordCrypt);
    }

    /**
     * Update
     *
     * @param $id
     * @param $data
     */
    public function update($id, $data)
    {
        unset($data['id']);
        unset($data['password']);

        /** @var UserGateway $tableGateway */
        $userGateway = $this->get('gateway');
        $userGateway->update($id, $data);
    }

    /**
     * Create
     *
     * @param $data
     */
    public function create($data)
    {
        $data['password'] = '';

        /** @var UserGateway $tableGateway */
        $userGateway = $this->get('gateway');
        $userGateway->insert($data);
    }

    /**
     * Is Admin
     *
     * @return mixed
     */
    public function isAdmin()
    {
        $container = new Container('user');
        $admin = $container->admin;
        return $admin;
    }

    /**
     * Delete
     *
     * @param $id
     */
    public function delete($id)
    {
        /** @var UserGateway $tableGateway */
        $userGateway = $this->get('gateway');
        $userGateway->delete($id);
    }
}