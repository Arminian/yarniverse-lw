<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Starting UserSeeder...');

        // Use env vars for credentials
        $email = env('FILAMENT_ADMIN_EMAIL', 'admin@example.com');
        $name = env('FILAMENT_ADMIN_NAME', 'Admin');
        $password = env('FILAMENT_ADMIN_PASSWORD', null);

        // Create a random password and output it to logs
        if (empty($password)) {
            $password = Str::random(20);
            $this->command->info("FILAMENT admin created for {$email} with random password: {$password}");
            \Log::info("FILAMENT admin created for {$email} with random password: {$password}");
        }

        // Create or update admin user - only use fields that exist in your User model
        $user = User::updateOrCreate(
            ['email' => $email],
            [
                'name' => $name,
                'email' => $email,
                'password' => Hash::make($password),
                'email_verified_at' => now(),
            ]
        );

        // Check if Spatie Permission is properly configured
        if (!class_exists(Role::class) || !class_exists(Permission::class)) {
            $this->command->error('Spatie Permission package not properly installed.');
            \Log::error('Spatie Permission package not properly installed.');
            return;
        }

        // Check if Filament Shield is configured
        if (!config('filament-shield')) {
            $this->command->warn('Filament Shield configuration not found.');
            \Log::warning('Filament Shield configuration not found.');
        }

        // Create a super-admin role
        $superRoleName = config('filament-shield.super_admin.name', 'super_admin');
        
        try {
            $role = Role::firstOrCreate(
                ['name' => $superRoleName],
                ['guard_name' => 'web']
            );

            // Get all permissions
            $permissions = Permission::where('guard_name', 'web')->get();
            
            if ($permissions->isEmpty()) {
                $this->command->warn('No permissions found. Run shield:generate first.');
                
                // Create at least one basic permission
                Permission::firstOrCreate(
                    ['name' => 'access_filament', 'guard_name' => 'web']
                );
                
                $permissions = Permission::where('guard_name', 'web')->get();
            }

            // Sync permissions to role
            $role->syncPermissions($permissions);
            
            // Assign role to user
            if (!$user->hasRole($superRoleName)) {
                $user->assignRole($role);
            }

            // Create a regular user for testing
            User::updateOrCreate(
                ['email' => 'john@proton.me'],
                [
                    'name' => 'John Doe',
                    'email' => 'john@proton.me',
                    'password' => Hash::make('password'),
                    'email_verified_at' => now(),
                ]
            );

            $this->command->info('Users created successfully!');

            // Clear caches
            try {
                \Artisan::call('permission:cache-reset');
                $this->command->info('Permission cache reset successfully.');
            } catch (\Throwable $e) {
                $this->command->warn('Could not reset permission cache: ' . $e->getMessage());
            }

        } catch (\Exception $e) {
            $this->command->error('Error creating roles/permissions: ' . $e->getMessage());
            \Log::error('Error in UserSeeder: ' . $e->getMessage());
        }
    }
}