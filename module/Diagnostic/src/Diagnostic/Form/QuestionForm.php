<?php
namespace Diagnostic\Form;

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
     * Init
     */
    public function init()
    {
        $this->add([
            'name' => 'maturity',
            'type' => 'Radio',
            'options' => [
                'label' => '__maturity',
                'value_options' => [
                    3 => [
                        'value' => '2',
                        'label_attributes' => ['class' => 'maticon matok', 'title' => '__maturity_ok'],
                    ],
                    2 => [
                        'value' => '1',
                        'label_attributes' => ['class' => 'maticon matmoyen', 'title' => '__maturity_medium'],
                    ],
                    0 => [
                        'value' => '0',
                        'label_attributes' => ['class' => 'maticon matnone', 'title' => '__maturity_none'],
                    ],
                    1 => [
                        'value' => '3', // The value is N/A, so it isn't counted in the score
                        'label_attributes' => ['class' => 'maticon matNA', 'title' => '__maturity_NA'],
                    ]
                ],
                'label_attributes' => [
                    'class' => 'radio-inline'
                ],
            ],
            'attributes' => [
                'class' => 'radio'
            ],
        ]);

        $this->add([
            'name' => 'maturityTarget',
            'type' => 'Radio',
            'options' => [
                'label' => '__maturity_target',
                'value_options' => [
                    3 => [
                        'value' => '3',
                        'label_attributes' => ['class' => 'maticon matok', 'title' => '__maturity_ok'],
                    ],
                    2 => [
                        'value' => '2',
                        'label_attributes' => ['class' => 'maticon matmoyen', 'title' => '__maturity_medium'],
                    ],
                ],
                'label_attributes' => [
                    'class' => 'radio-inline'
                ],
            ],
            'attributes' => [
                'class' => 'radio'
            ],
        ]);

        $this->add([
            'name' => 'recommandation',
            'type' => 'textarea',
            'options' => [
                'label' => '__recommandation'
            ],
            'attributes' => [
                'class' => 'form-control recommandation-area',
            ]
        ]);

        $this->add([
            'name' => 'gravity',
            'type' => 'Radio',
            'options' => [
                'label' => '__gravity',
                'value_options' => [
                    1 => [
                        'value' => '1',
			'label' => '●',
                        'label_attributes' => ['title' => '__low'],
                    ],
                    2 => [
                        'value' => '2',
			'label' => '●●',
                        'label_attributes' => ['title' => '__medium'],
                    ],
                    3 => [
                        'value' => '3',
			'label' => '●●●',
                        'label_attributes' => ['title' => '__strong'],
                    ],
                ],
                'label_attributes' => [
                    'class' => 'radio-inline'
                ],
            ],
            'attributes' => [
                'class' => 'radio'
            ],
        ]);

        $this->add([
            'name' => 'notes',
            'type' => 'textarea',
            'options' => [
                'label' => '__notes'
            ],
            'attributes' => [
                'class' => 'form-control notes-area',
            ]
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
                'value' => '__record_continue',
                'id' => 'submitbutton',
                'class' => 'btn btn-success',
            ],
        ]);
    }
}