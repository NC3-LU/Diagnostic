<?php
namespace Admin\Form;

use Zend\Form\Form;

/**
 * Question Form
 *
 * @package Diagnostic\Form
 * @author Jerome De Almeida <jerome.dealmeida@vesperiagroup.com>
 */
class QuestionForm extends Form
{
    /**
     * Categories
     *
     * @var array
     */
    protected $categories = [];

    /**
     * @return array
     */
    public function getCategories()
    {
        return $this->categories;
    }

    /**
     * @param array $categories
     * @return QuestionForm
     */
    public function setCategories($categories)
    {
        $this->categories = $categories;
        return $this;
    }

    /**
     * Init
     */
    public function init()
    {

        $threshold = [];
        for($i=0; $i<=6; $i++) {
            $threshold[$i*5] = $i*5;
        }

        $this->add(array(
            'name' => 'translation_key',
            'type' => 'Text',
            'required' => true,
            'options' => array(
                'label' => '__translation_key'
            ),
            'attributes' => array(
                'class' => 'form-control',
            )
        ));

        $this->add(array(
            'name' => 'category_id',
            'type' => 'Select',
            'options' => array(
                'label' => '__categories',
                'value_options' => $this->getCategories(),
            ),
            'attributes' => array(
                'class' => 'form-control',
            ),
        ));

        $this->add(array(
            'name' => 'threshold',
            'type' => 'Select',
            'options' => array(
                'label' => '__threshold_max',
                'value_options' => $threshold,
            ),
            'attributes' => array(
                'class' => 'form-control threshold',
            ),
        ));

        $this->add(array(
            'type' => 'Csrf',
            'name' => 'csrf',
            'options' => array(
                'csrf_options' => array(
                    'timeout' => 3600
                )
            )
        ));

        $this->add(array(
            'name' => 'submit',
            'type' => 'Submit',
            'attributes' => array(
                'value' => '__add',
                'id' => 'submitbutton',
                'class' => 'btn btn-success',
            ),
        ));
    }
}

