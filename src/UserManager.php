<?php

namespace Deg540\CleanCodeKata7_8;

use GuzzleHttp\Client;

class UserManager
{
    const LOCAL = 'local';
    const REMOTE = 'remote';

    /** @var Client */
    private $client;

    /** @var string */
    private $fileDir;

    public function __construct()
    {
        $this->fileDir = __DIR__.'/../res/users';
        $this->client = new Client(['base_uri' => 'https://jsonplaceholder.typicode.com']);
    }

    public function getUser(string $location, int $id): User
    {
        return $this->getUserRepository($location)->findOneById($id);
    }

    public function getAllUsers(string $location): UserCollection
    {
        return $this->getUserRepository($location)->findAll();
    }

    public function getUsersWithNameContaining(string $location, string $text): UserCollection
    {
        return $this->getAllUsers($location)->newWithNameContaining($text);
    }

    /**
     * @param string $location
     *
     * @return LocalUserRepository|ServerUserRepository
     */
    private function getUserRepository(string $location)
    {
        if ($location == self::LOCAL) {
            return new LocalUserRepository($this->fileDir);
        } else {
            return new ServerUserRepository($this->client);
        }
    }
}
