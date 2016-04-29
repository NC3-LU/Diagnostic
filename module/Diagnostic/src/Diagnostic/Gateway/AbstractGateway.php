<?php
namespace Diagnostic\Gateway;

use Zend\Db\TableGateway\TableGateway;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;

/**
 * Abstract Gateway
 *
 * @package Diagnostic\Gateway
 * @author Jerome De Almeida <jerome.dealmeida@vesperiagroup.com>
 */
abstract class AbstractGateway implements ServiceLocatorAwareInterface
{
    use ServiceLocatorAwareTrait;

    /**
     * @var tableGateway
     */
    protected $tableGateway;

    /**
     * AbstractGateway constructor.
     *
     * @param TableGateway $tableGateway
     */
    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }

    /**
     * Fetch All
     *
     * @return \Zend\Db\ResultSet\ResultSet
     */
    public function fetchAll()
    {
        $resultSet = $this->tableGateway->select();
        return $resultSet;
    }

    /**
     * Insert
     *
     * @param $data
     * @return int
     */
    public function insert($data)
    {
        if (is_object($data)) {
            $data = get_object_vars($data);
        }

        if (array_key_exists('csrf', $data)) {
            unset($data['csrf']);
        }

        if (array_key_exists('submit', $data)) {
            unset($data['submit']);
        }

        $this->tableGateway->insert($data);

        return $this->tableGateway->lastInsertValue;
    }

    /**
     * Delete
     *
     * @param $id
     */
    public function delete($id)
    {
        $this->tableGateway->delete(array('id' => (int) $id));
    }
}