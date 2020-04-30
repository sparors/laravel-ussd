<?php

namespace Sparors\Ussd\Tests;

use Sparors\Ussd\Record;
use Orchestra\Testbench\TestCase;
use Illuminate\Support\Facades\Cache;
use Sparors\Ussd\UssdServiceProvider;

class RecordTest extends TestCase
{
    /**
     * Tell Testbench to use this package.
     *
     * @param $app
     *
     * @return array
     */
    public function getPackageProviders($app)
    {
        return [UssdServiceProvider::class];
    }
    
    public function testSetAndGetSingleValue()
    {
        $record = new Record(Cache::store('array'), '1');
        $record->set('name', 'ussd');
        $this->assertEquals('ussd', $record->get('name'));
    }

    public function testsetAndGetMultipleValues()
    {
        $record = new Record(Cache::store('array'), '1');
        $record->setMultiple(['name' => 'ussd', 'version' => '1.0']);
        $this->assertEquals(['ussd', '1.0'], $record->getMultiple(['name', 'version']));
    }

    public function testDeleteValue()
    {
        $record = new Record(Cache::store('array'), '1');
        $record->setMultiple(['name' => 'ussd', 'version' => '1.0']);
        $record->delete('name');
        $this->assertEquals([null, '1.0'], $record->getMultiple(['name', 'version']));
    }

    public function testDeleteMultipleValues()
    {
        $record = new Record(Cache::store('array'), '1');
        $record->setMultiple(['name' => 'ussd', 'version' => '1.0']);
        $record->deleteMultiple(['name', 'version']);
        $this->assertEquals([null, null], $record->getMultiple(['name', 'version']));
    }

    public function testHasValue()
    {
        $record = new Record(Cache::store('array'), '1');
        $record->set('name', 'ussd');
        $this->assertTrue($record->has('name'));
        $this->assertFalse($record->has('version'));
    }
}