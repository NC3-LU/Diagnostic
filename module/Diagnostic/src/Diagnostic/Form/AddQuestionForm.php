<?php
namespace Diagnostic\Form;

use Zend\Form\Form;

/**
 * Add Question Form
 *
 * @package Diagnostic\Form
 * @author Jerome De Almeida <jerome.dealmeida@vesperiagroup.com>
 * @author Romain Desjardins
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
