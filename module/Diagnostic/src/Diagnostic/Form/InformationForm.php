<?php
namespace Diagnostic\Form;

use Zend\Form\Form;

/**
 * Information Form
 *
 * @package Diagnostic\Form
 * @author Jerome De Almeida <jerome.dealmeida@vesperiagroup.com>
 */
class InformationForm extends Form
{
    /**
     * Init
     */
    public function init()
    {
        $this->add([
            'name' => 'information',
            'type' => 'textarea',
            'options' => [
                'label' => '__information'
            ],
            'attributes' => [
                'class' => 'form-control information-area',
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
                'value' => '__record_continue',
                'id' => 'submitbutton',
                'class' => 'btn btn-success',
            ],
        ]);
    }
}