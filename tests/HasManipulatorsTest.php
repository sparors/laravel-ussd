<?php

namespace Sparors\Ussd\Tests;

use Orchestra\Testbench\TestCase;
use Sparors\Ussd\Decision;
use Sparors\Ussd\HasManipulators;
use Sparors\Ussd\Record;

class HasManipulatorsTest extends TestCase
{
    public function test_it_sets_session_id()
    {
        /** @var \Sparors\Ussd\HasManipulators */
        $manipulator = $this->getMockForTrait(HasManipulators::class);
        $manipulator->setSessionId('1234');
        $this->assertEquals('1234', $manipulator->sessionId);
    }

    public function test_it_sets_session_id_from_request()
    {
        request()->merge(['session_id' => '1234']);
        /** @var \Sparors\Ussd\HasManipulators */
        $manipulator = $this->getMockForTrait(HasManipulators::class);
        $manipulator->setSessionIdFromRequest('session_id');
        $this->assertEquals('1234', $manipulator->sessionId);
    }

    public function test_it_sets_phone_number()
    {
        /** @var \Sparors\Ussd\HasManipulators */
        $manipulator = $this->getMockForTrait(HasManipulators::class);
        $manipulator->setPhoneNumber('0545112466');
        $this->assertEquals('0545112466', $manipulator->phoneNumber);
    }

    public function test_it_sets_phone_number_from_request()
    {
        request()->merge(['phone_number' => '0545112466']);
        /** @var \Sparors\Ussd\HasManipulators */
        $manipulator = $this->getMockForTrait(HasManipulators::class);
        $manipulator->setPhoneNumberFromRequest('phone_number');
        $this->assertEquals('0545112466', $manipulator->phoneNumber);
    }

    public function test_it_sets_network()
    {
        /** @var \Sparors\Ussd\HasManipulators */
        $manipulator = $this->getMockForTrait(HasManipulators::class);
        $manipulator->setNetwork('MTN');
        $this->assertEquals('MTN', $manipulator->network);
    }

    public function test_it_sets_network_from_request()
    {
        request()->merge(['network' => 'MTN']);
        /** @var \Sparors\Ussd\HasManipulators */
        $manipulator = $this->getMockForTrait(HasManipulators::class);
        $manipulator->setNetworkFromRequest('network');
        $this->assertEquals('MTN', $manipulator->network);
    }

    public function test_it_sets_input()
    {
        /** @var \Sparors\Ussd\HasManipulators */
        $manipulator = $this->getMockForTrait(HasManipulators::class);
        $manipulator->setInput('1');
        $this->assertEquals('1', $manipulator->input);
    }

    public function test_it_sets_input_from_request()
    {
        request()->merge(['input' => '1']);
        /** @var \Sparors\Ussd\HasManipulators */
        $manipulator = $this->getMockForTrait(HasManipulators::class);
        $manipulator->setInputFromRequest('input');
        $this->assertEquals('1', $manipulator->input);
    }

    public function test_it_sets_store()
    {
        /** @var \Sparors\Ussd\HasManipulators */
        $manipulator = $this->getMockForTrait(HasManipulators::class);
        $manipulator->setStore('redis');
        $this->assertEquals('redis', $manipulator->store);
    }

    public function test_set_multiple_values_at_once()
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

    public function test_set_multiple_values_at_once_from_request()
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

    public function test_it_sets_initial_state_with_string_of_classname()
    {
        /** @var \Sparors\Ussd\HasManipulators */
        $manipulator = $this->getMockForTrait(HasManipulators::class);
        $manipulator->setInitialState(Record::class);
        $this->assertEquals('Sparors\Ussd\Record', $manipulator->initialState);
    }

    public function test_it_sets_initial_state_with_class_instance()
    {
        /** @var \Sparors\Ussd\HasManipulators */
        $manipulator = $this->getMockForTrait(HasManipulators::class);
        $decision = new Decision();
        $manipulator->setInitialState($decision);
        $this->assertEquals('Sparors\Ussd\Decision', $manipulator->initialState);
    }

    public function test_it_sets_initial_state_to_null_if_state_is_invalid()
    {
        /** @var \Sparors\Ussd\HasManipulators */
        $manipulator = $this->getMockForTrait(HasManipulators::class);
        $manipulator->setInitialState(1);
        $this->assertNull($manipulator->initialState);
    }

    public function test_it_set_response_formatting()
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
