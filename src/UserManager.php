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
        if ($location == self::LOCAL) {
            $user = (new UserStorage())->getLocalUser($this->fileDir, $id);

            if (is_null($user)) {
                throw new UserNotFoundException();
            }

            if ($user === false) {
                throw new ServerConnectException();
            }
        } else {
            $user = (new UserServer())->getServerUser($this->client, $id);

            if ($user == 1) {
                throw new UserNotFoundException();
            }

            if ($user == 2) {
                throw new ServerConnectException();
            }
        }

        return $user;
    }

    public function getAllUsers(string $location): array
    {
        if ($location == self::LOCAL) {
            $users = (new UserStorage())->getLocalUsers($this->fileDir);

            if ($users === false) {
                throw new ServerConnectException();
            }
        } else {
            $users = (new UserServer())->getServerUsers($this->client);

            if ($users == 1) {
                return [];
            }

            if ($users == 2) {
                throw new ServerConnectException();
            }
        }

        return $users;
    }

    public function getUsersWithNameContaining(string $location, string $text): array
    {
        $users = $this->getAllUsers($location);

        if ($users == null) {
            return [];
        }

        return $this->filterUsersNameContaining($text, $users);
    }

    /**
     * @param string $text
     * @param array  $users
     *
     * @return array
     */
    private function filterUsersNameContaining(string $text, $users): array
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
}
