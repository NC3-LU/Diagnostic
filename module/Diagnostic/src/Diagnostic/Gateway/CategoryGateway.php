<?php
namespace Diagnostic\Gateway;

use Zend\Db\Sql\Platform\Mysql\Mysql;
use Zend\Db\Sql\Predicate\Expression;

/**
 * Question Gateway
 *
 * @package Diagnostic\Gateway
 * @author Jerome De Almeida <jerome.dealmeida@vesperiagroup.com>
 */
class CategoryGateway extends AbstractGateway
{
    /**
     * Fetch all with questions
     *
     * @return null|\Zend\Db\ResultSet\ResultSetInterface
     */
    public function fetchAllWithCategories()
    {

        $select = $this->tableGateway
            ->getSql()
            ->select()
            ->columns(['id', 'translation_key']);

        $resultSet = $this->tableGateway->selectWith($select);

        return $resultSet;
    }

    /**
     * Get question by id
     *
     * @param $id
     * @return null|\Zend\Db\ResultSet\ResultSetInterface
     */
    public function getCategoryById($id)
    {

        $select = $this->tableGateway
            ->getSql()
            ->select()
            ->where(['id = ?' => $id]);

        $resultSet = $this->tableGateway->selectWith($select);

        return $resultSet;
    }

    /**
     * Update
     *
     * @param $id
     * @param $data
     */
    public function update($id, $data)
    {
        $this->tableGateway->update([
            'translation_key' => $data['translation_key']
        ], ['id' => $id]);
    }
}
