<?php

namespace Deg540\CleanCodeKata7_8;

class UserCollection implements \IteratorAggregate
{
    /** @var User[] */
    private $users;

    public function __construct(array $users = [])
    {
        $this->users = $users;
    }

    public function newWithNameContaining(string $text): self
    {
        return new self(
            array_values(
                array_filter(
                    $this->users,
                    function ($user) use ($text) {
                        return strpos($user->name(), $text) !== false;
                    }
                )
            )
        );
    }

    public function getIterator(): \ArrayIterator
    {
        return new \ArrayIterator($this->users);
    }

    public function getAtPosition(int $position): User
    {
        return $this->users[$position];
    }

    public function isEmpty(): bool
    {
        return empty($this->users);
    }
}
