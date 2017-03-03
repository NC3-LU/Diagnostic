<?php
namespace Diagnostic\InputFilter;

use Zend\InputFilter\InputFilter;

/**
 * Download Form Filter
 *
 * @package Diagnostic\Form
 * @author Jerome De Almeida <jerome.dealmeida@vesperiagroup.com>
 */
class DownloadFormFilter extends InputFilter
{
    public function __construct($adapter)
    {
        $this->add([
            'name' => 'document',
            'required' => true,
        ]);

        $this->add([
            'name' => 'company',
            'required' => true,
        ]);

        $this->add([
            'name' => 'version',
            'required' => true,
        ]);

        $this->add([
            'name' => 'state',
            'required' => true,
        ]);

        $this->add([
            'name' => 'classification',
            'required' => true,
        ]);

        $this->add([
            'name' => 'consultant',
            'required' => true,
        ]);

        $this->add([
            'name' => 'client',
            'required' => true,
        ]);
    }
}

