<?php

namespace Tests\Unit;

use App\Models\Post;
use Tests\TestCase;

class PostDatabaseTest extends TestCase
{
    /**
     * A basic unit test example.
     *
     * @return void
     */
    protected function createPost()
    {
        return Post::create([
            'title' => $this->faker->title(),
            'body' => 'a',
            'author_id' => '1'
        ]);
    }

    /**
     * A basic unit test example.
     *
     * @return void
     */

    public function test_insert_data_to_database_success()
    {
        $user = $this->createPost();

        $this->assertModelExists($user);
    }

    /**
     * A basic unit test example.
     *
     * @return void
     */

    public function test_insert_data_to_database_failed()
    {
        $post = $this->createPost();

        try {
            Post::create([
                'title' => '',
                'body' => 'ab',
                'author_id' => '1'

            ]);
        } catch (\Exception $e) {
            $this->assertDatabaseMissing('posts', [
                'title' => ''
            ]);
        }

        try {
            Post::create([
                'title' => $post->title,
                'body' => 'ab',
                'author_id' => '1'

            ]);
        } catch (\Exception $e) {
            $this->assertDatabaseMissing('posts', [
                'title' => 'ab'
            ]);
        }

        try {
            Post::create([
                'title' => 'bbmm',
                'body' => 'ab',
                'author_id' => ''

            ]);
        } catch (\Exception $e) {
            $this->assertDatabaseMissing('posts', [
                'title' => 'bbmm'
            ]);
        }

        try {
            Post::create([
                'title' => 'bbnn',
                'body' => 'abc',
                'author_id' => 'sadsad'

            ]);
        } catch (\Exception $e) {
            $this->assertDatabaseMissing('posts', [
                'title' => 'bbnn'
            ]);
        }

        try {
            Post::create([
                'title' => 'aaa',
                'body' => 'abcd',
                'author_id' => '0'

            ]);
        } catch (\Exception $e) {
            $this->assertDatabaseMissing('posts', [
                'title' => 'aaa'
            ]);
        }
    }

    public function test_update_data_to_database_success()
    {
        $post = $this->createPost();

        $this->assertModelExists($post);

        $post->update(['title' => $this->faker->title()]);

        $this->assertModelExists($post);
    }

    public function test_update_data_to_database_failed()
    {
        $post = $this->createPost();

        try {
            $updateStatus = $post->update(['title' => '']);

            $this->assertFalse($updateStatus);
        } catch (\Exception $e) {
            $this->assertTrue(true);
        }

        try {
            $updateStatus = $post->update(['author_id' => '']);

            $this->assertFalse($updateStatus);
        } catch (\Exception $e) {
            $this->assertTrue(true);
        }

        try {
            $updateStatus = $post->update(['author_id' => 'sdsadad']);

            $this->assertFalse($updateStatus);
        } catch (\Exception $e) {
            $this->assertTrue(true);
        }

        try {
            $updateStatus = $post->update(['author_id' => '0']);

            $this->assertFalse($updateStatus);
        } catch (\Exception $e) {
            $this->assertTrue(true);
        }
    }
}
