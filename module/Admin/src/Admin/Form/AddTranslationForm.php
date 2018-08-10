<?php
namespace Admin\Form;

use Zend\Form\Form;

/**
 * Add Translation Form
 *
 * @package Diagnostic\Form
 * @author Romain Desjardins
 */
class AddTranslationForm extends Form
{
    /**
     * Init
     */
    public function init()
    {
        $this->add([
            'name' => 'translation_key',
            'type' => 'Text',
            'required' => true,
            'options' => [
                'label' => '__translation_key'
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
                'value' => '__add',
                'id' => 'submitbutton',
                'class' => 'btn btn-success',
            ],
        ]);
    }
}
