<?php

namespace Sparors\Ussd;

/** @since v1.1.0 */
abstract class Action
{
    /** @var Record */
    protected $record;

    public abstract function run(): string;

    /**
     * @param Record $record
     */
    public function setRecord(Record $record)
    {
        $this->record = $record;
    }
}
