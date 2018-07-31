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
class QuestionGateway extends AbstractGateway
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
            ->columns(['id', 'category_id', 'translation_key', 'translation_key_help' => new Expression('CONCAT(questions.translation_key, \'help\')'), 'threshold', 'uid'])
            ->join('categories', 'categories.id = category_id', ['category_translation_key' => 'translation_key'], 'left');

        $resultSet = $this->tableGateway->selectWith($select);

        return $resultSet;
    }

    /**
     * Get question by id
     *
     * @param $id
     * @return null|\Zend\Db\ResultSet\ResultSetInterface
     */
    public function getQuestionById($id)
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
            'category_id' => $data['category_id'],
            'translation_key' => $data['translation_key'],
            'threshold' => $data['threshold'],
            'uid' => $data['uid']
        ], ['id' => $id]);
    }
}
