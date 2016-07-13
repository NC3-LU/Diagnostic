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
        $this->add(array(
            'name' => 'information',
            'type' => 'textarea',
            'options' => array(
                'label' => '__information'
            ),
            'attributes' => array(
                'class' => 'form-control information-area',
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
                'value' => '__record_continue',
                'id' => 'submitbutton',
                'class' => 'btn btn-success',
            ),
        ));
    }
}

