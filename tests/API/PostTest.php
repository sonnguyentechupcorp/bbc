<?php

namespace Tests\API;

use App\Models\Post;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PostTest extends TestCase
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

    protected function createPost()
    {
        return Post::create([
            'title' => $this->faker->title(),
            'body' => 'abc',
            'author_id' => 1,
        ]);
    }

    public function test_post_api_post_index_exists()
    {
        $response = $this->get(route('post.index'));

        $response->assertStatus(500);
    }

    public function test_post_api_post_index_none_authenticated()
    {
        $response = $this->get(route('post.index'), [
            'Accept' => 'application/json',
            'Authorization' => ''
        ]);

        $response->assertStatus(401)
            ->assertJsonStructure([
                "message",
            ]);
    }

    public function test_post_api_post_index_an_authenticated()
    {
        $user = $this->createUser();

        $token = $user->createToken('myapptoken')->plainTextToken;

        $response = $this->get(route('post.index'), [
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

    public function test_post_api_post_store_exists()
    {
        $response = $this->post(route('post.store'));

        $response->assertStatus(500);
    }

    public function test_post_api_post_store_none_authenticated()
    {
        $response = $this->post(route('post.store'), [], [
            'Accept' => 'application/json',
            'Authorization' => ''
        ]);

        $response->assertStatus(401)
            ->assertJsonStructure([
                "message",
            ]);
    }

    public function test_post_api_post_store_an_authenticated()
    {
        $user = $this->createUser();

        $token = $user->createToken('myapptoken')->plainTextToken;

        $response = $this->post(route('post.store'), [], [
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

    public function test_post_api_post_store_an_authenticated_validation_failed()
    {

        $user = $this->createUser();

        $token = $user->createToken('myapptoken')->plainTextToken;

        $response = $this->post(route('post.store'), [], [
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

        $response = $this->post(route('post.store'), [
            'title' => '',
            'author_id' => '',
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

        $response = $this->post(route('post.store'), [
            'title' => '',
            'author_id' => '1',
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

        $response = $this->post(route('post.store'), [
            'title' => $this->faker->title(),
            'author_id' => '',

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

        $response = $this->post(route('post.store'), [
            'title' => $this->faker->title(),
            'author_id' => '0',

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

    public function test_post_api_post_store_an_authenticated_create_failed()
    {
        $post = $this->createPost();

        $user = $this->createUser();

        $token = $user->createToken('myapptoken')->plainTextToken;

        $response = $this->post(route('post.store'), [
            'title' => $post->title,
            'body' => 'bbb',
            'author_id' => '1'
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

        $this->assertDatabaseMissing('posts', [
            'body' => 'bbb'
        ]);

        $response = $this->post(route('post.store'), [
            'title' => 'kkk',
            'body' => 'bbbb',
            'author_id' => '0'
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

        $this->assertDatabaseMissing('posts', [
            'body' => 'bbbb'
        ]);
    }

    public function test_post_api_post_store_an_authenticated_create_success()
    {
        $user = $this->createUser();

        $token = $user->createToken('myapptoken')->plainTextToken;

        $response = $this->post(route('post.store'), [
            'title' => 'son1111',
            'body' => 'abc',
            'author_id' => '2'
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

        $this->assertDatabaseHas('posts', [
            'title' => 'son1111'
        ]);
    }

    public function test_post_api_post_update_exists()
    {
        $response = $this->put(route('post.edit', [1]));

        $response->assertStatus(500);
    }

    public function test_post_api_post_update_none_authenticated()
    {
        $response = $this->put(route('post.edit', [1]), [], [
            'Accept' => 'application/json',
            'Authorization' => ''
        ]);

        $response->assertStatus(401)
            ->assertJsonStructure([
                "message",
            ]);
    }

    public function test_post_api_post_update_an_authenticated_validation_failed()
    {
        $user = $this->createUser();

        $token = $user->createToken('myapptoken')->plainTextToken;

        $response = $this->put(route('post.edit', [1]), [
            'title' => ''
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

        $response = $this->put(route('post.edit', [1]), [
            'author_id' => ''
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

        $response = $this->put(route('post.edit', [1]), [
            'author_id' => 'asdsad'
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
        $response = $this->put(route('post.edit', ['1']), [
            'title' => $this->faker->name(),
            'body' => 'lll',
            'author_id' => '1',
            'categories' => '1'
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

        // $response = $this->put(route('post.edit', ['1']), [
        //     'title' => $this->faker->name(),
        //     'body' => 'lll',
        //     'author_id' => '1',
        //     'categories[]' => 'dasdadad'
        // ],[
        //     'Accept' => 'application/json',
        //     'Authorization' => 'Bearer ' . $token
        // ]);

        // $response->assertStatus(422)
        //     ->assertJsonStructure([
        //         "status",
        //         "code",
        //         "locale",
        //         "message",
        //         "errors"
        //     ]);

        // $response = $this->put(route('post.edit', ['1']), [
        //     'title' => $this->faker->name(),
        //     'body' => 'lll',
        //     'author_id' => '1',
        //     'categories[]' => '100000'
        // ], [
        //     'Accept' => 'application/json',
        //     'Authorization' => 'Bearer ' . $token
        // ]);

        // $response->assertStatus(422)
        //     ->assertJsonStructure([
        //         "status",
        //         "code",
        //         "locale",
        //         "message",
        //         "errors"
        //     ]);
    }

    public function test_post_api_post_update_an_authenticated_success()
    {

        $user = $this->createUser();

        $token = $user->createToken('myapptoken')->plainTextToken;

        $response = $this->put(route('post.edit', ['1']), [
            'title' => $this->faker->name(),
            'body' => 'lll',
            'author_id' => '1',
            'categories[]' => '1',
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

        $this->assertDatabaseHas(
            'posts',
            [
                'body' => 'lll'
            ]
        );
    }

    // public function test_avatars_uploaded()
    // {
    //     Storage::fake('uploadFeatureImgPosts');

    //     $file = UploadedFile::fake()->image('abc.jpg');

    //     $response = $this->put(route('post.edit', ['1']), [
    //         'feature_img' => $file,
    //     ]);

    //     Storage::disk('uploadFeatureImgPosts')->assertExists($file->hashName());
    // }

    public function test_post_api_post_destroy_exists()
    {
        $response = $this->delete(route('post.destroy', [1]));

        $response->assertStatus(500);
    }

    public function test_post_api_post_destroy_none_authenticated()
    {
        $response = $this->delete(route('post.destroy', [1]), [], [
            'Accept' => 'application/json',
            'Authorization' => ''
        ]);

        $response->assertStatus(401)
            ->assertJsonStructure([
                "message",
            ]);
    }

    public function test_post_api_post_destroy_an_authenticated_failed()
    {
        $user = $this->createUser();

        $token = $user->createToken('myapptoken')->plainTextToken;

        $response = $this->delete(route('post.destroy', ['sadsad']), [], [
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

        $response = $this->delete(route('post.destroy', ['0']), [], [
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

    public function test_post_api_post_destroy_an_authenticated_success()
    {
        $user = $this->createUser();

        $token = $user->createToken('myapptoken')->plainTextToken;

        $response = $this->delete(route('post.destroy', ['1']), [], [
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
