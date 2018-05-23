<?php
namespace Diagnostic\Service;

use Zend\Crypt\BlockCipher;
use Zend\Session\Container;

/**
 * QuestionService
 *
 * @package Diagnostic\Service
 * @author Jerome De Almeida <jerome.dealmeida@vesperiagroup.com>
 */
class CategoryService extends AbstractService
{
    protected $config;

    /**
     * Fetch all with categories
     *
     * @return \Zend\Db\ResultSet\ResultSet
     * @throws \Exception
     */
    public function fetchAllWithCategories()
    {
        $tableGateway = $this->get('gateway');

        return $tableGateway->fetchAllWithCategories();
    }

    /**
     * Get question
     *
     * @return array
     * @throws \Exception
     */
    public function getCategories()
    {
        $container = new Container('diagnostic');
        if ($container->offsetExists('categories')) {
            $questions = $container->categories;
        } else {
            $questionsObject = $this->fetchAllWithCategories();

            $questions = [];
            foreach ($questionsObject as $question) {
                $questions[$question->getId()] = $question;
            }

            $container->categories = $questions;
        }

        return $questions;
    }




	 /**
     * Get Bdd Questions
     *
     * @return array
     * @throws \Exception
     */
    public function getBddCategories()
    {
        $categoriesObject = $this->fetchAllWithCategories();

        $categories = [];
        foreach ($categoriesObject as $category) {
            $categories[$category->getId()] = $category;
        }

        return $categories;
    }

    /**
     * Create
     *
     * @param $data
     */
    public function create($data)
    {
        $categoryGateway = $this->get('gateway');
        $categoryGateway->insert($data);
    }

    /**
     * Reset Cache
     */
    public function resetCache()
    {
        $container = new Container('diagnostic');
        $container->offsetUnset('categories');
    }

    /**
     * Get category by id
     *
     * @return \Zend\Db\ResultSet\ResultSet
     * @throws \Exception
     */
    public function getCategoryById($id)
    {
        $tableGateway = $this->get('gateway');

        return $tableGateway->getCategoryById($id);
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
        $categoryGateway = $this->get('gateway');
        $categoryGateway->update($id, $data);
    }

    /**
     * Delete
     *
     * @param $id
     */
    public function delete($id)
    {
        $categoryGateway = $this->get('gateway');
        $categoryGateway->delete($id);
    }
}
