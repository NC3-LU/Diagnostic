<?php
namespace Admin\Form;

use Zend\Form\Form;

/**
 * Language Form
 *
 * @package Diagnostic\Form
 * @author Jerome De Almeida <jerome.dealmeida@vesperiagroup.com>
 */
class LanguageForm extends Form
{
    /**
     * Init
     */
    public function init()
    {
        $this->add([
            'name' => 'add_language',
            'type' => 'Select',
            'options' => [
                'label' => 'add_a_language'
            ],
            'attributes' => [
                'class' => 'form-control',
            ]
        ]);

	$this->add([
            'name' => 'language_ref',
            'type' => 'Select',
            'options' => [
                'label' => 'language_ref'
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
            'name' => 'submit_lang_add',
            'type' => 'Submit',
            'attributes' => [
                'value' => '__add',
                'class' => 'btn btn-success',
            ],
        ]);

	$this->add([
            'name' => 'submit_lang_del',
            'type' => 'Submit',
            'attributes' => [
                'value' => '__delete',
                'class' => 'btn btn-success',
            ],
        ]);

	$this->add([
            'name' => 'submit_lang_ref',
            'type' => 'Submit',
            'attributes' => [
                'value' => '__add',
                'class' => 'btn btn-success',
            ],
        ]);

	$this->add([
            'name' => 'submit_all',
            'type' => 'Submit',
            'attributes' => [
                'value' => '__modify',
                'class' => 'btn btn-success',
            ],
        ]);
    }
}
