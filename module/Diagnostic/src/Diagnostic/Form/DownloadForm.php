<?php
namespace Diagnostic\Form;

use Zend\Form\Form;

/**
 * Download Form
 *
 * @package Diagnostic\Form
 * @author Jerome De Almeida <jerome.dealmeida@vesperiagroup.com>
 */
class DownloadForm extends Form
{
    public $state = [
        '__draft' => '__draft',
        '__final' => '__final'
    ];

    /**
     * Init
     */
    public function init()
    {
        $this->add(array(
            'name' => 'document',
            'type' => 'Text',
            'required' => true,
            'options' => array(
                'label' => '__document_name'
            ),
            'attributes' => array(
                'class' => 'form-control',
            )
        ));

        $this->add(array(
            'name' => 'company',
            'type' => 'Text',
            'required' => true,
            'options' => array(
                'label' => '__company'
            ),
            'attributes' => array(
                'class' => 'form-control',
            )
        ));

        $this->add(array(
            'name' => 'version',
            'type' => 'Text',
            'required' => true,
            'options' => array(
                'label' => '__version'
            ),
            'attributes' => array(
                'class' => 'form-control',
            )
        ));

        $this->add(array(
            'name' => 'state',
            'type' => 'Select',
            'options' => array(
                'label' => '__state',
                'value_options' => $this->state,
            ),
            'attributes' => array(
                'class' => 'form-control',
            )
        ));

        $this->add(array(
            'name' => 'classification',
            'type' => 'Text',
            'required' => true,
            'options' => array(
                'label' => '__classification'
            ),
            'attributes' => array(
                'class' => 'form-control',
            )
        ));

        $this->add(array(
            'name' => 'consultant',
            'type' => 'Text',
            'options' => array(
                'label' => '__consultant',
            ),
            'attributes' => array(
                'class' => 'form-control',
            )
        ));

        $this->add(array(
            'name' => 'client',
            'type' => 'Text',
            'required' => true,
            'options' => array(
                'label' => '__client'
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
                'value' => '__download',
                'id' => 'submitbutton',
                'class' => 'btn btn-success',
            ),
        ));
    }
}

