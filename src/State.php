<?php

namespace Sparors\Ussd;

abstract class State
{
    /** @var int */
    const CONTINUE = 1;

    /** @var int */
    const END = 2;

    /** @var int */
    protected $type = self::CONTINUE;

    /** @var Menu */
    protected $menu;

    /** @var Decision */
    protected $decision;

    /** @var Record */
    protected $record;

    /**
     * The function to run before the rendering
     */
    protected abstract function beforeRendering(): void;

    /**
     * The view to be displayed to users
     * 
     * @return string
     */
    public function render(): string
    {
        $this->menu = new Menu();
        $this->beforeRendering();
        return $this->menu->toString();
    }

    /**
     * The function to run after the rendering
     * 
     * @param string $argument
     */
    protected abstract function afterRendering(string $argument): void;

    /**
     * The new State full path
     */
    public function next(string $input): ?string
    {
        $this->decision = new Decision($input);
        $this->afterRendering($input);
        return $this->decision->outcome();
    }

    /**
     * @return int
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param Record $record
     */
    public function setRecord(Record $record)
    {
        $this->record = $record;
    }
}
