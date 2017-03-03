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
        $this->add([
            'name' => 'radar',
            'type' => 'Hidden',
        ]);

        $this->add([
            'name' => 'pie',
            'type' => 'Hidden',
        ]);

        $this->add([
            'name' => 'bar',
            'type' => 'Hidden',
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