<?php

namespace Sparors\Ussd\Tests;

use Illuminate\Support\Facades\Request;
use Orchestra\Testbench\TestCase;
use Sparors\Ussd\Decision;
use Sparors\Ussd\HasManipulators;
use Sparors\Ussd\Record;

class HasManipulatorsTest extends TestCase
{
    public function testSetSessionId()
    {
        /** @var \Sparors\Ussd\HasManipulators */
        $manipulator = $this->getMockForTrait(HasManipulators::class);
        $manipulator->setSessionId('1234');
        $this->assertEquals('1234', $manipulator->sessionId);
    }

    public function testSetSessionIdFromRequest()
    {
        request()->merge(['session_id' => '1234']);
        /** @var \Sparors\Ussd\HasManipulators */
        $manipulator = $this->getMockForTrait(HasManipulators::class);
        $manipulator->setSessionIdFromRequest('session_id');
        $this->assertEquals('1234', $manipulator->sessionId);
    }

    public function testSetPhoneNumber()
    {
        /** @var \Sparors\Ussd\HasManipulators */
        $manipulator = $this->getMockForTrait(HasManipulators::class);
        $manipulator->setPhoneNumber('0545112466');
        $this->assertEquals('0545112466', $manipulator->phoneNumber);
    }

    public function testSetPhoneNumberFromRequest()
    {
        request()->merge(['phone_number' => '0545112466']);
        /** @var \Sparors\Ussd\HasManipulators */
        $manipulator = $this->getMockForTrait(HasManipulators::class);
        $manipulator->setPhoneNumberFromRequest('phone_number');
        $this->assertEquals('0545112466', $manipulator->phoneNumber);
    }

    public function testSetNetwork()
    {
        /** @var \Sparors\Ussd\HasManipulators */
        $manipulator = $this->getMockForTrait(HasManipulators::class);
        $manipulator->setNetwork('MTN');
        $this->assertEquals('MTN', $manipulator->network);
    }

    public function testSetNetworkFromRequest()
    {
        request()->merge(['network' => 'MTN']);
        /** @var \Sparors\Ussd\HasManipulators */
        $manipulator = $this->getMockForTrait(HasManipulators::class);
        $manipulator->setNetworkFromRequest('network');
        $this->assertEquals('MTN', $manipulator->network);
    }

    public function testSetInput()
    {
        /** @var \Sparors\Ussd\HasManipulators */
        $manipulator = $this->getMockForTrait(HasManipulators::class);
        $manipulator->setInput('1');
        $this->assertEquals('1', $manipulator->input);
    }

    public function testSetInputFromRequest()
    {
        request()->merge(['input' => '1']);
        /** @var \Sparors\Ussd\HasManipulators */
        $manipulator = $this->getMockForTrait(HasManipulators::class);
        $manipulator->setSessionIdFromRequest('input');
        $this->assertEquals('1', $manipulator->sessionId);
    }

    public function testSetStore()
    {
        /** @var \Sparors\Ussd\HasManipulators */
        $manipulator = $this->getMockForTrait(HasManipulators::class);
        $manipulator->setStore('redis');
        $this->assertEquals('redis', $manipulator->store);
    }

    public function testSet()
    {
        /** @var \Sparors\Ussd\HasManipulators */
        $manipulator = $this->getMockForTrait(HasManipulators::class);
        // Ensure the property exist so it can be set
        $manipulator->network = null;
        $manipulator->sessionId = null;
        $manipulator->phoneNumber = null;
        $manipulator->set([
            'network' => 'MTN',
            'session_id' => '1234',
            'phoneNumber' => '0545112466',
        ]);
        $this->assertEquals('MTN', $manipulator->network);
        $this->assertEquals('1234', $manipulator->sessionId);
        $this->assertEquals('0545112466', $manipulator->phoneNumber);
    }

    public function testSetFromRequest()
    {
        request()->merge([
            'op_network' => 'MTN',
            'session_id' => '1234',
            'phoneNumber' => '0545112466'
        ]);
        /** @var \Sparors\Ussd\HasManipulators */
        $manipulator = $this->getMockForTrait(HasManipulators::class);
        // Ensure the property exist so it can be set
        $manipulator->network = null;
        $manipulator->sessionId = null;
        $manipulator->phoneNumber = null;
        $manipulator->setFromRequest([
            'network' => 'op_network',
            'session_id',
            'phoneNumber',
        ]);
        $this->assertEquals('MTN', $manipulator->network);
        $this->assertEquals('1234', $manipulator->sessionId);
        $this->assertEquals('0545112466', $manipulator->phoneNumber);
    }

    public function testSetInitialStateCanTakeString()
    {
        /** @var \Sparors\Ussd\HasManipulators */
        $manipulator = $this->getMockForTrait(HasManipulators::class);
        $manipulator->setInitialState(Record::class);
        $this->assertEquals('Sparors\Ussd\Record', $manipulator->initialState);
    }

    public function testSetInitialStateCanTakeClassInstance()
    {
        /** @var \Sparors\Ussd\HasManipulators */
        $manipulator = $this->getMockForTrait(HasManipulators::class);
        $decision = new Decision();
        $manipulator->setInitialState($decision);
        $this->assertEquals('Sparors\Ussd\Decision', $manipulator->initialState);
    }

    public function testSetResponse()
    {
        /** @var \Sparors\Ussd\HasManipulators */
        $manipulator = $this->getMockForTrait(HasManipulators::class);
        $manipulator->setResponse(function (string $message, int $code) {
            return [
                'message' => $message,
                'state' => $code === 1 ? 'START' : 'END'
            ];
        });
        $this->assertIsCallable($manipulator->response);
        $this->assertEquals(
            function (string $message, int $code) {
                return [
                    'message' => $message,
                    'state' => $code === 1 ? 'START' : 'END'
                ];
            },
            $manipulator->response
        );
    }
}
