<?php
namespace Diagnostic\Form;

use Zend\Form\Form;

/**
 * Link Download Form
 *
 * @package Diagnostic\Form
 * @author Jerome De Almeida <jerome.dealmeida@vesperiagroup.com>
 * @author Romain Desjardins
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

        $this->add([
            'name' => 'submit_stat',
            'type' => 'Submit',
            'attributes' => [
                'value' => '__statistics',
                'class' => 'btn btn-warning',
                'style' => "padding: 7px 50px 7px 50px; margin-top: 15px;"
            ],
        ]);
    }
}
