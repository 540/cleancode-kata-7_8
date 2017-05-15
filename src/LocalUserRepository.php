<?php

namespace Deg540\CleanCodeKata7_8;

use Exception;

class LocalUserRepository implements UserRepository
{
    /** @var string */
    private $fileDir;

    public function __construct(string $fileDir)
    {
        $this->fileDir = $fileDir;
    }

    public function findAll()
    {
        try {
            return unserialize(file_get_contents($this->fileDir));
        } catch (Exception $exception) {
            throw new ServerConnectException();
        }
    }

    public function findOneById(int $id)
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

        return array_shift($usersForId);
    }


}
