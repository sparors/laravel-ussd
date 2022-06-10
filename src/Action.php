<?php

namespace Sparors\Ussd;

/** @since v2.0.0 */
abstract class Action
{
    /** @var Record */
    protected $record;

    abstract public function run(): string;

    /**
     * @param Record $record
     */
    public function setRecord(Record $record): self
    {
        $this->record = $record;

        return $this;
    }
}
