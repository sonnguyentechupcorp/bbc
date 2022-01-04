<?php

namespace Tests\API;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;

class AuthTest extends TestCase
{
    protected function createUser()
    {
        return User::create([
            'name' => $this->faker->name(),
            'email' => $this->faker->email(),
            'password' => Hash::make('123456'),
            'birth_date' => Carbon::parse('1998-10-10'),
            'gender' => 1,
            'role' => ["User"],
        ]);
    }

    public function test_login_api_exists()
    {
        $response = $this->post(route('auth.login'));

        $response->assertStatus(422);
    }

    public function test_login_api_validation_failed()
    {
        $response = $this->post(route('auth.login'), [
            'email' => '',
            'password' => ''
        ]);

        $response->assertStatus(422)
            ->assertJsonStructure([
                "status",
                "code",
                "locale",
                "message",
                "errors"
            ]);

        $response = $this->post(route('auth.login'), [
            'email' => $this->faker->email(),
            'password' => '123456'
        ]);

        $response->assertStatus(422)
            ->assertJsonStructure([
                "status",
                "code",
                "locale",
                "message",
                "errors"
            ]);
    }

    public function test_login_api_validation_success()
    {
        $user = $this->createUser();

        $response = $this->post(route('auth.login'), [
            'email' => $user->email,
            'password' => '123'
        ]);

        $response->assertStatus(401)
            ->assertJsonStructure([
                "status",
                "message",
            ]);
    }

    public function test_login_api_login_failed()
    {
        $user = $this->createUser();

        $response = $this->post(route('auth.login'), [
            'email' => $user->email,
            'password' => '123'
        ]);

        $response->assertStatus(401)
            ->assertJsonStructure([
                "status",
                "message",
            ]);
    }

    public function test_login_api_login_success()
    {
        $user = $this->createUser();

        $response = $this->post(route('auth.login'), [
            'email' => $user->email,
            'password' => '123456'
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                "status",
                "locale",
                "message",
                "data"
            ]);
    }

    public function test_logout_api_exists()
    {
        $response = $this->post(route('auth.logout'));

        $response->assertStatus(500);
    }

    public function test_logout_api_logout_falied()
    {
        $response = $this->post(route('auth.logout'), [], [
            'Accept' => '',
            'Authorization' => ''
        ]);

        $response->assertStatus(500);

        $response = $this->post(route('auth.logout'), [], [
            'Accept' => 'application/json',
            'Authorization' => ''
        ]);

        $response->assertStatus(401)
            ->assertJsonStructure([
                "message"
            ]);
    }

    public function test_logout_api_logout_success()
    {
        $user = $this->createUser();

        $token = $user->createToken('myapptoken')->plainTextToken;

        $response = $this->post(route('auth.logout'), [], [
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $token
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                "status",
                "locale",
                "message"
            ]);
    }

    public function test_register_api_exists()
    {
        $response = $this->post(route('auth.register'));

        $response->assertStatus(422);
    }

    public function test_register_api_validation_failed()
    {
        $response = $this->post(route('auth.register'), [
            'name' => '',
            'email' => '',
            'password' => ''
        ]);

        $response->assertStatus(422)
            ->assertJsonStructure([
                "status",
                "code",
                "locale",
                "message",
                "errors"
            ]);

        $response = $this->post(route('auth.register'), [
            'name' => $this->faker->name(),
            'email' => 'son@gmail.com',
            'password' => ''
        ]);

        $response->assertStatus(422)
            ->assertJsonStructure([
                "status",
                "code",
                "locale",
                "message",
                "errors"
            ]);

        $response = $this->post(route('auth.register'), [
            'name' => $this->faker->name(),
            'email' => '',
            'password' => '123456'
        ]);

        $response->assertStatus(422)
            ->assertJsonStructure([
                "status",
                "code",
                "locale",
                "message",
                "errors"
            ]);

        $response = $this->post(route('auth.register'), [
            'name' => '',
            'email' => 'son@gmail.com',
            'password' => '123456'
        ]);

        $response->assertStatus(422)
            ->assertJsonStructure([
                "status",
                "code",
                "locale",
                "message",
                "errors"
            ]);

        $response = $this->post(route('auth.register'), [
            'name' => $this->faker->name(),
            'email' => 'son@gmail.com',
            'password' => '123456',
            'password_confirmation' => ''
        ]);

        $response->assertStatus(422)
            ->assertJsonStructure([
                "status",
                "code",
                "locale",
                "message",
                "errors"
            ]);

        $response = $this->post(route('auth.register'), [
            'name' => $this->faker->name(),
            'email' => 'son@gmail.com',
            'password' => '123456',
            'password_confirmation' => '123456',
            'gender' => 'asasdsad'
        ]);

        $response->assertStatus(422)
            ->assertJsonStructure([
                "status",
                "code",
                "locale",
                "message",
                "errors"
            ]);

        $response = $this->post(route('auth.register'), [
            'name' => $this->faker->name(),
            'email' => 'son@gmail.com',
            'password' => '123456',
            'password_confirmation' => '123456',
            'birth_date' => 'asasdsad'
        ]);

        $response->assertStatus(422)
            ->assertJsonStructure([
                "status",
                "code",
                "locale",
                "message",
                "errors"
            ]);

        $response = $this->post(route('auth.register'), [
            'name' => $this->faker->name(),
            'email' => 'son@gmail.com',
            'password' => '123456',
            'password_confirmation' => '123456',
            'gender' => 'asasdsad',
            'birth_date' => '2012-12-12'
        ]);

        $response->assertStatus(422)
            ->assertJsonStructure([
                "status",
                "code",
                "locale",
                "message",
                "errors"
            ]);
    }

    public function test_register_api_register_validation_success()
    {

        $response = $this->post(route('auth.register'), [
            'name' => 'sadsadsd',
            'email' => 'son123@gmail.com',
            'password' => '123',
            'password_confirmation' => '123'
        ]);

        $response->assertStatus(201);
    }

    public function test_register_api_register_failed()
    {
        $user = $this->createUser();

        $response = $this->post(route('auth.register'), [
            'name' => 'son',
            'email' => $user->email
        ]);

        $response->assertStatus(422)
            ->assertJsonStructure([
                "status",
                "code",
                "locale",
                "message",
                "errors"
            ]);

        $this->assertDatabaseMissing('users', [
            'name' => 'son'
        ]);

        $response = $this->post(route('auth.register'), [
            'name' => 'sadsadsd',
            'email' => 'adasdasd',
            'password' => '123',
            'password_confirmation' => '123'
        ]);

        $response->assertStatus(422)
            ->assertJsonStructure([
                "status",
                "code",
                "locale",
                "message",
                "errors"
            ]);

        $this->assertDatabaseMissing('users', [
            'email' => 'adasdasd'
        ]);
    }

    public function test_register_api_register_success()
    {
        $response = $this->post(route('auth.register'), [
            'name' => 'sadsadsd',
            'email' => 'son1234@gmail.com',
            'password' => '123',
            'password_confirmation' => '123'
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                "status",
                "locale",
                "message",
                "data"
            ]);

        $this->assertDatabaseHas('users', [
            'email' => 'son1234@gmail.com'
        ]);
    }
}
