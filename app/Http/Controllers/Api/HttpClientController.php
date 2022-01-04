<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;

class HttpClientController extends Controller
{
    public function getAllPost()
    {
        $response = Http::get('https://jsonplaceholder.typicode.com/posts');

        return $response->json();
    }

    public function getPostById($id)
    {
        $post = Http::get('https://jsonplaceholder.typicode.com/posts/'.$id);

        return $post->json();
    }

    public function addPost()
    {
        $post = Http::post('https://jsonplaceholder.typicode.com/posts',[
            'userId' => '1',
            'title' => 'New Post',
            'body' => 'Description',
        ]);

        return $post->json();
    }

    public function updatePost($id)
    {
        $post = Http::put('https://jsonplaceholder.typicode.com/posts/' .$id,[
            'userId' => '1',
            'title' => 'Update Post',
            'body' => 'Update Description',
        ]);

        return $post->json();
    }

    public function deletePost($id)
    {
        $post = Http::delete('https://jsonplaceholder.typicode.com/posts/' .$id);

        return response()->json(['message' => 'Delete successfully']);
    }

}
