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
        for ($i = 0; $i <= 6; $i++) {
            $threshold[$i * 5] = $i * 5;
        }

        $this->add([
            'name' => 'translation_key',
            'type' => 'Text',
            'required' => true,
            'options' => [
                'label' => '__translation_key'
            ],
            'attributes' => [
                'class' => 'form-control',
            ]
        ]);

        $this->add([
            'name' => 'category_id',
            'type' => 'Select',
            'options' => [
                'label' => '__categories',
                'value_options' => $this->getCategories(),
            ],
            'attributes' => [
                'class' => 'form-control',
            ],
        ]);

        $this->add([
            'name' => 'threshold',
            'type' => 'Select',
            'options' => [
                'label' => '__threshold_max',
                'value_options' => $threshold,
            ],
            'attributes' => [
                'class' => 'form-control threshold',
            ],
        ]);

        $this->add([
            'type' => 'Csrf',
            'name' => 'csrf',
            'options' => [
                'csrf_options' => [
                    'timeout' => 3600
                ]
            ]
        ]);

        $this->add([
            'name' => 'submit',
            'type' => 'Submit',
            'attributes' => [
                'value' => '__add',
                'id' => 'submitbutton',
                'class' => 'btn btn-success',
            ],
        ]);
    }
}
