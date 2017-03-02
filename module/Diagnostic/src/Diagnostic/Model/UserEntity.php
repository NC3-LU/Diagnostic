<?php
namespace Diagnostic\Model;

/**
 * User Entity
 *
 * @package Diagnostic\Model
 * @author Jerome De Almeida <jerome.dealmeida@vesperiagroup.com>
 */
class UserEntity
{
    /**
     * Id
     */
    public $id;

    /**
     * Email
     */
    public $email;

    /**
     * Password
     */
    public $password;

    /**
     * Admin
     */
    public $admin;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     * @return UserEntity
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param mixed $email
     * @return UserEntity
     */
    public function setEmail($email)
    {
        $this->email = $email;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param mixed $password
     * @return UserEntity
     */
    public function setPassword($password)
    {
        $this->password = $password;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getAdmin()
    {
        return $this->admin;
    }

    /**
     * @param mixed $admin
     * @return UserEntity
     */
    public function setAdmin($admin)
    {
        $this->admin = $admin;
        return $this;
    }

    /**
     * @param array $data
     */
    public function exchangeArray($data)
    {
        if (is_object($data)) {
            $data = (array)$data;
        }

        //id
        if (isset($data['id'])) {
            $this->id = $data['id'];
        } else {
            $this->id = null;
        }

        $this->email = (isset($data['email'])) ? $data['email'] : null;
        $this->password = (isset($data['password'])) ? $data['password'] : null;
        $this->admin = (isset($data['admin'])) ? $data['admin'] : null;
    }

    /**
     * Get Array Copy
     *
     * @return array
     */
    public function getArrayCopy()
    {
        return get_object_vars($this);
    }
}