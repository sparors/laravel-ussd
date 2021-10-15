<?php

namespace Sparors\Ussd\Tests;

use Sparors\Ussd\Record;
use Sparors\Ussd\Tests\TestCase;
use Illuminate\Support\Facades\Cache;

class RecordTest extends TestCase
{
    public function test_it_can_set_and_get_a_single_value()
    {
        $record = new Record(Cache::store('array'), '1');
        $record->set('name', 'ussd');
        $record->set('author', 'Isaac Sai');
        $record->version = '1.0';
        $this->assertEquals('ussd', $record->name);
        $this->assertEquals('Isaac Sai', $record('author'));
        $this->assertEquals('1.0', $record->get('version'));
    }

    public function test_it_can_set_and_get_multiple_values()
    {
        $record = new Record(Cache::store('array'), '1');
        $record->setMultiple(['name' => 'ussd', 'version' => '1.0']);
        $record(['author' => 'Isaac Sai',]);
        $this->assertEquals(['ussd', '1.0', 'Isaac Sai'], $record->getMultiple(['name', 'version', 'author']));
    }

    public function test_it_can_delete_a_value()
    {
        $record = new Record(Cache::store('array'), '1');
        $record->setMultiple(['name' => 'ussd', 'version' => '1.0', 'author' => 'Isaac Sai']);
        $record->delete('name');
        unset($record->author);
        $this->assertEquals([null, '1.0', null], $record->getMultiple(['name', 'version', 'author']));
    }

    public function test_it_can_delete_multiple_values()
    {
        $record = new Record(Cache::store('array'), '1');
        $record->setMultiple(['name' => 'ussd', 'version' => '1.0']);
        $record->deleteMultiple(['name', 'version']);
        $this->assertEquals([null, null], $record->getMultiple(['name', 'version']));
    }

    public function test_it_can_verify_if_cache_has_value()
    {
        $record = new Record(Cache::store('array'), '1');
        $record->set('name', 'ussd');
        $record->set('author', 'Isaac Sai');
        $this->assertTrue($record->has('name'));
        $this->assertFalse($record->has('version'));
        $this->assertTrue(isset($record->name));
    }

    public function test_it_can_increment_or_decrement_a_numeric_value()
    {
        $record = new Record(Cache::store('array'), '1');
        $record->set('age', 17);
        $this->assertEquals(18, $record->increment('age'));
        $this->assertEquals(17, $record->decrement('age'));
    }

    public function test_it_can_delete_all_values()
    {
        $record = new Record(Cache::store('array'), '1');
        $record->setMultiple(['name' => 'ussd', 'version' => '1.0']);
        $record->flush();
        $this->assertNull($record->get('name'));
        $this->assertNull($record->get('version'));
    }
}
