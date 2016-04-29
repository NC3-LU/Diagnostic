<?php
namespace Diagnostic\Form;

use Zend\Form\Form;

/**
 * Upload Form
 *
 * @package Diagnostic\Form
 * @author Jerome De Almeida <jerome.dealmeida@vesperiagroup.com>
 */
class UploadForm extends Form
{
    /**
     * Init
     */
    public function init()
    {
        $this->add(array(
            'name' => 'file',
            'type' => 'File',
            'options' => array(
                'label' => ' '
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
            'name' => 'submit',
            'type' => 'Submit',
            'attributes' => array(
                'value' => '__upload',
                'id' => 'submitbutton',
                'class' => 'btn btn-success',
            ),
        ));
    }
}

