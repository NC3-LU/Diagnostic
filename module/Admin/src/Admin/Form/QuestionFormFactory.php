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
        $questions = $serviceLocator->getServiceLocator()->get('Diagnostic\Service\QuestionService')->getQuestions();

        //retrieve categories
        $categories = [];
        foreach ($questions as $question) {
            $categories[$question->getCategoryId()] = $question->getCategoryTranslationKey();
        }

        $form = new QuestionForm();
        $form->setCategories($categories);

        $questionFormFilter = new QuestionFormFilter($serviceLocator->getServiceLocator()->get('Zend\Db\Adapter\Adapter'));
        $form->setInputFilter($questionFormFilter);

        return $form;
    }
}