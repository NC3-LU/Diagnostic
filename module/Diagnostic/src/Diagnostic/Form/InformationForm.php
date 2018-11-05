<?php
namespace Diagnostic\Form;

use Zend\Form\Form;

/**
 * Information Form
 *
 * @package Diagnostic\Form
 * @author Jerome De Almeida <jerome.dealmeida@vesperiagroup.com>
 * @author Romain Desjardins
 */
class InformationForm extends Form
{
    public function init() {
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
            'name' => 'nb_employees',
            'type' => 'Select',
            'options' => [
                'label' => '__employees',
                'value_options' => [
                    '0 - 4' => '0 - 4',
                    '5 - 9' => '5 - 9',
                    '10 - 49' => '10 - 49',
                    '50 - 99' => '50 - 99',
                    '100 - 499' => '100 - 499',
                    '500+' => '500+',
                ]
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
                'value' => '__record_continue',
                'id' => 'submitbutton',
                'class' => 'btn btn-success',
            ],
        ]);
    }
}
