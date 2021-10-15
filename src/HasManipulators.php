<?php

namespace Sparors\Ussd;

use Closure;
use Illuminate\Support\Str;

trait HasManipulators
{
    public function setSessionId(string $sessionId)
    {
        $this->sessionId = $sessionId;

        return $this;
    }

    public function setSessionIdFromRequest(string $key)
    {
        $this->sessionId = request($key);

        return $this;
    }

    public function setPhoneNumber(?string $phoneNumber)
    {
        $this->phoneNumber = $phoneNumber;

        return $this;
    }

    public function setPhoneNumberFromRequest(string $key)
    {
        $this->phoneNumber = request($key);

        return $this;
    }

    public function setNetwork(?string $network)
    {
        $this->network = $network;

        return $this;
    }

    public function setNetworkFromRequest(string $key)
    {
        $this->network = request($key);
        return $this;
    }

    public function setInput(?string $input)
    {
        $this->input = $input;

        return $this;
    }

    public function setInputFromRequest(string $key)
    {
        $this->input = request($key);

        return $this;
    }

    public function setStore(?string $store)
    {
        $this->store = $store;

        return $this;
    }

    public function set(array $parameters)
    {
        foreach ($parameters as $property => $value) {
            $property = Str::camel($property);
            if (property_exists($this, $property)) {
                $this->$property = $value;
            }
        }

        return $this;
    }

    public function setFromRequest(array $parameters)
    {
        foreach ($parameters as $property => $key) {
            $property = Str::camel($property);
            if (property_exists($this, $property)) {
                $this->$property = request($key);
            } elseif (property_exists($this, Str::camel($key))) {
                $this->{Str::camel($key)} = request($key);
            }
        }

        return $this;
    }

    public function setInitialState($state)
    {
        if (is_object($state) && (!$state instanceof Closure)) {
            $this->initialState = get_class($state);
        } elseif (is_string($state) && class_exists($state)) {
            $this->initialState = $state;
        } elseif (is_callable($state)) {
            $this->initialState = $state;
        } else {
            $this->initialState = null;
        }

        return $this;
    }

    public function setResponse(callable $response)
    {
        $this->response = $response;

        return $this;
    }
}
