<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Item;
use App\Models\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ZipRackLocationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create test user
        $user = User::factory()->create([
            'role' => 'admin'
        ]);
        $this->actingAs($user);
        
        // Create test category
        Category::create([
            'name' => 'Test Category',
            'description' => 'Test Description'
        ]);
    }

    public function test_zip_rack_location_allows_duplicates()
    {
        // Create first item with ZIP location
        $item1 = Item::create([
            'dscription' => 'Item 1',
            'itemCode' => 'TEST001',
            'codeBars' => null,
            'rack_location' => 'ZIP',
            'category_id' => 1,
        ]);

        // Create second item with ZIP location (should be allowed)
        $item2 = Item::create([
            'dscription' => 'Item 2',
            'itemCode' => 'TEST002',
            'codeBars' => null,
            'rack_location' => 'ZIP',
            'category_id' => 1,
        ]);

        $this->assertEquals('ZIP', $item1->rack_location);
        $this->assertEquals('ZIP', $item2->rack_location);
        $this->assertNull($item1->rack_location_unique);
        $this->assertNull($item2->rack_location_unique);
    }

    public function test_regular_rack_location_must_be_unique()
    {
        // Create first item with regular rack location
        Item::create([
            'dscription' => 'Item 1',
            'itemCode' => 'TEST001',
            'codeBars' => null,
            'rack_location' => 'P01-01-01-01',
            'category_id' => 1,
        ]);

        // Try to create second item with same rack location (should fail)
        $this->expectException(\Illuminate\Database\QueryException::class);
        
        Item::create([
            'dscription' => 'Item 2',
            'itemCode' => 'TEST002',
            'codeBars' => null,
            'rack_location' => 'P01-01-01-01',
            'category_id' => 1,
        ]);
    }

    public function test_null_rack_location_becomes_zip()
    {
        $item = Item::create([
            'dscription' => 'Item with null location',
            'itemCode' => 'TEST003',
            'codeBars' => null,
            'rack_location' => null,
            'category_id' => 1,
        ]);

        // Should be automatically set to ZIP through observer
        $this->assertEquals('ZIP', $item->fresh()->rack_location);
        $this->assertNull($item->fresh()->rack_location_unique);
    }

    public function test_empty_rack_location_becomes_zip()
    {
        $item = Item::create([
            'dscription' => 'Item with empty location',
            'itemCode' => 'TEST004',
            'codeBars' => null,
            'rack_location' => '',
            'category_id' => 1,
        ]);

        // Should be automatically set to ZIP through observer
        $this->assertEquals('ZIP', $item->fresh()->rack_location);
        $this->assertNull($item->fresh()->rack_location_unique);
    }

    public function test_controller_allows_zip_duplicates()
    {
        // Create first item through controller
        $response1 = $this->post('/items', [
            'dscription' => 'Controller Item 1',
            'itemCode' => 'CTRL001',
            'codeBars' => '',
            'rack_location' => 'ZIP',
            'category_id' => 1,
        ]);

        // Create second item through controller (should succeed)
        $response2 = $this->post('/items', [
            'dscription' => 'Controller Item 2',
            'itemCode' => 'CTRL002',
            'codeBars' => '',
            'rack_location' => 'ZIP',
            'category_id' => 1,
        ]);

        $response1->assertRedirect();
        $response2->assertRedirect();
        
        $this->assertDatabaseHas('items', [
            'itemCode' => 'CTRL001',
            'rack_location' => 'ZIP'
        ]);
        
        $this->assertDatabaseHas('items', [
            'itemCode' => 'CTRL002',
            'rack_location' => 'ZIP'
        ]);
    }

    public function test_controller_rejects_duplicate_regular_locations()
    {
        // Create first item through controller
        $this->post('/items', [
            'dscription' => 'Controller Item 1',
            'itemCode' => 'CTRL003',
            'codeBars' => '',
            'rack_location' => 'P01-02-03-04',
            'category_id' => 1,
        ]);

        // Try to create second item with same location
        $response = $this->post('/items', [
            'dscription' => 'Controller Item 2',
            'itemCode' => 'CTRL004',
            'codeBars' => '',
            'rack_location' => 'P01-02-03-04',
            'category_id' => 1,
        ]);

        $response->assertSessionHasErrors('rack_location');
    }
}
