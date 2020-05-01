<?php

namespace Sparors\Ussd;

abstract class State
{
    // Just two needed
    const START = 1;
    const CONTINUE = 2;
    const END = 3;

    /** @var int */
    protected $type = self::CONTINUE;

    /** @var \Sparors\Ussd\Menu */
    protected $menu;

    /** @var \Sparors\Ussd\Decision */
    protected $decision;

    public final function __construct()
    {
        // All State constructors should has no parameter
    }

    /**
     * The menu to be displayed to users
     */
    protected abstract function prepareMenu(): void;

    /**
     * The view to be displayed to users
     */
    public final function render(): string
    {
        $this->menu = new Menu();
        $this->beforeRendering();
        $this->prepareMenu();
        return $this->menu->toString();
    }

    /**
     * The decision for the next state
     * 
     * @param string $argument
     */
    protected abstract function prepareDecision(string $argument): void;

    /**
     * The new State full path
     */
    public final function next(string $input): ?string
    {
        $this->decision = new Decision($input);
        $this->prepareDecision($input);
        return $this->decision->outcome();
    }

    /**
     * The function to run before the render method
     */
    private function beforeRendering(): void {}

    public function getType()
    {
        return $this->type;
    }
}
