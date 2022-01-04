<?php

namespace Tests\Unit;

use App\Models\categories;
use App\Models\Category;
use Tests\TestCase;

class CategoryDatabaseTest extends TestCase
{
    /**
     * A basic unit test example.
     *
     * @return void
     */
    protected function createCategory()
    {
        return Category::create([
            'name' => $this->faker->name(),
            'slug' => $this->faker->slug(),
        ]);
    }

    /**
     * A basic unit test example.
     *
     * @return void
     */

    public function test_insert_data_to_database_success()
    {
        $category = $this->createCategory();

        $this->assertModelExists($category);
    }

    /**
     * A basic unit test example.
     *
     * @return void
     */

    public function test_insert_data_to_database_failed()
    {
        $category = $this->createCategory();

        try {
            Category::create([
                'name' => '',
                'slug' => 'ab',
            ]);
        } catch (\Exception $e) {
            $this->assertDatabaseMissing('categories', [
                'name' => ''
            ]);
        }

        try {
            Category::create([
                'name' => $category->name,
                'slug' => 'abc',
            ]);
        } catch (\Exception $e) {
            $this->assertDatabaseMissing('categories', [
                'slug' => 'abc'
            ]);
        }

        try {
            Category::create([
                'name' => 's',
                'slug' => $category->slug,
            ]);
        } catch (\Exception $e) {
            $this->assertDatabaseMissing('categories', [
                'name' => 's'
            ]);
        }
    }

    public function test_update_data_to_database_success()
    {
        $category = $this->createCategory();

        $this->assertModelExists($category);

        $category->update(['name' => $this->faker->name()]);

        $this->assertModelExists($category);
    }

    public function test_update_data_to_database_failed()
    {
        $category = $this->createCategory();

        try {
            $updateStatus = $category->update(['name' => '']);

            $this->assertFalse($updateStatus);
        } catch (\Exception $e) {
            $this->assertTrue(true);
        }

        try {
            $updateStatus = $category->update(['name' => $category->name]);

            $this->assertFalse($updateStatus);
        } catch (\Exception $e) {
            $this->assertTrue(true);
        }

        try {
            $updateStatus = $category->update(['slug' => $category->slug]);

            $this->assertFalse($updateStatus);
        } catch (\Exception $e) {
            $this->assertTrue(true);
        }
    }
}
