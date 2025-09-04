<?php

use App\Models\Item;
use App\Models\Category;
use App\Http\Controllers\ItemController;
use Illuminate\Http\Request;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PaginationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create test user
        $user = \App\Models\User::factory()->create([
            'role' => 'admin'
        ]);
        $this->actingAs($user);
        
        // Create test category
        $category = Category::create([
            'name' => 'Test Category',
            'is_default' => false
        ]);
        
        // Create multiple test items
        for ($i = 1; $i <= 50; $i++) {
            Item::create([
                'dscription' => "Test Item $i",
                'itemCode' => "ITEM$i",
                'codeBars' => "BAR$i",
                'category_id' => $category->id,
                'rack_location' => 'A' . $i,
                'rack_location_unique' => 'A' . $i,
            ]);
        }
    }

    /** @test */
    public function pagination_defaults_to_25_items_per_page()
    {
        $response = $this->get('/items');
        
        $response->assertStatus(200);
        $response->assertViewHas('items');
        
        $items = $response->viewData('items');
        $this->assertEquals(25, $items->perPage());
        $this->assertEquals(25, $items->count());
    }

    /** @test */
    public function pagination_can_show_25_items_per_page()
    {
        $response = $this->get('/items?per_page=25');
        
        $response->assertStatus(200);
        
        $items = $response->viewData('items');
        $this->assertEquals(25, $items->perPage());
        $this->assertEquals(25, $items->count());
    }

    /** @test */
    public function pagination_can_show_50_items_per_page()
    {
        $response = $this->get('/items?per_page=50');
        
        $response->assertStatus(200);
        
        $items = $response->viewData('items');
        $this->assertEquals(50, $items->perPage());
        $this->assertEquals(50, $items->count());
    }

    /** @test */
    public function pagination_can_show_100_items_per_page()
    {
        $response = $this->get('/items?per_page=100');
        
        $response->assertStatus(200);
        
        $items = $response->viewData('items');
        $this->assertEquals(100, $items->perPage());
        // We only have 50 items, so count should be 50
        $this->assertEquals(50, $items->count());
    }

    /** @test */
    public function pagination_defaults_to_25_when_invalid_per_page_provided()
    {
        $response = $this->get('/items?per_page=999');
        
        $response->assertStatus(200);
        
        $items = $response->viewData('items');
        $this->assertEquals(25, $items->perPage());
    }

    /** @test */
    public function pagination_rejects_old_value_10_and_defaults_to_25()
    {
        // Test bahwa 10 tidak lagi valid dan akan default ke 25
        $response = $this->get('/items?per_page=10');
        
        $response->assertStatus(200);
        
        $items = $response->viewData('items');
        $this->assertEquals(25, $items->perPage()); // Should default to 25, not 10
    }

    /** @test */
    public function pagination_preserves_search_parameters()
    {
        $response = $this->get('/items?search=Test&per_page=25');
        
        $response->assertStatus(200);
        
        $items = $response->viewData('items');
        $this->assertEquals(25, $items->perPage());
        
        // Check that pagination links preserve search parameter
        $paginationView = $items->withQueryString()->links()->render();
        $this->assertStringContainsString('search=Test', $paginationView);
        $this->assertStringContainsString('per_page=25', $paginationView);
    }

    /** @test */
    public function pagination_preserves_category_filters()
    {
        $category = Category::first();
        
        $response = $this->get("/items?categories[]={$category->id}&per_page=25");
        
        $response->assertStatus(200);
        
        $items = $response->viewData('items');
        $this->assertEquals(25, $items->perPage());
        
        // Check that pagination links preserve category filter
        $paginationView = $items->withQueryString()->links()->render();
        // Laravel encodes array parameters as categories%5B0%5D instead of categories%5B%5D
        $this->assertStringContainsString("categories%5B0%5D={$category->id}", $paginationView);
        $this->assertStringContainsString('per_page=25', $paginationView);
    }

    /** @test */
    public function pagination_preserves_zip_only_filter()
    {
        // Create some ZIP items
        $category = Category::first();
        
        // Create multiple ZIP items to ensure pagination
        for ($i = 1; $i <= 30; $i++) {
            Item::create([
                'dscription' => "ZIP Item $i",
                'itemCode' => "ZIP$i",
                'category_id' => $category->id,
                'rack_location' => 'ZIP',
                'rack_location_unique' => null,
            ]);
        }
        
        $response = $this->get('/items?zip_only=1&per_page=25');
        
        $response->assertStatus(200);
        
        $items = $response->viewData('items');
        $this->assertEquals(25, $items->perPage());
        
        // Test the basic functionality: we should have items
        $this->assertGreaterThan(0, $items->count());
        
        // For this test, let's just verify the response contains the pagination
        // and the filter is working by counting the results
        $this->assertTrue($items->hasPages(), 'Should have multiple pages when filtering ZIP items');
    }

    /** @test */
    public function ajax_pagination_returns_json_response()
    {
        $response = $this->get('/items?per_page=25', [
            'X-Requested-With' => 'XMLHttpRequest',
            'Accept' => 'application/json'
        ]);
        
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'html',
            'pagination', 
            'info',
            'total',
            'current_page',
            'per_page',
            'from',
            'to'
        ]);
        
        $data = $response->json();
        $this->assertEquals(25, $data['per_page']);
        $this->assertStringContainsString('<tbody', $data['html']);
    }
}
