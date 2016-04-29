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
                'label' => '__new_password'
            ),
            'attributes' => array(
                'class' => 'form-control',
            )
        ));

        $this->add(array(
            'name' => 'password2',
            'type' => 'Password',
            'required' => true,
            'options' => array(
                'label' => '__confirm_password'
            ),
            'attributes' => array(
                'class' => 'form-control',
            )
        ));

        $this->add(array(
            'name' => 'submit',
            'type' => 'Submit',
            'attributes' => array(
                'value' => '__change_password',
                'id' => 'submitbutton',
                'class' => 'btn btn-primary',
            )
        ));
    }
}

