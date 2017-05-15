<?php

namespace Deg540\CleanCodeKata7_8;

use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;

class ServerUserRepository implements UserRepository
{
    /** @var Client */
    private $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function findAll()
    {
        try {
            $users = json_decode($this->client->get('/users')->getBody()->getContents(), true);
        } catch (ClientException $exception) {
            return [];
        } catch (Exception $exception) {
            throw new ServerConnectException();
        }

        return array_map(
            function ($user) {
                return $this->mapUserData($user);
            },
            $users
        );
    }

    public function findOneById(int $id)
    {
        try {
            $user = json_decode($this->client->get('/users/'.$id)->getBody()->getContents(), true);
        } catch (ClientException $exception) {
            throw new UserNotFoundException();
        } catch (Exception $exception) {
            throw new ServerConnectException();
        }

        return $this->mapUserData($user);
    }

    private function mapUserData(array $user): array
    {
        return array_intersect_key($user, ['id' => true, 'name' => true]);
    }
}
