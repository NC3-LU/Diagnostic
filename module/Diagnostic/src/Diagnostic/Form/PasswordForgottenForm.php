<?php
namespace Diagnostic\Form;

use Zend\Form\Form;

/**
 * Password Forgotten Form
 *
 * @package Diagnostic\Form
 * @author Jerome De Almeida <jerome.dealmeida@vesperiagroup.com>
 */
class PasswordForgottenForm extends Form
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
            'name' => 'submit',
            'type' => 'Submit',
            'attributes' => [
                'value' => '__receive_mail_password',
                'id' => 'submitbutton',
                'class' => 'btn btn-primary',
            ]
        ]);
    }
}