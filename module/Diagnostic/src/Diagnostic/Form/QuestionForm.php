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
        $this->add(array(
            'name' => 'maturity',
            'type' => 'Radio',
            'options' => array(
                'label' => '__maturity',
                'value_options' => [
                    3 => [
                        'value' => '3',
                        'label_attributes' => array('class' => 'maticon matok', 'title' => '__maturity_ok'),
                    ],
                    2 => [
                        'value' => '2',
                        'label_attributes' => array('class' => 'maticon matmoyen', 'title' => '__maturity_medium'),
                    ],
                    1 => [
                        'value' => '1',
                        'label_attributes' => array('class' => 'maticon matplan', 'title' => '__maturity_plan'),
                    ],
                    0 => [
                        'value' => '0',
                        'label_attributes' => array('class' => 'maticon matnone', 'title' => '__maturity_none'),
                    ]
                ],
                'label_attributes' => array(
                    'class' => 'radio-inline'
                ),
            ),
            'attributes' => array(
                'class' => 'radio'
            ),
        ));

        $this->add(array(
            'name' => 'maturityTarget',
            'type' => 'Radio',
            'options' => array(
                'label' => '__maturity_target',
                'value_options' => [
                    3 => [
                        'value' => '3',
                        'label_attributes' => array('class' => 'maticon matok', 'title' => '__maturity_ok'),
                    ],
                    2 => [
                        'value' => '2',
                        'label_attributes' => array('class' => 'maticon matmoyen', 'title' => '__maturity_medium'),
                    ],
                ],
                'label_attributes' => array(
                    'class' => 'radio-inline'
                ),
            ),
            'attributes' => array(
                'class' => 'radio'
            ),
        ));

        $this->add(array(
            'name' => 'recommandation',
            'type' => 'textarea',
            'options' => array(
                'label' => '__recommandation'
            ),
            'attributes' => array(
                'class' => 'form-control recommandation-area',
            )
        ));

        $this->add(array(
            'name' => 'gravity',
            'type' => 'Radio',
            'options' => array(
                'label' => '__gravity',
                'value_options' => [
                    1 => [
                        'value' => '1',
                        'label_attributes' => array('class' => 'maticon gravity1', 'title' => '__low'),
                    ],
                    2 => [
                        'value' => '2',
                        'label_attributes' => array('class' => 'maticon gravity2', 'title' => '__medium'),
                    ],
                    3 => [
                        'value' => '3',
                        'label_attributes' => array('class' => 'maticon gravity3', 'title' => '__strong'),
                    ],
                ],
                'label_attributes' => array(
                    'class' => 'radio-inline'
                ),
            ),
            'attributes' => array(
                'class' => 'radio'
            ),
        ));

        $this->add(array(
            'name' => 'notes',
            'type' => 'textarea',
            'options' => array(
                'label' => '__notes'
            ),
            'attributes' => array(
                'class' => 'form-control notes-area',
            )
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
                'value' => '__record_continue',
                'id' => 'submitbutton',
                'class' => 'btn btn-success',
            ),
        ));
    }
}

