<?php

declare(strict_types=1);

namespace Deg540\CleanCodeKata7_8\Test;

use Deg540\CleanCodeKata7_8\UserManager;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use VCR\VCR;

final class UserManagerTest extends TestCase
{
    /**z
     * @var UserManager
     */
    private $userManager;

    /**
     * @before
     */
    protected function setUp()
    {
        VCR::configure()->setCassettePath(__DIR__.'/../tests/fixtures');
        VCR::turnOn();
        VCR::insertCassette('remote_server.yml');

        $this->userManager = new UserManager();
    }

    /**
     * @test
     */
    public function get_single_user1_locally()
    {
        $this->assertEquals(
            ['id' => 1, 'name' => 'Leanne Graham'],
            $this->userManager->getUser(UserManager::LOCAL, 1)
        );
    }

    /**
     * @test
     */
    public function get_single_user1_remotely()
    {
        $this->assertEquals(
            ['id' => 1, 'name' => 'Leanne Graham'],
            $this->userManager->getUser(UserManager::REMOTE, 1)
        );
    }

    /**
     * @test
     */
    public function get_single_user2_locally()
    {
        $this->assertEquals(
            ['id' => 2, 'name' => 'Ervin Howell'],
            $this->userManager->getUser(UserManager::LOCAL, 2)
        );
    }

    /**
     * @test
     */
    public function get_single_user2_remotely()
    {
        $this->assertEquals(
            ['id' => 2, 'name' => 'Ervin Howell'],
            $this->userManager->getUser(UserManager::REMOTE, 2)
        );
    }

    /**
     * @test
     * @expectedException \Deg540\CleanCodeKata7_8\UserNotFoundException
     */
    public function throws_exception_for_not_found_local_user()
    {
        $this->userManager->getUser(UserManager::LOCAL, 200);
    }

    /**
     * @test
     * @expectedException \Deg540\CleanCodeKata7_8\UserNotFoundException
     */
    public function throws_exception_for_not_found_remote_user()
    {
        $this->userManager->getUser(UserManager::REMOTE, 200);
    }

    /**
     * @test
     */
    public function gets_all_users_locally()
    {
        $users = $this->userManager->getAllUsers(UserManager::LOCAL);

        $this->assertCount(10, $users);
        $this->assertEquals(['id' => 1, 'name' => 'Leanne Graham'], $users[0]);
        $this->assertEquals(['id' => 10, 'name' => 'Clementina DuBuque'], $users[9]);
    }

    /**
     * @test
     */
    public function gets_all_users_remotely()
    {
        $users = $this->userManager->getAllUsers(UserManager::REMOTE);

        $this->assertCount(10, $users);
        $this->assertEquals(['id' => 1, 'name' => 'Leanne Graham'], $users[0]);
        $this->assertEquals(['id' => 10, 'name' => 'Clementina DuBuque'], $users[9]);
    }

    /**
     * @test
     */
    public function returns_empty_array_for_no_user_available_locally()
    {
        $this->mockContextToFetchEmptyUserList();

        $this->assertEmpty($this->userManager->getAllUsers(UserManager::LOCAL));
    }

    /**
     * @test
     */
    public function returns_empty_array_for_no_user_available_remotely()
    {
        $this->mockContextToFetchEmptyUserList();

        $this->assertEmpty($this->userManager->getAllUsers(UserManager::REMOTE));
    }

    /**
     * @test
     */
    public function filters_local_users_containing_text_in_name()
    {
        $users = $this->userManager->getUsersWithNameContaining(UserManager::LOCAL, 'DuBuque');

        $this->assertCount(1, $users);
        $this->assertEquals(['id' => 10, 'name' => 'Clementina DuBuque'], $users[0]);
    }

    /**
     * @test
     */
    public function filters_remote_users_containing_text_in_name()
    {
        $users = $this->userManager->getUsersWithNameContaining(UserManager::REMOTE, 'DuBuque');

        $this->assertCount(1, $users);
        $this->assertEquals(['id' => 10, 'name' => 'Clementina DuBuque'], $users[0]);
    }

    /**
     * @test
     */
    public function returns_empty_array_for_no_local_user_containing_text_in_name()
    {
        $this->assertEmpty($this->userManager->getUsersWithNameContaining(UserManager::LOCAL, 'Any other text'));
    }

    /**
     * @test
     */
    public function returns_empty_array_for_no_remote_user_containing_text_in_name()
    {
        $this->assertEmpty($this->userManager->getUsersWithNameContaining(UserManager::REMOTE, 'Any other text'));
    }

    /**
     * @test
     * @expectedException \Deg540\CleanCodeKata7_8\ServerConnectException
     */
    public function get_local_user_throws_exception_for_server_connection_error()
    {
        $this->mockContextToFailOnFetchUsersList();

        $this->userManager->getUser(UserManager::LOCAL, 200);
    }

    /**
     * @test
     * @expectedException \Deg540\CleanCodeKata7_8\ServerConnectException
     */
    public function get_remote_user_throws_exception_for_server_connection_error()
    {
        $this->mockContextToFailOnFetchUsersList();

        $this->userManager->getUser(UserManager::REMOTE, 200);
    }

    /**
     * @test
     * @expectedException \Deg540\CleanCodeKata7_8\ServerConnectException
     */
    public function get_all_local__users_throws_exception_for_server_connection_error()
    {
        $this->mockContextToFailOnFetchUsersList();

        $this->userManager->getAllUsers(UserManager::LOCAL);
    }

    /**
     * @test
     * @expectedException \Deg540\CleanCodeKata7_8\ServerConnectException
     */
    public function get_all_remote__users_throws_exception_for_server_connection_error()
    {
        $this->mockContextToFailOnFetchUsersList();

        $this->userManager->getAllUsers(UserManager::REMOTE);
    }

    /**
     * @return UserManager
     */
    private function mockContextToFailOnFetchUsersList(): UserManager
    {
        $client = $this->prophesize(Client::class);
        $client->get(Argument::any())->willThrow(Exception::class);

        $reflectionClass = new \ReflectionClass(UserManager::class);

        $reflectionProperty = $reflectionClass->getProperty('client');
        $reflectionProperty->setAccessible(true);
        $reflectionProperty->setValue($this->userManager, $client->reveal());

        $reflectionProperty = $reflectionClass->getProperty('fileDir');
        $reflectionProperty->setAccessible(true);
        $reflectionProperty->setValue($this->userManager, __DIR__.'/../res/users_no_available');

        return $this->userManager;
    }

    /**
     * @return UserManager
     */
    private function mockContextToFetchEmptyUserList(): UserManager
    {
        $client = $this->prophesize(Client::class);
        $client->get(Argument::any())->willThrow(ClientException::class);

        $reflectionClass = new \ReflectionClass(UserManager::class);

        $reflectionProperty = $reflectionClass->getProperty('client');
        $reflectionProperty->setAccessible(true);
        $reflectionProperty->setValue($this->userManager, $client->reveal());

        $reflectionProperty = $reflectionClass->getProperty('fileDir');
        $reflectionProperty->setAccessible(true);
        $reflectionProperty->setValue($this->userManager, __DIR__.'/../res/users_empty');

        return $this->userManager;
    }
}
