<?php

namespace Deg540\CleanCodeKata7_8;

interface UserRepository
{
    public function findAll();

    public function findOneById(int $id);
}