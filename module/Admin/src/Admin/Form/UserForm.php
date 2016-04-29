<?php
namespace Admin\Form;

use Zend\Form\Form;

/**
 * Login Form
 *
 * @package Admin\Form
 * @author Jerome De Almeida <jerome.dealmeida@vesperiagroup.com>
 */
class UserForm extends Form
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
            'name' => 'admin',
            'type' => 'Checkbox',
            'required' => true,
            'options' => array(
                'label' => '__administrator'
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
            'name' => 'submit',
            'type' => 'Submit',
            'attributes' => array(
                'value' => '__modify',
                'id' => 'submitbutton',
                'class' => 'btn btn-primary',
            )
        ));
    }
}

