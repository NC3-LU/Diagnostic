<?php
namespace Admin\Form;

use Zend\Form\Form;

/**
 * Setting Form
 *
 * @package Diagnostic\Form
 * @author Romain Desjardins
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
            'options' => [
                'label' => '__encryption_key'
            ],
            'attributes' => [
                'class' => 'form-control',
            ]
        ]);

        $this->add([
            'name' => 'date',
            'type' => 'Select',
            'options' => [
                'value_options' => [
                    '2015' => '2015',
                    '2016' => '2016',
                    '2017' => '2017',
                    '2018' => '2018',
                    '2019' => '2019',
                    '2020' => '2020',
                    '2021' => '2021',
                    '2022' => '2022',
                    '2023' => '2023',
                    '2024' => '2024',
                    '2025' => '2025',
                ]
            ],
            'attributes' => [
                'class' => 'form-control',
            ],
        ]);

        $this->add([
            'name' => 'activity',
            'type' => 'Select',
            'options' => [
                'label' => '__activity',
                'value_options' => [
                    '__activity1' => '__activity1',
                    '__activity2' => '__activity2',
                    '__activity3' => '__activity3',
                    '__activity4' => '__activity4',
                    '__activity5' => '__activity5',
                    '__activity6' => '__activity6',
                    '__activity7' => '__activity7',
                    '__activity8' => '__activity8',
                    '__activity9' => '__activity9',
                    '__activity10' => '__activity10',
                    '__activity11' => '__activity11',
                    '__activity12' => '__activity12',
                    '__activity13' => '__activity13',
                    '__activity14' => '__activity14',
                    '__activity15' => '__activity15',
                    '__activity16' => '__activity16',
                    '__activity17' => '__activity17',
                    '__activity18' => '__activity18',
                    '__activity19' => '__activity19',
                    '__activity20' => '__activity20',
                    '__activity21' => '__activity21',
                    '__activity22' => '__activity22',
                    '__activity23' => '__activity23',
                    '__activity24' => '__activity24',
                    '__activity25' => '__activity25',
                    '__activity26' => '__activity26',
                    '__activity27' => '__activity27',
                    '__activity28' => '__activity28',
                    '__activity19' => '__activity19',
                    '__activity20' => '__activity20',
                    '__activity21' => '__activity21',
                    '__activity22' => '__activity22',
                    '__activity23' => '__activity23',
                    '__activity24' => '__activity24',
                    '__activity25' => '__activity25',
                    '__activity26' => '__activity26',
                    '__activity27' => '__activity27',
                    '__activity28' => '__activity28',
                    '__activity29' => '__activity29',
                    '__activity30' => '__activity30',
                    '__activity31' => '__activity31',
                    '__activity32' => '__activity32',
                    '__activity33' => '__activity33',
                    '__activity34' => '__activity34',
                    '__activity35' => '__activity35',
                    '__activity36' => '__activity36',
                    '__activity37' => '__activity37',
                    '__activity38' => '__activity38'
                ]
            ],
            'attributes' => [
                'class' => 'form-control',
            ]
        ]);

        $this->add([
            'name' => 'diagnosis_stat',
            'type' => 'Text',
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

        $this->add([
            'name' => 'submit_stat',
            'type' => 'Submit',
            'attributes' => [
                'value' => '__record',
                'class' => 'btn btn-success',
            ],
        ]);
    }
}
