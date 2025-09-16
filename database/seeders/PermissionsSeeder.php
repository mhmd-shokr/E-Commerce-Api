<?php

// database/seeders/PermissionSeeder.php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionsSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('permissions')->delete();

        $permissions = [
            // Users
            'manage users', 'view users', 'create user', 'edit user', 'delete user',

            // Products
            'view products', 'create product', 'edit product', 'delete product', 'manage stock',

            // Categories
            'view categories', 'create category', 'edit category', 'delete category',

            // Cart
            'view cart', 'add to cart', 'update cart', 'remove from cart', 'clear cart',

            // Order
            'view Order', 'create order', 'update order status', 'cancel order', 'refund order',

            // Reviews
            'view reviews', 'create review', 'edit review', 'delete review',

            // Coupon
            'view Coupon', 'create coupon', 'edit coupon', 'delete coupon',

            // Settings
            'manage roles', 'manage permissions', 'manage site settings',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Roles
        $admin = Role::firstOrCreate(['name' => 'Admin']);
        $seller = Role::firstOrCreate(['name' => 'Seller']);
        $customer = Role::firstOrCreate(['name' => 'Customer']);

        // Assign permissions
        $admin->givePermissionTo(Permission::all());

        $seller->givePermissionTo([
            'view products', 'create product', 'edit product', 'delete product', 'manage stock',
            'view categories', 'view Order',
        ]);

        $customer->givePermissionTo([
            'view products', 'view categories',
            'view cart', 'add to cart', 'update cart', 'remove from cart', 'clear cart',
            'create order', 'view Order',
            'create review', 'view reviews',
        ]);
    }
}