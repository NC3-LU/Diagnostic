<?php

namespace Admin\Form;

use Admin\InputFilter\CategoryFormFilter;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Question Form Factory
 *
 * @package Admin\Factory
 * @author Jerome De Almeida <jerome.dealmeida@vesperiagroup.com>
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
        $questions = [];
        foreach ($categories as $category) {
            $questions[$category->getId()] = $category->getTranslationKey();
        }

        $form = new CategoryForm();
        $form->setCategories($questions);

        $categoryFormFilter = new CategoryFormFilter($serviceLocator->getServiceLocator()->get('Zend\Db\Adapter\Adapter'));
        $form->setInputFilter($categoryFormFilter);

        return $form;
    }
}
