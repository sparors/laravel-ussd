<?php

namespace Sparors\Ussd\Tests\Integration;

use Sparors\Ussd\Record;
use Sparors\Ussd\Tests\TestCase;

final class RecordTest extends TestCase
{
    public function test_record_can_set_and_get_a_single_value()
    {
        $record = new Record('array', '1234', 'abcd');
        $record->set('name', 'ussd');
        $record->set('author', 'Isaac Sai');
        $record->version = '1.0';
        $this->assertEquals('ussd', $record->name);
        $this->assertEquals('Isaac Sai', $record('author'));
        $this->assertEquals('1.0', $record->get('version'));
    }

    public function test_record_can_set_and_get_many_values()
    {
        $record = new Record('array', '1234', 'abcd');
        $record->setMany(['name' => 'ussd', 'version' => '1.0']);
        $record(['author' => 'Isaac Sai',]);
        $this->assertEquals(['ussd', '1.0', 'Isaac Sai'], $record->getMany(['name', 'version', 'author']));
    }

    public function test_record_can_forget_a_value()
    {
        $record = new Record('array', '1234', 'abcd');
        $record->setMany(['name' => 'ussd', 'version' => '1.0', 'author' => 'Isaac Sai']);
        $record->forget('name');
        unset($record->author);
        $this->assertEquals([null, '1.0', null], $record->getMany(['name', 'version', 'author']));
    }

    public function test_record_can_forget_multiple_values()
    {
        $record = new Record('array', '1234', 'abcd');
        $record->setMany(['name' => 'ussd', 'version' => '1.0']);
        $record->forgetMany(['name', 'version']);
        $this->assertEquals([null, null], $record->getMany(['name', 'version']));
    }

    public function test_record_can_verify_if_cache_has_value()
    {
        $record = new Record('array', '1234', 'abcd');
        $record->set('name', 'ussd');
        $record->set('author', 'Isaac Sai');
        $this->assertTrue($record->has('name'));
        $this->assertFalse($record->has('version'));
        $this->assertTrue(isset($record->name));
    }

    public function test_record_can_increment_or_decrement_a_numeric_value()
    {
        $record = new Record('array', '1234', 'abcd');
        $record->set('age', 17);
        $this->assertEquals(18, $record->increment('age'));
        $this->assertEquals(17, $record->decrement('age'));
    }
}
