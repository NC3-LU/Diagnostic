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
        $this->add([
            'name' => 'file',
            'type' => 'File',
            'options' => [
                'label' => ' '
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
                    'timeout' => 3600
                ]
            ]
        ]);

        $this->add([
            'name' => 'submit',
            'type' => 'Submit',
            'attributes' => [
                'value' => '__upload',
                'id' => 'submitbutton',
                'class' => 'btn btn-success',
            ],
        ]);
    }
}