<?php

namespace Glacier\Buffers;

interface BufferInterface
{
    public function write($data);
    public function clear();
    public function getValue();
}

