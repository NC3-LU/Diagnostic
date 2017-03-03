<?php
namespace Diagnostic\Form;

use Zend\Form\Form;

/**
 * New Password Form
 *
 * @package Diagnostic\Form
 * @author Jerome De Almeida <jerome.dealmeida@vesperiagroup.com>
 */
class NewPasswordForm extends Form
{
    /**
     * Init
     */
    public function init()
    {
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
                'label' => '__new_password'
            ],
            'attributes' => [
                'class' => 'form-control',
            ]
        ]);

        $this->add([
            'name' => 'password2',
            'type' => 'Password',
            'required' => true,
            'options' => [
                'label' => '__confirm_password'
            ],
            'attributes' => [
                'class' => 'form-control',
            ]
        ]);

        $this->add([
            'name' => 'submit',
            'type' => 'Submit',
            'attributes' => [
                'value' => '__change_password',
                'id' => 'submitbutton',
                'class' => 'btn btn-primary',
            ]
        ]);
    }
}