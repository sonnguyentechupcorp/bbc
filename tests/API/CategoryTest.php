<?php

namespace Tests\API;

use App\Models\Category;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class CategoryTest extends TestCase
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

    protected function createCategory()
    {
        return Category::create([
            'name' => $this->faker->name(),
            'slug' => $this->faker->slug(),
        ]);
    }

    public function test_category_api_category_index_exists()
    {
        $response = $this->get(route('category.index'));

        $response->assertStatus(500);
    }

    public function test_category_api_category_index_none_authenticated()
    {
        $response = $this->get(route('category.index'), [
            'Accept' => 'application/json',
            'Authorization' => ''
        ]);

        $response->assertStatus(401)
            ->assertJsonStructure([
                "message",
            ]);
    }

    public function test_category_api_category_index_an_authenticated()
    {
        $user = $this->createUser();

        $token = $user->createToken('myapptoken')->plainTextToken;

        $response = $this->get(route('category.index'), [
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

    public function test_category_api_category_store_exists()
    {
        $response = $this->post(route('category.store'));

        $response->assertStatus(500);
    }

    public function test_category_api_category_store_none_authenticated()
    {
        $response = $this->post(route('category.store'), [], [
            'Accept' => 'application/json',
            'Authorization' => ''
        ]);

        $response->assertStatus(401)
            ->assertJsonStructure([
                "message",
            ]);
    }

    public function test_category_api_category_store_an_authenticated()
    {
        $user = $this->createUser();

        $token = $user->createToken('myapptoken')->plainTextToken;

        $response = $this->post(route('category.store'), [], [
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

    public function test_category_api_category_store_an_authenticated_validation_failed()
    {
        $category = $this->createCategory();

        $user = $this->createUser();

        $token = $user->createToken('myapptoken')->plainTextToken;

        $response = $this->post(route('category.store'), [], [
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

        $response = $this->post(route('category.store'), [
            'name' => '',
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

        $response = $this->post(route('category.store'), [
            'name' => $category->name,
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

        $response = $this->post(route('category.store'), [
            'slug' => $category->slug,
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

    public function test_category_api_category_store_an_authenticated_create_failed()
    {
        $category = $this->createCategory();

        $user = $this->createUser();

        $token = $user->createToken('myapptoken')->plainTextToken;

        $response = $this->post(route('category.store'), [
            'name' => $category->name,
            'slug' => $this->faker->slug
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

        $this->assertDatabaseMissing('categories', [
            'slug' => $this->faker->slug
        ]);

        $response = $this->post(route('category.store'), [
            'name' => $this->faker->name,
            'slug' => $category->slug
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

        $this->assertDatabaseMissing('categories', [
            'name' => $this->faker->name
        ]);

        $response = $this->post(route('category.store'), [
            'name' => '',
            'slug' => $this->faker->slug
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

        $this->assertDatabaseMissing('categories', [
            'slug' => $this->faker->slug
        ]);
    }

    public function test_category_api_category_store_an_authenticated_create_success()
    {
        $user = $this->createUser();

        $token = $user->createToken('myapptoken')->plainTextToken;

        $response = $this->post(route('category.store'), [
            'name' => 'nnnn',
            'slug' => 'nnnn'
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

        $this->assertDatabaseHas('categories', [
            'slug' => 'nnnn'
        ]);
    }

    public function test_category_api_category_update_exists()
    {
        $response = $this->put(route('category.edit', [1]));

        $response->assertStatus(500);
    }

    public function test_category_api_category_update_none_authenticated()
    {
        $response = $this->put(route('category.edit', [1]), [], [
            'Accept' => 'application/json',
            'Authorization' => ''
        ]);

        $response->assertStatus(401)
            ->assertJsonStructure([
                "message",
            ]);
    }

    public function test_category_api_category_update_an_authenticated_failed()
    {
        $category = $this->createCategory();

        $user = $this->createUser();

        $token = $user->createToken('myapptoken')->plainTextToken;

        $response = $this->put(route('category.edit', [1]), [
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

        $response = $this->put(route('category.edit', [1]), [
            'name' => $category->name
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

        $response = $this->put(route('category.edit', [1]), [
            'slug' => $category->slug
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

    public function test_category_api_category_update_an_authenticated_success()
    {

        $user = $this->createUser();

        $token = $user->createToken('myapptoken')->plainTextToken;

        $response = $this->put(route('category.edit', ['1']), [
            'name' => $this->faker->name(),
            'slug' => $this->faker->slug()
        ], [
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $token
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                "status",
                "locale",
                "message",
                "data",
            ]);
    }

    public function test_category_api_category_destroy_exists()
    {
        $response = $this->delete(route('category.destroy', [1]));

        $response->assertStatus(500);
    }

    public function test_category_api_category_destroy_none_authenticated()
    {
        $response = $this->delete(route('category.destroy', [1]), [], [
            'Accept' => 'application/json',
            'Authorization' => ''
        ]);

        $response->assertStatus(401)
            ->assertJsonStructure([
                "message",
            ]);
    }

    public function test_category_api_category_destroy_an_authenticated_failed()
    {
        $user = $this->createUser();

        $token = $user->createToken('myapptoken')->plainTextToken;

        $response = $this->delete(route('category.destroy', ['sadsad']), [], [
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

        $this->assertDatabaseMissing('categories', [
            'id' => 'sadsad'
        ]);

        $response = $this->delete(route('category.destroy', ['0']), [], [
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

        $this->assertDatabaseMissing('categories', [
            'id' => '0'
        ]);
    }

    public function test_category_api_category_destroy_an_authenticated_success()
    {
        $user = $this->createUser();

        $token = $user->createToken('myapptoken')->plainTextToken;

        $response = $this->delete(route('category.destroy', ['1']), [], [
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
}
