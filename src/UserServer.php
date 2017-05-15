<?php

namespace Deg540\CleanCodeKata7_8;

use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;

class UserServer
{
    public function getServerUsers(Client $client)
    {
        try {
            $users = json_decode($client->get('/users')->getBody()->getContents(), true);
        } catch (ClientException $exception) {
            return 1;
        } catch (Exception $exception) {
            return 2;
        }

        return array_map(
            function ($user) {
                return $this->mapUserData($user);
            },
            $users
        );
    }

    public function getServerUser(Client $client, int $id)
    {
        try {
            $user = json_decode($client->get('/users/'.$id)->getBody()->getContents(), true);
        } catch (ClientException $exception) {
            return 1;
        } catch (Exception $exception) {
            return 2;
        }

        return $this->mapUserData($user);
    }

    private function mapUserData(array $user): array
    {
        return array_intersect_key($user, ['id' => true, 'name' => true]);
    }
}
