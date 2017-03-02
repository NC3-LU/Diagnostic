<?php
namespace Diagnostic\Service;

use Zend\Crypt\Password\Bcrypt;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;
use Zend\Session\Container;

/**
 * UserService
 *
 * @package Diagnostic\Service
 * @author Jerome De Almeida <jerome.dealmeida@vesperiagroup.com>
 */
class UserService implements ServiceLocatorAwareInterface
{
    use ServiceLocatorAwareTrait;

    /**
     * Get user by email
     *
     * @return \Zend\Db\ResultSet\ResultSet
     * @throws \Exception
     */
    public function getUserByEmail($email)
    {
        if ($this->getServiceLocator()->has('Diagnostic\Gateway\UserGateway')) {

            $tableGateway = $this->getServiceLocator()->get('Diagnostic\Gateway\UserGateway');

            return $tableGateway->getUserByEmail($email);

        } else {
            throw new \Exception('User Gateway not found');
        }
    }

    /**
     * Get users
     *
     * @return \Zend\Db\ResultSet\ResultSet
     * @throws \Exception
     */
    public function getUsers()
    {
        if ($this->getServiceLocator()->has('Diagnostic\Gateway\UserGateway')) {

            $tableGateway = $this->getServiceLocator()->get('Diagnostic\Gateway\UserGateway');

            return $tableGateway->getUsers();
        }
    }

    /**
     * Get user by id
     *
     * @return \Zend\Db\ResultSet\ResultSet
     * @throws \Exception
     */
    public function getUserById($id)
    {
        if ($this->getServiceLocator()->has('Diagnostic\Gateway\UserGateway')) {

            $tableGateway = $this->getServiceLocator()->get('Diagnostic\Gateway\UserGateway');

            return $tableGateway->getUserById($id);
        }
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
        $userGateway = $this->getServiceLocator()->get('Diagnostic\Gateway\UserGateway');
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
        $userGateway = $this->getServiceLocator()->get('Diagnostic\Gateway\UserGateway');
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
        $userGateway = $this->getServiceLocator()->get('Diagnostic\Gateway\UserGateway');
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
        $userGateway = $this->getServiceLocator()->get('Diagnostic\Gateway\UserGateway');
        $userGateway->delete($id);
    }
}