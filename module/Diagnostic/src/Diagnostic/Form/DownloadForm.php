<?php
namespace Diagnostic\Form;

use Zend\Form\Form;

/**
 * Download Form
 *
 * @package Diagnostic\Form
 * @author Jerome De Almeida <jerome.dealmeida@vesperiagroup.com>
 * @author Romain Desjardins
 */
class DownloadForm extends Form
{
    public $state = [
	'__final' => '__final',
        '__draft' => '__draft'
    ];

    /**
     * Init
     */
    public function init()
    {
        $this->add([
            'name' => 'document',
            'type' => 'Text',
            'required' => true,
            'options' => [
                'label' => '__document_name'
            ],
            'attributes' => [
                'class' => 'form-control',
            ]
        ]);

        $this->add([
            'name' => 'company',
            'type' => 'Text',
            'required' => true,
            'options' => [
                'label' => '__company'
            ],
            'attributes' => [
                'class' => 'form-control',
            ]
        ]);

        $this->add([
            'name' => 'version',
            'type' => 'Text',
            'required' => true,
            'options' => [
                'label' => '__version'
            ],
            'attributes' => [
                'class' => 'form-control',
            ]
        ]);

        $this->add([
            'name' => 'state',
            'type' => 'Select',
            'options' => [
                'label' => '__state',
                'value_options' => $this->state,
            ],
            'attributes' => [
                'class' => 'form-control',
            ]
        ]);

        $this->add([
            'name' => 'classification',
            'type' => 'Text',
            'required' => true,
            'options' => [
                'label' => '__classification'
            ],
            'attributes' => [
                'class' => 'form-control',
            ]
        ]);

        $this->add([
            'name' => 'consultant',
            'type' => 'Text',
            'options' => [
                'label' => '__consultant',
            ],
            'attributes' => [
                'class' => 'form-control',
            ]
        ]);

        $this->add([
            'name' => 'client',
            'type' => 'Text',
            'required' => true,
            'options' => [
                'label' => '__client'
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
                'value' => '__download',
                'id' => 'submitbutton',
                'class' => 'btn btn-success',
            ],
        ]);
    }
}

