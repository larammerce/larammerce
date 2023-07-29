<?php

namespace App\Libraries\Excel\Events;

use App\Libraries\Excel\Sheet;

class AfterSheet extends Event
{
    /**
     * @var \App\Libraries\Excel\Sheet
     */
    public $sheet;

    /**
     * @var object
     */
    private $exportable;

    /**
     * @param  \App\Libraries\Excel\Sheet  $sheet
     * @param  object  $exportable
     */
    public function __construct(Sheet $sheet, $exportable)
    {
        $this->sheet      = $sheet;
        $this->exportable = $exportable;
    }

    /**
     * @return \App\Libraries\Excel\Sheet
     */
    public function getSheet(): Sheet
    {
        return $this->sheet;
    }

    /**
     * @return object
     */
    public function getConcernable()
    {
        return $this->exportable;
    }

    /**
     * @return mixed
     */
    public function getDelegate()
    {
        return $this->sheet;
    }
}
