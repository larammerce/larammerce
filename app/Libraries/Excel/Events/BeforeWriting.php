<?php

namespace App\Libraries\Excel\Events;

use App\Libraries\Excel\Writer;

class BeforeWriting extends Event
{
    /**
     * @var Writer
     */
    public $writer;

    /**
     * @var object
     */
    private $exportable;

    /**
     * @param  \App\Libraries\Excel\Writer  $writer
     * @param  object  $exportable
     */
    public function __construct(Writer $writer, $exportable)
    {
        $this->writer     = $writer;
        $this->exportable = $exportable;
    }

    /**
     * @return \App\Libraries\Excel\Writer
     */
    public function getWriter(): Writer
    {
        return $this->writer;
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
        return $this->writer;
    }
}
