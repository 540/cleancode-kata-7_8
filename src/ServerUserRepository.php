<?php

namespace Deg540\CleanCodeKata7_8;

use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;

class ServerUserRepository extends UserRepository
{
    /** @var Client */
    private $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function findAll(): UserCollection
    {
        try {
            $users = json_decode($this->client->get('/users')->getBody()->getContents(), true);
        } catch (ClientException $exception) {
            return new UserCollection();
        } catch (Exception $exception) {
            throw new ServerConnectException();
        }

        $usersArray =  array_map(
            function ($user) {
                return $this->mapUserData($user);
            },
            $users
        );

        return new UserCollection($usersArray);
    }

    public function findOneById(int $id): User
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

}
