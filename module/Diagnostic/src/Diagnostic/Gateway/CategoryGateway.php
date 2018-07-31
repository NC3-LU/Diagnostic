<?php
namespace Diagnostic\Gateway;

use Zend\Db\Sql\Platform\Mysql\Mysql;
use Zend\Db\Sql\Predicate\Expression;

/**
 * Category Gateway
 *
 * @package Diagnostic\Gateway
 * @author Romain DESJARDINS
 */
class CategoryGateway extends AbstractGateway
{
    /**
     * Fetch all with categories
     *
     * @return null|\Zend\Db\ResultSet\ResultSetInterface
     */
    public function fetchAllWithCategories()
    {

        $select = $this->tableGateway
            ->getSql()
            ->select()
            ->columns(['id', 'translation_key', 'uid']);

        $resultSet = $this->tableGateway->selectWith($select);

        return $resultSet;
    }

    /**
     * Get category by id
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
            'translation_key' => $data['translation_key'],
            'uid' => $data['uid']
        ], ['id' => $id]);
    }
}
