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
            'name' => 'threat',
            'type' => 'Select',
            'options' => [
                'label' => '__threat',
                'value_options' => [
                    '1' => 1,
                    '2' => 2,
                    '3' => 3,
                    '4' => 4,
                    '5' => 5,
                ],
            ],
            'attributes' => [
                'class' => 'form-control threshold',
            ],
        ]);

        $this->add([
            'name' => 'weight',
            'type' => 'Select',
            'options' => [
                'label' => '__weight',
                'value_options' => [
                    '1' => 1,
                    '2' => 2,
                    '3' => 3,
                    '4' => 4,
                    '5' => 5,
                    '6' => 6,
                ],
            ],
            'attributes' => [
                'class' => 'form-control threshold',
            ],
        ]);

        $this->add([
            'name' => 'blocking',
            'type' => 'Checkbox',
            'options' => [
                'label' => '__blocking',
                'checked_value' => '✓',
                'unchecked_value' => '✕',
            ],
        ]);

        $this->add([
            'name' => 'file',
            'type' => 'File',
            'options' => [
                'label' => '',
            ],
            'attributes' => [
                'class' => 'form-control',
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

        $this->add([
            'name' => 'submit_file',
            'type' => 'Submit',
            'attributes' => [
                'value' => '__upload',
                'class' => 'btn btn-success',
            ],
        ]);
    }
}
