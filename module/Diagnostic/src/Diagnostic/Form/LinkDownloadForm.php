<?php
namespace Diagnostic\Form;

use Zend\Form\Form;

/**
 * Link Download Form
 *
 * @package Diagnostic\Form
 * @author Jerome De Almeida <jerome.dealmeida@vesperiagroup.com>
 */
class LinkDownloadForm extends Form
{
    /**
     * Init
     */
    public function init()
    {
        $this->add(array(
            'name' => 'radar',
            'type' => 'Hidden',
        ));

        $this->add(array(
            'name' => 'pie',
            'type' => 'Hidden',
        ));

        $this->add(array(
            'name' => 'bar',
            'type' => 'Hidden',
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

