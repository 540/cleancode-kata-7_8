<?php

namespace Deg540\CleanCodeKata7_8;

use Exception;

class LocalUserRepository extends UserRepository
{
    /** @var string */
    private $fileDir;

    public function __construct(string $fileDir)
    {
        $this->fileDir = $fileDir;
    }

    public function findAll(): UserCollection
    {
        try {
            $users = array_map(
                function ($user) {
                    return $this->mapUserData($user);
                },
                unserialize(file_get_contents($this->fileDir))
            );

            return new UserCollection($users);
        } catch (Exception $exception) {
            throw new ServerConnectException();
        }
    }

    public function findOneById(int $id): User
    {
        try {
            $user = unserialize(file_get_contents($this->fileDir));
        } catch (Exception $exception) {
            throw new ServerConnectException();
        }

        $usersForId = array_filter(
            $user,
            function ($user) use ($id) {
                return $user['id'] == $id;
            }
        );

        if (empty($usersForId)) {
            throw new UserNotFoundException();
        }

        return $this->mapUserData(array_shift($usersForId));
    }
}
