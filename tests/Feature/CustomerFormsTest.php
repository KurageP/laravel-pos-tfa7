<?php

namespace Tests\Feature;

use App\Models\Customer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerFormsTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_redirects_to_the_customer_directory(): void
    {
        $this->get('/')->assertRedirect('/customers');
    }

    public function test_directory_and_create_form_are_available(): void
    {
        $this->get('/customers')->assertOk()->assertSee('Create customer');
        $this->get('/customers/create')->assertOk()->assertSee('name="email"', false);
    }

    public function test_customer_can_be_created_with_an_optional_phone(): void
    {
        $this->post('/customers', ['full_name' => 'Lanz Villaruel', 'email' => 'lanz@example.com'])
            ->assertRedirect('/customers')->assertSessionHas('success');
        $this->assertDatabaseHas('customers', [
            'full_name' => 'Lanz Villaruel', 'email' => 'lanz@example.com', 'phone' => '',
        ]);
    }

    public function test_required_fields_are_validated(): void
    {
        $this->from('/customers/create')->post('/customers', [])
            ->assertRedirect('/customers/create')->assertSessionHasErrors(['full_name', 'email']);
        $this->assertDatabaseCount('customers', 0);
    }

    public function test_invalid_email_preserves_input_and_displays_errors(): void
    {
        $this->from('/customers/create')->post('/customers', [
            'full_name' => 'Keep This Name', 'email' => 'invalid-email', 'phone' => '09171234567',
        ])->assertRedirect('/customers/create')->assertSessionHasErrors('email')
            ->assertSessionHasInput('full_name', 'Keep This Name');
        $this->get('/customers/create')->assertOk()
            ->assertSee('value="Keep This Name"', false)
            ->assertSee('value="09171234567"', false)
            ->assertSee('Please enter a valid email address.');
        $this->assertDatabaseCount('customers', 0);
    }

    public function test_edit_is_prefilled_and_updates_only_the_selected_record(): void
    {
        $customer = Customer::create(['full_name' => 'Original Customer', 'email' => 'old@example.com', 'phone' => '123']);
        $other = Customer::create(['full_name' => 'Other Customer', 'email' => 'other@example.com', 'phone' => '456']);
        $this->get('/customers/'.$customer->id.'/edit')->assertOk()
            ->assertSee('value="Original Customer"', false)->assertSee('value="old@example.com"', false);
        $this->put('/customers/'.$customer->id, [
            'full_name' => 'Updated Customer', 'email' => 'new@example.com', 'phone' => '789',
        ])->assertRedirect('/customers');
        $this->assertDatabaseHas('customers', ['id' => $customer->id, 'full_name' => 'Updated Customer', 'phone' => '789']);
        $this->assertSame('Other Customer', $other->fresh()->full_name);
    }

    public function test_invalid_update_does_not_change_the_record(): void
    {
        $customer = Customer::create(['full_name' => 'Original', 'email' => 'original@example.com', 'phone' => '123']);
        $this->put('/customers/'.$customer->id, ['full_name' => 'New', 'email' => 'bad'])
            ->assertSessionHasErrors('email');
        $this->assertSame('Original', $customer->fresh()->full_name);
    }

    public function test_missing_customer_returns_404(): void
    {
        $this->get('/customers/999/edit')->assertNotFound();
        $this->put('/customers/999', ['full_name' => 'Test', 'email' => 'test@example.com'])->assertNotFound();
    }
}
