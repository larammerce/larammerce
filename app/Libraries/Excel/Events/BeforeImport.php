<?php

namespace App\Libraries\Excel\Events;

use App\Libraries\Excel\Reader;

class BeforeImport extends Event
{
    /**
     * @var \App\Libraries\Excel\Reader
     */
    public $reader;

    /**
     * @var object
     */
    private $importable;

    /**
     * @param  \App\Libraries\Excel\Reader  $reader
     * @param  object  $importable
     */
    public function __construct(Reader $reader, $importable)
    {
        $this->reader     = $reader;
        $this->importable = $importable;
    }

    /**
     * @return \App\Libraries\Excel\Reader
     */
    public function getReader(): Reader
    {
        return $this->reader;
    }

    /**
     * @return object
     */
    public function getConcernable()
    {
        return $this->importable;
    }

    /**
     * @return mixed
     */
    public function getDelegate()
    {
        return $this->reader;
    }
}
