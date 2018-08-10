<?php
namespace Admin\Form;

use Zend\Form\Form;

/**
 * Template Form
 *
 * @package Diagnostic\Form
 * @author Romain Desjardins
 */
class TemplateForm extends Form
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
                'label' => ' ',
            ],
            'attributes' => [
                'class' => 'form-control',
            ],
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
            'name' => 'submit_file',
            'type' => 'Submit',
            'attributes' => [
                'value' => '__upload',
                'class' => 'btn btn-success',
            ],
        ]);
    }
}
