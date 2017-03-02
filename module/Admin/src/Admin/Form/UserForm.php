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
            'name' => 'admin',
            'type' => 'Checkbox',
            'required' => true,
            'options' => [
                'label' => '__administrator'
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
                'value' => '__modify',
                'id' => 'submitbutton',
                'class' => 'btn btn-primary',
            ]
        ]);
    }
}

