<?php

namespace Admin\Form;

use Admin\InputFilter\QuestionFormFilter;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Question Form Factory
 *
 * @package Admin\Factory
 * @author Jerome De Almeida <jerome.dealmeida@vesperiagroup.com>
 * @author Romain Desjardins
 */
class QuestionFormFactory implements FactoryInterface
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

        $form = new QuestionForm();
        $form->setCategories($tab_categories);

        $questionFormFilter = new QuestionFormFilter($serviceLocator->getServiceLocator()->get('Zend\Db\Adapter\Adapter'));
        $form->setInputFilter($questionFormFilter);

        return $form;
    }
}
