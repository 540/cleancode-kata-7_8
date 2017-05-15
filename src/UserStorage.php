<?php

namespace Deg540\CleanCodeKata7_8;

use Exception;

class UserStorage
{
    public function getLocalUsers(string $fileDir)
    {
        try {
            return unserialize(file_get_contents($fileDir));
        } catch (Exception $exception) {
            return false;
        }
    }

    public function getLocalUser(string $fileDir, int $id)
    {
        try {
            $user = unserialize(file_get_contents($fileDir));
        } catch (Exception $exception) {
            return false;
        }

        $usersForId = array_filter(
            $user,
            function ($user) use ($id) {
                return $user['id'] == $id;
            }
        );

        if (empty($usersForId)) {
            return null;
        }

        return array_shift($usersForId);
    }


}
