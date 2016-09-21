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
        for($i=0; $i<=6; $i++) {
            $threshold[$i*5] = $i*5;
        }

        $this->add(array(
            'name' => 'question',
            'type' => 'Text',
            'required' => true,
            'options' => array(
                'label' => '__question'
            ),
            'attributes' => array(
                'class' => 'form-control',
            )
        ));

        $this->add(array(
            'name' => 'help',
            'type' => 'Text',
            'required' => true,
            'options' => array(
                'label' => '__help'
            ),
            'attributes' => array(
                'class' => 'form-control',
            )
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

