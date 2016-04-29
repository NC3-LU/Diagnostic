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
            'name' => 'submit',
            'type' => 'Submit',
            'attributes' => array(
                'value' => '__receive_mail_password',
                'id' => 'submitbutton',
                'class' => 'btn btn-primary',
            )
        ));
    }
}

