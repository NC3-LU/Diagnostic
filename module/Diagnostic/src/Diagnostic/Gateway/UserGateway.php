<?php
namespace Diagnostic\Gateway;

/**
 * User Gateway
 *
 * @package Diagnostic\Gateway
 * @author Jerome De Almeida <jerome.dealmeida@vesperiagroup.com>
 */
class UserGateway extends AbstractGateway
{
    /**
     * Get User By Email
     *
     * @return null|\Zend\Db\ResultSet\ResultSetInterface
     */
    public function getUserByEmail($email){

        $select = $this->tableGateway
            ->getSql()
            ->select()
            ->where(['email = ?' => $email]);

        $resultSet = $this->tableGateway->selectWith($select);

        return $resultSet;
    }

    /**
     * Get user by id
     *
     * @param $id
     * @return null|\Zend\Db\ResultSet\ResultSetInterface
     */
    public function getUserById($id){

        $select = $this->tableGateway
            ->getSql()
            ->select()
            ->where(['id = ?' => $id]);

        $resultSet = $this->tableGateway->selectWith($select);

        return $resultSet;
    }

    /**
     * Get Users
     *
     * @return null|\Zend\Db\ResultSet\ResultSetInterface
     */
    public function getUsers(){

        $select = $this->tableGateway
            ->getSql()
            ->select();

        $resultSet = $this->tableGateway->selectWith($select);

        return $resultSet;
    }

    /**
     * Update Password
     *
     * @param $email
     * @param $password
     */
    public function updatePassword($email, $password)
    {
        $this->tableGateway->update(array('password' => $password), array('email' => $email));
    }

    /**
     * Update
     *
     * @param $id
     * @param $data
     */
    public function update($id, $data)
    {
        $array = [
            'email' => $data['email']
        ];
        if (array_key_exists('admin', $data)) {
            $array['admin'] = $data['admin'];
        }
        $this->tableGateway->update($array, array('id' => $id));
    }
}