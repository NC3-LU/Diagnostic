<?php
namespace Diagnostic\Form;

use Zend\Form\Form;

/**
 * Login Form
 *
 * @package Diagnostic\Form
 * @author Jerome De Almeida <jerome.dealmeida@vesperiagroup.com>
 */
class LoginForm extends Form
{
    /**
     * Init
     */
    public function init()
    {

        $this->add(array(
            'name' => 'email',
            'type' => 'Text',
            'required' => true,
            'options' => array(
                'label' => '__email'
            ),
            'attributes' => array(
                'class' => 'form-control',
            )
        ));

        $this->add(array(
            'type' => 'Csrf',
            'name' => 'csrf',
            'options' => array(
                'csrf_options' => array(
                    'timeout' => 600
                )
            )
        ));

        $this->add(array(
            'name' => 'password',
            'type' => 'Password',
            'required' => true,
            'options' => array(
                'label' => '__password'
            ),
            'attributes' => array(
                'class' => 'form-control',
            )
        ));

        $this->add(array(
            'name' => 'submit',
            'type' => 'Submit',
            'attributes' => array(
                'value' => '__log_in',
                'id' => 'submitbutton',
                'class' => 'btn btn-primary',
            )
        ));
    }
}

