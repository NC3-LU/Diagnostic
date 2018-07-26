<?php
namespace Admin\Form;

use Zend\Form\Form;

/**
 * Setting Form
 *
 * @package Diagnostic\Form
 * @author Romain DESJARDINS
 */
class SettingForm extends Form
{
    protected $languages_ref = [];

    /**
     * @return array
     */
    public function getLanguagesRef()
    {
        return $this->languages_ref;
    }

    /**
     * @param array $languages
     * @return LanguageForm
     */
    public function setLanguagesRef($languages_ref)
    {
        $this->languages_ref = $languages_ref;
        return $this;
    }

    /**
     * Init
     */
    public function init()
    {
        $this->add([
            'name' => 'select_language',
            'type' => 'Select',
            'options' => [
                'label' => '__change_default_language',
                'value_options' => $this->getLanguagesRef(),
            ],
            'attributes' => [
                'class' => 'form-control',
            ],
        ]);

        $this->add([
            'name' => 'checkbox_mxCheck',
            'type' => 'checkbox',
            'options' => [
                'label' => '__mail_verif',
            ],
        ]);

        $this->add([
            'name' => 'encryption_key',
            'type' => 'Text',
            'required' => true,
            'options' => [
                'label' => '__encryption_key'
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
                'value' => '__record',
                'class' => 'btn btn-success',
            ],
        ]);
	}
}
