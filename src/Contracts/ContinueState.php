<?php

namespace Sparors\Ussd\Contracts;

interface ContinueState extends State
{
    public function confirm(): Decision;
}
