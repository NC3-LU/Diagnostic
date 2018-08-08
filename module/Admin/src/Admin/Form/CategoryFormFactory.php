<?php

namespace Admin\Form;

use Admin\InputFilter\CategoryFormFilter;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Category Form Factory
 *
 * @package Admin\Factory
 * @author Romain DESJARDINS
 */
class CategoryFormFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $categories = $serviceLocator->getServiceLocator()->get('Diagnostic\Service\CategoryService')->getCategories();

        //retrieve categories
        $tab_categories = [];
        foreach ($categories as $category) {
            $tab_categories[$category->getId()] = $category->getTranslationKey();
        }

        $form = new CategoryForm();
        $form->setCategories($tab_categories);

        $categoryFormFilter = new CategoryFormFilter($serviceLocator->getServiceLocator()->get('Zend\Db\Adapter\Adapter'));
        $form->setInputFilter($categoryFormFilter);

        return $form;
    }
}
