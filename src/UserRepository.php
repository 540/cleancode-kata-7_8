<?php

namespace Deg540\CleanCodeKata7_8;

abstract class UserRepository
{
    public abstract function findAll(): UserCollection;

    public abstract function findOneById(int $id): User;

    protected function mapUserData(array $user): User
    {
        return new User($user['id'], $user['name']);
    }
}