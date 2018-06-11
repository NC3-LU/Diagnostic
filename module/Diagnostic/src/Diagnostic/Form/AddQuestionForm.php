<?php
namespace Diagnostic\Form;

use Zend\Form\Form;

/**
 * Add Question Form
 *
 * @package Diagnostic\Form
 * @author Jerome De Almeida <jerome.dealmeida@vesperiagroup.com>
 */
class AddQuestionForm extends Form
{
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
            'name' => 'question',
            'type' => 'Text',
            'required' => true,
            'options' => [
                'label' => '__question'
            ],
            'attributes' => [
                'class' => 'form-control',
            ]
        ]);

        $this->add([
            'name' => 'help',
            'type' => 'Text',
            'required' => true,
            'options' => [
                'label' => '__help'
            ],
            'attributes' => [
                'class' => 'form-control',
            ]
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
