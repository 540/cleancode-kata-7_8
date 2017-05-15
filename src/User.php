<?php

namespace Deg540\CleanCodeKata7_8;

class User
{
    /** @var int */
    private $id;

    /** @var string */
    private $name;

    /**
     * @param int    $id
     * @param string $name
     */
    public function __construct(int $id, string $name)
    {
        $this->id = $id;
        $this->name = $name;
    }

    /**
     * @return int
     */
    public function id(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function name(): string
    {
        return $this->name;
    }
}
