<?php
namespace Diagnostic\Service;

use Zend\Crypt\BlockCipher;
use Zend\Session\Container;

/**
 * CategoryService
 *
 * @package Diagnostic\Service
 * @author Romain Desjardins
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
     * Get category
     *
     * @return array
     * @throws \Exception
     */
    public function getCategories()
    {
        $container = new Container('diagnostic');
        if ($container->offsetExists('categories')) {
            $categories = $container->categories;
        } else {
            $categoriesObject = $this->fetchAllWithCategories();

            $categories = [];
            foreach ($categoriesObject as $category) {
                $categories[$category->getId()] = $category;
            }

            $container->categories = $categories;
        }

        $tmpArray = [];
        foreach ($categories as $category) {
            $tmpArray[$category->getId()] = $category;
        }

        ksort($tmpArray);
        $categories = [];
        foreach ($tmpArray as $value) {
            $categories[$value->getId()] = $value;
        }

        return $categories;
    }

    /**
     * Get Bdd Categories
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
