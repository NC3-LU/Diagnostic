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
        $this->add([
            'name' => 'email',
            'type' => 'Text',
            'required' => true,
            'options' => [
                'label' => '__email'
            ],
            'attributes' => [
                'class' => 'form-control',
            ]
        ]);

        $this->add([
            'type' => 'Csrf',
            'name' => 'csrf',
            'options' => [
                'csrf_options' => [
                    'timeout' => 600
                ]
            ]
        ]);

        $this->add([
            'name' => 'password',
            'type' => 'Password',
            'required' => true,
            'options' => [
                'label' => '__password'
            ],
            'attributes' => [
                'class' => 'form-control',
            ]
        ]);

        $this->add([
            'name' => 'submit',
            'type' => 'Submit',
            'attributes' => [
                'value' => '__log_in',
                'id' => 'submitbutton',
                'class' => 'btn btn-primary',
            ]
        ]);
    }
}