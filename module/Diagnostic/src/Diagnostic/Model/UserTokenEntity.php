<?php
namespace Diagnostic\Model;

/**
 * User Token Entity
 *
 * @package Diagnostic\Model
 * @author Jerome De Almeida <jerome.dealmeida@vesperiagroup.com>
 */
class UserTokenEntity
{
    /**
     * Id
     */
    public $id;

    /**
     * Email
     */
    public $user_email;

    /**
     * Token
     */
    public $token;

    /**
     * Limit Timestamp
     */
    public $limit_timestamp;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     * @return UserTokenEntity
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getUserEmail()
    {
        return $this->user_email;
    }

    /**
     * @param mixed $user_email
     * @return UserTokenEntity
     */
    public function setUserEmail($user_email)
    {
        $this->user_email = $user_email;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * @param mixed $token
     * @return UserTokenEntity
     */
    public function setToken($token)
    {
        $this->token = $token;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getLimitTimestamp()
    {
        return $this->limit_timestamp;
    }

    /**
     * @param mixed $limit_timestamp
     * @return UserTokenEntity
     */
    public function setLimitTimestamp($limit_timestamp)
    {
        $this->limit_timestamp = $limit_timestamp;
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

        $this->user_email = null;
        if (isset($data['userEmail'])) {
            $this->user_email = $data['userEmail'];
        } else if (isset($data['user_email'])) {
            $this->user_email = $data['user_email'];
        }

        $this->token = (isset($data['token'])) ? $data['token'] : null;

        $this->limit_timestamp = null;
        if (isset($data['limitTimestamp'])) {
            $this->limit_timestamp = $data['limitTimestamp'];
        } else if (isset($data['limit_timestamp'])) {
            $this->limit_timestamp = $data['limit_timestamp'];
        }
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