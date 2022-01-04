<?php

namespace Tests\API;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;

class UserTest extends TestCase
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

    public function test_user_api_user_index_exists()
    {
        $response = $this->get(route('user.index'));

        $response->assertStatus(500);
    }

    public function test_user_api_user_index_none_authenticated()
    {
        $response = $this->get(route('user.index'), [
            'Accept' => 'application/json',
            'Authorization' => ''
        ]);

        $response->assertStatus(401)
            ->assertJsonStructure([
                "message",
            ]);
    }

    public function test_user_api_user_index_an_authenticated()
    {
        $user = $this->createUser();

        $token = $user->createToken('myapptoken')->plainTextToken;

        $response = $this->get(route('user.index'), [
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $token
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                "status",
                "message",
                "data"
            ]);
    }

    public function test_user_api_user_show_exists()
    {
        $response = $this->get(route('user.show', [1]));

        $response->assertStatus(500);
    }

    public function test_user_api_user_show_none_authenticated()
    {
        $response = $this->get(route('user.show', [1]), [
            'Accept' => 'application/json',
            'Authorization' => ''
        ]);

        $response->assertStatus(401)
            ->assertJsonStructure([
                "message",
            ]);
    }

    public function test_user_api_user_show_an_authenticated_failed()
    {
        $user = $this->createUser();

        $token = $user->createToken('myapptoken')->plainTextToken;

        $response = $this->get(route('user.show', ['sadsad']), [
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $token
        ]);

        $response->assertStatus(404)
            ->assertJsonStructure([
                "message",
                "exception",
                "file",
                "line",
                "trace"
            ]);

        $response = $this->get(route('user.show', ['0']), [
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $token
        ]);

        $response->assertStatus(404)
            ->assertJsonStructure([
                "message",
                "exception",
                "file",
                "line",
                "trace"
            ]);
    }

    public function test_user_api_user_show_an_authenticated_success()
    {
        $user = $this->createUser();

        $token = $user->createToken('myapptoken')->plainTextToken;

        $response = $this->get(route('user.show', ['1']), [
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $token
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                "status",
                "locale",
                "message",
                "data"

            ]);

        $response = $this->get(route('user.show', ['14']), [
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $token
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                "status",
                "locale",
                "message",
                "data"

            ]);

        $response = $this->get(route('user.show', ['14asdasdasd123']), [
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $token
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                "status",
                "locale",
                "message",
                "data"

            ]);
    }

    public function test_user_api_user_store_exists()
    {
        $response = $this->post(route('user.store'));

        $response->assertStatus(500);
    }

    public function test_user_api_user_store_none_authenticated()
    {
        $response = $this->post(route('user.store'), [], [
            'Accept' => 'application/json',
            'Authorization' => ''
        ]);

        $response->assertStatus(401)
            ->assertJsonStructure([
                "message",
            ]);
    }

    public function test_user_api_user_store_an_authenticated()
    {
        $user = $this->createUser();

        $token = $user->createToken('myapptoken')->plainTextToken;

        $response = $this->post(route('user.store'), [], [
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $token
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

    public function test_user_api_user_store_an_authenticated_validation_failed()
    {
        $user = $this->createUser();

        $token = $user->createToken('myapptoken')->plainTextToken;

        $response = $this->post(route('user.store'), [], [
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $token
        ]);

        $response->assertStatus(422)
            ->assertJsonStructure([
                "status",
                "code",
                "locale",
                "message",
                "errors"

            ]);

        $response = $this->post(route('user.store'), [
            'name' => '',
            'email' => '',
            'password' => ''
        ], [
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $token
        ]);

        $response->assertStatus(422)
            ->assertJsonStructure([
                "status",
                "code",
                "locale",
                "message",
                "errors"

            ]);

        $response = $this->post(route('user.store'), [
            'name' => $this->faker->name(),
            'email' => $this->faker->email(),
            'password' => ''
        ], [
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $token
        ]);

        $response->assertStatus(422)
            ->assertJsonStructure([
                "status",
                "code",
                "locale",
                "message",
                "errors"

            ]);

        $response = $this->post(route('user.store'), [
            'name' => '',
            'email' => $this->faker->email(),
            'password' => '123'
        ], [
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $token
        ]);

        $response->assertStatus(422)
            ->assertJsonStructure([
                "status",
                "code",
                "locale",
                "message",
                "errors"

            ]);

        $response = $this->post(route('user.store'), [
            'name' => $this->faker->name(),
            'email' => '',
            'password' => '123'
        ], [
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $token
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

    public function test_user_api_user_store_an_authenticated_validation_success()
    {
        $user = $this->createUser();

        $token = $user->createToken('myapptoken')->plainTextToken;

        $response = $this->post(route('user.store'), [
            'name' => $this->faker->name(),
            'email' => $user->email,
            'password' => '123'
        ], [
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $token
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

    public function test_user_api_user_store_an_authenticated_create_failed()
    {
        $user = $this->createUser();

        $token = $user->createToken('myapptoken')->plainTextToken;

        $response = $this->post(route('user.store'), [
            'name' => 'son',
            'email' => $user->email,
            'password' => '123'
        ], [
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $token
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
    }

    public function test_user_api_user_store_an_authenticated_create_success()
    {
        $user = $this->createUser();

        $token = $user->createToken('myapptoken')->plainTextToken;

        $response = $this->post(route('user.store'), [
            'name' => $this->faker->name(),
            'email' => 'son',
            'password' => '123'
        ], [
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $token
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                "status",
                "locale",
                "message",
                "data"
            ]);

        $this->assertDatabaseHas('users', [
            'email' => 'son'
        ]);
    }

    public function test_user_api_user_destroy_exists()
    {
        $response = $this->delete(route('user.destroy', [1]));

        $response->assertStatus(500);
    }

    public function test_user_api_user_destroy_none_authenticated()
    {
        $response = $this->delete(route('user.destroy', [1]), [], [
            'Accept' => 'application/json',
            'Authorization' => ''
        ]);

        $response->assertStatus(401)
            ->assertJsonStructure([
                "message",
            ]);
    }

    public function test_user_api_user_destroy_an_authenticated_failed()
    {
        $user = $this->createUser();

        $token = $user->createToken('myapptoken')->plainTextToken;

        $response = $this->delete(route('user.destroy', ['sadsad']), [], [
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $token
        ]);

        $response->assertStatus(404)
            ->assertJsonStructure([
                "message",
                "exception",
                "file",
                "line",
                "trace"
            ]);

        $this->assertDatabaseMissing('users', [
            'id' => 'sadsad'
        ]);

        $response = $this->delete(route('user.destroy', ['0']), [], [
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $token
        ]);

        $response->assertStatus(404)
            ->assertJsonStructure([
                "message",
                "exception",
                "file",
                "line",
                "trace"
            ]);

        $this->assertDatabaseMissing('users', [
            'id' => '0'
        ]);

    }

    public function test_user_api_user_destroy_an_authenticated_success()
    {
        $user = $this->createUser();

        $token = $user->createToken('myapptoken')->plainTextToken;

        $response = $this->delete(route('user.destroy', ['1']), [], [
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $token
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                "status",
                "locale",
                "message",
            ]);

    }

    public function test_user_api_user_update_avatar_exists()
    {
        $response = $this->put(route('user.edit', [1]));

        $response->assertStatus(500);
    }

    public function test_user_api_user_update_avatar_none_authenticated()
    {
        $response = $this->put(route('user.edit', [1]), [], [
            'Accept' => 'application/json',
            'Authorization' => ''
        ]);

        $response->assertStatus(401)
            ->assertJsonStructure([
                "message",
            ]);
    }

    public function test_user_api_user_update_avatar_an_authenticated_validation_failed()
    {
        $user = $this->createUser();

        $token = $user->createToken('myapptoken')->plainTextToken;

        $response = $this->put(route('user.edit', [1]), [], [
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $token
        ]);

        $response->assertStatus(404)
            ->assertJsonStructure([
                "message",
                "exception",
                "file",
                "line",
                "trace"
            ]);

        $response = $this->put(route('user.edit', [1]), [
            'name' => ''
        ], [
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $token
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

    public function test_user_api_user_update_avatar_an_authenticated_validation_success()
    {
        $user = $this->createUser();

        $token = $user->createToken('myapptoken')->plainTextToken;

        $response = $this->put(route('user.edit', ['sadasdasd']), [
            'name' => $this->faker->name()
        ], [
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $token
        ]);

        $response->assertStatus(404)
            ->assertJsonStructure([
                "message",
                "exception",
                "file",
                "line",
                "trace",

            ]);
    }
}
