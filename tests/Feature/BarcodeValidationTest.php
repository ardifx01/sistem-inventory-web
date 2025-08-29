<?php

use App\Models\User;
use App\Models\Category;
use App\Models\Item;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    // Create a user with admin role and authenticate
    $user = User::factory()->create(['role' => 'admin']);
    $this->actingAs($user);
    
    // Create a category manually
    Category::create(['id' => 1, 'name' => 'Test Category', 'is_default' => false]);
});

test('it accepts valid ean13 barcode', function () {
    $response = $this->post('/items', [
        'dscription' => 'Test Item',
        'itemCode' => 'TEST001',
        'codeBars' => '1234567890128', // Valid EAN-13 with correct check digit
        'category_id' => 1,
    ]);

    $response->assertRedirect('/items');
    $this->assertDatabaseHas('items', [
        'codeBars' => '1234567890128'
    ]);
});

test('it accepts valid upca barcode', function () {
    $response = $this->post('/items', [
        'dscription' => 'Test Item',
        'itemCode' => 'TEST002',
        'codeBars' => '123456789012', // Valid UPC-A with correct check digit
        'category_id' => 1,
    ]);

    $response->assertRedirect('/items');
    $this->assertDatabaseHas('items', [
        'codeBars' => '123456789012'
    ]);
});

test('it accepts valid ean8 barcode', function () {
    $response = $this->post('/items', [
        'dscription' => 'Test Item',
        'itemCode' => 'TEST003',
        'codeBars' => '12345670', // Valid EAN-8 with correct check digit
        'category_id' => 1,
    ]);

    $response->assertRedirect('/items');
    $this->assertDatabaseHas('items', [
        'codeBars' => '12345670'
    ]);
});

test('it accepts valid code128 barcode', function () {
    $response = $this->post('/items', [
        'dscription' => 'Test Item',
        'itemCode' => 'TEST004',
        'codeBars' => 'ABC123def', // Valid Code 128
        'category_id' => 1,
    ]);

    $response->assertRedirect('/items');
    $this->assertDatabaseHas('items', [
        'codeBars' => 'ABC123def'
    ]);
});

test('it rejects invalid ean13 check digit', function () {
    $response = $this->post('/items', [
        'dscription' => 'Test Item',
        'itemCode' => 'TEST005',
        'codeBars' => '1234567890123', // Invalid EAN-13 check digit
        'category_id' => 1,
    ]);

    $response->assertSessionHasErrors('codeBars');
});

test('it rejects invalid length numeric barcode', function () {
    $response = $this->post('/items', [
        'dscription' => 'Test Item',
        'itemCode' => 'TEST006',
        'codeBars' => '12345', // Invalid length
        'category_id' => 1,
    ]);

    $response->assertSessionHasErrors('codeBars');
});

test('it rejects duplicate barcode', function () {
    // Create first item
    Item::create([
        'dscription' => 'First Item',
        'itemCode' => 'FIRST001',
        'codeBars' => '1234567890128',
        'category_id' => 1,
    ]);

    // Try to create second item with same barcode
    $response = $this->post('/items', [
        'dscription' => 'Second Item',
        'itemCode' => 'SECOND001',
        'codeBars' => '1234567890128', // Duplicate barcode
        'category_id' => 1,
    ]);

    $response->assertSessionHasErrors('codeBars');
});

test('it accepts empty barcode', function () {
    $response = $this->post('/items', [
        'dscription' => 'Test Item',
        'itemCode' => 'TEST007',
        'codeBars' => '', // Empty barcode should be allowed
        'category_id' => 1,
    ]);

    $response->assertRedirect('/items');
    $this->assertDatabaseHas('items', [
        'itemCode' => 'TEST007',
        'codeBars' => null
    ]);
});
