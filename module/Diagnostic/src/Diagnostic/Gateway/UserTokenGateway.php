<?php
namespace Diagnostic\Gateway;

/**
 * User Token Gateway
 *
 * @package Diagnostic\Gateway
 * @author Jerome De Almeida <jerome.dealmeida@vesperiagroup.com>
 */
class UserTokenGateway extends AbstractGateway
{
    /**
     * Get By Token
     *
     * @return null|\Zend\Db\ResultSet\ResultSetInterface
     */
    public function getByToken($token)
    {

        $select = $this->tableGateway
            ->getSql()
            ->select()
            ->where(['token = ?' => $token]);

        $resultSet = $this->tableGateway->selectWith($select);

        return $resultSet;
    }

    /**
     * Delete
     *
     * @param $token
     */
    public function delete($token)
    {
        $this->tableGateway->delete(['token' => $token]);
    }

    /**
     * Delete Old
     */
    public function deleteOld()
    {
        $query = $this->tableGateway
            ->getSql()
            ->delete()
            ->where(['limit_timestamp < ?' => (int)time()]);

        $this->tableGateway->deleteWith($query);
    }

    /**
     * Delete By Email
     *
     * @param $email
     */
    public function deleteByEmail($email)
    {
        $this->tableGateway->delete(['user_email' => $email]);
    }
}