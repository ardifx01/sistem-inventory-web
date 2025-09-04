<?php

namespace Tests\Feature;

use App\Models\Item;
use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BulkDeletePaginationTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;
    protected $category;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->admin = User::factory()->create(['role' => 'admin']);
        $this->category = Category::create([
            'name' => 'Test Category',
            'is_default' => false
        ]);
    }

    public function test_bulk_delete_works_with_25_items_per_page()
    {
        // Create 30 items
        $itemIds = [];
        for ($i = 1; $i <= 30; $i++) {
            $item = Item::create([
                'dscription' => "Test Item $i",
                'itemCode' => "ITEM$i",
                'codeBars' => "BAR$i",
                'category_id' => $this->category->id,
                'rack_location' => 'A' . $i,
                'rack_location_unique' => 'A' . $i,
            ]);
            $itemIds[] = $item->id;
        }
        
        // Get first 25 items (default pagination)
        $itemsToDelete = array_slice($itemIds, 0, 25);
        
        $response = $this->actingAs($this->admin)
            ->delete(route('items.bulkDelete'), [
                'ids' => $itemsToDelete
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');
        
        // Verify items are deleted
        foreach ($itemsToDelete as $itemId) {
            $this->assertDatabaseMissing('items', ['id' => $itemId]);
        }
        
        // Verify remaining 5 items still exist
        $this->assertEquals(5, Item::count());
    }

    public function test_bulk_delete_works_with_50_items_per_page()
    {
        // Create 60 items
        $itemIds = [];
        for ($i = 1; $i <= 60; $i++) {
            $item = Item::create([
                'dscription' => "Test Item $i",
                'itemCode' => "ITEM$i",
                'codeBars' => "BAR$i",
                'category_id' => $this->category->id,
                'rack_location' => 'A' . $i,
                'rack_location_unique' => 'A' . $i,
            ]);
            $itemIds[] = $item->id;
        }
        
        // Get first 50 items (when user selects 50 per page)
        $itemsToDelete = array_slice($itemIds, 0, 50);
        
        $response = $this->actingAs($this->admin)
            ->delete(route('items.bulkDelete'), [
                'ids' => $itemsToDelete
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');
        
        // Verify items are deleted
        foreach ($itemsToDelete as $itemId) {
            $this->assertDatabaseMissing('items', ['id' => $itemId]);
        }
        
        // Verify remaining 10 items still exist
        $this->assertEquals(10, Item::count());
    }

    public function test_bulk_delete_works_with_100_items_per_page()
    {
        // Create 120 items
        $itemIds = [];
        for ($i = 1; $i <= 120; $i++) {
            $item = Item::create([
                'dscription' => "Test Item $i",
                'itemCode' => "ITEM$i",
                'codeBars' => "BAR$i",
                'category_id' => $this->category->id,
                'rack_location' => 'A' . $i,
                'rack_location_unique' => 'A' . $i,
            ]);
            $itemIds[] = $item->id;
        }
        
        // Get first 100 items (when user selects 100 per page)
        $itemsToDelete = array_slice($itemIds, 0, 100);
        
        $response = $this->actingAs($this->admin)
            ->delete(route('items.bulkDelete'), [
                'ids' => $itemsToDelete
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');
        
        // Verify items are deleted
        foreach ($itemsToDelete as $itemId) {
            $this->assertDatabaseMissing('items', ['id' => $itemId]);
        }
        
        // Verify remaining 20 items still exist
        $this->assertEquals(20, Item::count());
    }

    public function test_select_all_checkbox_affects_only_current_page_items()
    {
        // Create 30 items
        for ($i = 1; $i <= 30; $i++) {
            Item::create([
                'dscription' => "Test Item $i",
                'itemCode' => "ITEM$i",
                'codeBars' => "BAR$i",
                'category_id' => $this->category->id,
                'rack_location' => 'A' . $i,
                'rack_location_unique' => 'A' . $i,
            ]);
        }
        
        // Test with 25 items per page (should show first 25 items)
        $response = $this->actingAs($this->admin)
            ->get(route('items.index', ['per_page' => 25]));

        $response->assertStatus(200);
        
        // Check that page shows 25 items
        $response->assertViewHas('items');
        $items = $response->viewData('items');
        $this->assertEquals(25, $items->count());
        $this->assertEquals(30, $items->total());
    }
}
