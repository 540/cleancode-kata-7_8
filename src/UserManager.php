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

    public function getUser(string $location, int $id): array
    {
        return $this->getUserRepository($location)->findOneById($id);
    }

    public function getAllUsers(string $location): array
    {
        return $this->getUserRepository($location)->findAll();
    }

    public function getUsersWithNameContaining(string $location, string $text): array
    {
        $users = $this->getAllUsers($location);

        return $this->filterUsersNameContaining($users, $text);
    }

    /**
     * @param array  $users
     *
     * @param string $text
     *
     * @return array
     */
    private function filterUsersNameContaining($users, string $text): array
    {
        return array_values(
            array_filter(
                $users,
                function ($user) use ($text) {
                    return strpos($user['name'], $text) !== false;
                }
            )
        );
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
