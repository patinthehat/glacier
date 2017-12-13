<?php

use PHPUnit\Framework\TestCase;


class StringBufferTest extends TestCase
{

  public function test_clear_buffer_and_write_string()
  {
    $buffer = new Glacier\Buffers\StringBuffer;

    $buffer->clear();
    $this->assertTrue(strlen($buffer->value) == 0);

    $buffer->write('test data');
    $this->assertEquals('test data', $buffer->value);

    $buffer->clear();
    $this->assertTrue(strlen($buffer->value) == 0);
  }

  public function test_writes_data_and_converts_to_string_properly()
  {
      $buffer = new Glacier\Buffers\StringBuffer;
      $buffer->write('TEST');
      $temp = "$buffer";

      $this->assertEquals('TEST', $temp);
  }

  public function test_get_value_returns_correct_value()
  {
      $buffer = new Glacier\Buffers\StringBuffer;
      $buffer->write('TEST');
      $this->assertEquals('TEST', $buffer->getValue());
  }

}
