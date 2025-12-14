<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthorizationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesAndPermissionsSeeder::class);
    }

    // =====================
    // Admin Dashboard Access
    // =====================

    public function test_owner_can_access_admin_dashboard(): void
    {
        $owner = User::factory()->create();
        $owner->assignRole('owner');

        $response = $this->actingAs($owner)->get(route('admin.dashboard'));

        $response->assertStatus(200);
    }

    public function test_admin_can_access_admin_dashboard(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $response = $this->actingAs($admin)->get(route('admin.dashboard'));

        $response->assertStatus(200);
    }

    public function test_worker_can_access_admin_dashboard(): void
    {
        $worker = User::factory()->create();
        $worker->assignRole('worker');

        $response = $this->actingAs($worker)->get(route('admin.dashboard'));

        $response->assertStatus(200);
    }

    public function test_client_cannot_access_admin_dashboard(): void
    {
        $client = User::factory()->create();
        $client->assignRole('client');

        $response = $this->actingAs($client)->get(route('admin.dashboard'));

        $response->assertStatus(403);
    }

    public function test_guest_cannot_access_admin_dashboard(): void
    {
        $response = $this->get(route('admin.dashboard'));

        $response->assertRedirect(route('login'));
    }

    // =====================
    // Products Management
    // =====================

    public function test_owner_can_manage_products(): void
    {
        $owner = User::factory()->create();
        $owner->assignRole('owner');

        $response = $this->actingAs($owner)->get(route('admin.products.index'));
        $response->assertStatus(200);

        $response = $this->actingAs($owner)->get(route('admin.products.create'));
        $response->assertStatus(200);
    }

    public function test_admin_can_manage_products(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $response = $this->actingAs($admin)->get(route('admin.products.index'));
        $response->assertStatus(200);
    }

    public function test_worker_can_view_products(): void
    {
        $worker = User::factory()->create();
        $worker->assignRole('worker');

        $response = $this->actingAs($worker)->get(route('admin.products.index'));
        $response->assertStatus(200);
    }

    public function test_client_cannot_access_products_admin(): void
    {
        $client = User::factory()->create();
        $client->assignRole('client');

        $response = $this->actingAs($client)->get(route('admin.products.index'));
        $response->assertStatus(403);
    }

    // =====================
    // Users Management (Owner/Admin Only)
    // =====================

    public function test_owner_can_manage_users(): void
    {
        $owner = User::factory()->create();
        $owner->assignRole('owner');

        $response = $this->actingAs($owner)->get(route('admin.users.index'));
        $response->assertStatus(200);
    }

    public function test_admin_can_manage_users(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $response = $this->actingAs($admin)->get(route('admin.users.index'));
        $response->assertStatus(200);
    }

    public function test_worker_cannot_manage_users(): void
    {
        $worker = User::factory()->create();
        $worker->assignRole('worker');

        $response = $this->actingAs($worker)->get(route('admin.users.index'));
        $response->assertStatus(403);
    }

    // =====================
    // Settings (Owner Only)
    // =====================

    public function test_owner_can_access_settings(): void
    {
        $owner = User::factory()->create();
        $owner->assignRole('owner');

        $response = $this->actingAs($owner)->get(route('admin.settings.index'));
        $response->assertStatus(200);
    }

    public function test_admin_cannot_access_settings(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $response = $this->actingAs($admin)->get(route('admin.settings.index'));
        $response->assertStatus(403);
    }

    public function test_worker_cannot_access_settings(): void
    {
        $worker = User::factory()->create();
        $worker->assignRole('worker');

        $response = $this->actingAs($worker)->get(route('admin.settings.index'));
        $response->assertStatus(403);
    }

    // =====================
    // Reports (Owner/Admin Only)
    // =====================

    public function test_owner_can_access_reports(): void
    {
        $owner = User::factory()->create();
        $owner->assignRole('owner');

        $response = $this->actingAs($owner)->get(route('admin.reports.index'));
        $response->assertStatus(200);
    }

    public function test_admin_can_access_reports(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $response = $this->actingAs($admin)->get(route('admin.reports.index'));
        $response->assertStatus(200);
    }

    public function test_worker_cannot_access_reports(): void
    {
        $worker = User::factory()->create();
        $worker->assignRole('worker');

        $response = $this->actingAs($worker)->get(route('admin.reports.index'));
        $response->assertStatus(403);
    }

    // =====================
    // Orders Management
    // =====================

    public function test_worker_can_view_orders(): void
    {
        $worker = User::factory()->create();
        $worker->assignRole('worker');

        $response = $this->actingAs($worker)->get(route('admin.orders.index'));
        $response->assertStatus(200);
    }

    // =====================
    // Client Routes
    // =====================

    public function test_client_can_access_order_history(): void
    {
        $client = User::factory()->create();
        $client->assignRole('client');

        $response = $this->actingAs($client)->get(route('orders.index'));
        $response->assertStatus(200);
    }

    public function test_client_can_access_profile(): void
    {
        $client = User::factory()->create();
        $client->assignRole('client');

        $response = $this->actingAs($client)->get(route('profile.edit'));
        $response->assertStatus(200);
    }

    public function test_guest_can_access_catalog(): void
    {
        $response = $this->get(route('catalog'));
        $response->assertStatus(200);
    }

    public function test_guest_can_access_cart(): void
    {
        $response = $this->get(route('cart.index'));
        $response->assertStatus(200);
    }
}
