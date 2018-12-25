<?php
namespace App\Tests;


use PHPUnit\Framework\TestCase;

class SimpleTest extends TestCase
{
    public function testAddition()
    {
        $this->assertEquals(5, 2+3, 'Five was expected');
    }
}