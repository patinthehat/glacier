<?php

namespace Glacier\Buffers;

use Glacier\Buffers\BufferInterface;

class StringBuffer implements BufferInterface
{
    public $value = '';

    public function write($data)
    {
        $this->value .= $data;
        return $this;
    }

    public function clear()
    {
        $this->value = '';
        return $this;
    }

    public function getValue()
    {
        return $this->value;
    }

    public function __toString()
    {
        return $this->getValue();
    }

}