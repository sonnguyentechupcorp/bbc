<?php

namespace Tests\Unit;

use App\Models\User;
use Carbon\Carbon;
use Tests\TestCase;

class UserDatabaseTest extends TestCase
{
    /**
     * A basic unit test example.
     *
     * @return void
     */
    protected function createUser()
    {
        return User::create([
            'name' => $this->faker->name(),
            'email' => $this->faker->email(),
            'password' => '123456',
            'birth_date' => Carbon::parse('1998-10-10'),
            'gender' => 1,
            'role' => ["User"],
        ]);
    }

    /**
     * A basic unit test example.
     *
     * @return void
     */

    public function test_insert_data_to_database_success()
    {
        $user = $this->createUser();

        $this->assertModelExists($user);
    }

    /**
     * A basic unit test example.
     *
     * @return void
     */

    public function test_insert_data_to_database_failed()
    {
        $name = $this->faker->name();

        try {
            User::create([
                'name' => $name,
                'email' => $this->faker->email(),
                'password' => '123456',
                'birth_date' => Carbon::parse('1998-10-10'),
                'gender' => 1,
                'role' => null,
            ]);
        } catch (\Exception $e) {
            $this->assertDatabaseMissing('users', [
                'name' => $name
            ]);
        }

        try {
            User::create([
                'name' => $name,
                'email' => $this->faker->email(),
                'password' => '123456',
                'birth_date' => '',
                'gender' => 1,
                'role' => ["User"],
            ]);
        } catch (\Exception $e) {
            $this->assertDatabaseMissing('users', [
                'name' => $name
            ]);
        }

        try {
            User::create([
                'name' => $name,
                'email' => $this->faker->email(),
                'password' => '123456',
                'birth_date' => Carbon::parse('1998-10-10'),
                'gender' => '',
                'role' => ["User"],
            ]);
        } catch (\Exception $e) {
            $this->assertDatabaseMissing('users', [
                'name' => $name
            ]);
        }

        try {
            User::create([
                'name' => $name,
                'email' => '',
                'password' => '123456',
                'birth_date' => Carbon::parse('1998-10-10'),
                'gender' => 1,
                'role' => ["User"],
            ]);
        } catch (\Exception $e) {
            $this->assertDatabaseMissing('users', [
                'name' => $name
            ]);
        }
    }

    public function test_update_data_to_database_success()
    {
        $user = $this->createUser();

        $this->assertModelExists($user);

        $user->update(['name' => $this->faker->name()]);

        $this->assertModelExists($user);
    }

    public function test_update_data_to_database_failed()
    {
        $user = $this->createUser();

        try {
            $updateStatus = $user->update(['birth_date' => '']);

            $this->assertFalse($updateStatus);
        } catch (\Exception $e) {
            $this->assertTrue(true);
        }

        try {
            $updateStatus = $user->update(['email' => '']);

            $this->assertFalse($updateStatus);
        } catch (\Exception $e) {
            $this->assertTrue(true);
        }

        try {
            $updateStatus = $user->update(['gender' => '']);

            $this->assertFalse($updateStatus);
        } catch (\Exception $e) {
            $this->assertTrue(true);
        }
    }
}
