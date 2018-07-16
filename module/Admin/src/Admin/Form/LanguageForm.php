<?php
namespace Admin\Form;

use Zend\Form\Form;

/**
 * Language Form
 *
 * @package Diagnostic\Form
 * @author Romain DESJARDINS
 */
class LanguageForm extends Form
{

    /**
     * Languages
     *
     * @var array
     */
    protected $languages = [];
    protected $languages_ref = [];

    /**
     * @return array
     */
    public function getLanguages()
    {
        return $this->languages;
    }

    /**
     * @param array $languages
     * @return LanguageForm
     */
    public function setLanguages($languages)
    {
        $this->languages = $languages;
        return $this;
    }

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
            'name' => 'add_language',
            'type' => 'Select',
            'options' => [
                'label' => '__add_a_language',
		'value_options' => $this->getLanguages(),
            ],
            'attributes' => [
                'class' => '',
            ]
        ]);

	$this->add([
            'name' => 'language_ref',
            'type' => 'Select',
            'options' => [
                'label' => '__translation_ref',
		'value_options' => $this->getLanguagesRef(),
            ],
            'attributes' => [
                'class' => '',
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
                'value' => 'Ok',
                'class' => 'btn btn-success',
            ],
        ]);

	$this->add([
            'name' => 'submit_all',
            'type' => 'Submit',
            'attributes' => [
                'value' => '__modify_all',
                'class' => 'btn btn-success',
            ],
        ]);

        $this->add([
            'name' => 'submit_dl_report',
            'type' => 'Submit',
            'attributes' => [
                'value' => '__dl_report',
                'class' => 'btn btn-success',
            ],
        ]);

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
            'name' => 'submit_file',
            'type' => 'Submit',
            'attributes' => [
                'value' => '__upload',
                'class' => 'btn btn-success',
            ],
        ]);
    }
}
