<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;
use App\Models\Admin;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $guard = 'admin';

        // Define permissions
        $permissions = [
            // Dashboard
            'dashboard.view',

            // Homepage Settings
            'homepage-settings.view',
            'homepage-settings.edit',

            // General Settings
            'settings.view',
            'settings.edit',

            // Featured Projects
            'projects.view',
            'projects.create',
            'projects.edit',
            'projects.delete',

            // Team Members
            'team-members.view',
            'team-members.create',
            'team-members.edit',
            'team-members.delete',

            // Testimonials
            'testimonials.view',
            'testimonials.create',
            'testimonials.edit',
            'testimonials.delete',

            // Properties
            'properties.view',
            'properties.create',
            'properties.edit',
            'properties.delete',

            // Property Types
            'property-types.view',
            'property-types.create',
            'property-types.edit',
            'property-types.delete',

            // Submissions
            'submissions.view',
            'submissions.delete',

            // Newsletters & Subscribers
            'newsletters.view',
            'newsletters.delete',

            // Services
            'services.view',
            'services.create',
            'services.edit',
            'services.delete',

            // Amenities
            'amenities.view',
            'amenities.create',
            'amenities.edit',
            'amenities.delete',

            // About Settings
            'about-settings.view',
            'about-settings.edit',

            // Journeys
            'journeys.view',
            'journeys.create',
            'journeys.edit',
            'journeys.delete',

            // Contact Settings
            'contact-settings.view',
            'contact-settings.edit',

            // Media Gallery
            'galleries.view',
            'galleries.create',
            'galleries.edit',
            'galleries.delete',

            // Blog Posts
            'posts.view',
            'posts.create',
            'posts.edit',
            'posts.delete',

            // FAQs
            'faqs.view',
            'faqs.create',
            'faqs.edit',
            'faqs.delete',

            // Roles & Permissions Management
            'roles.view',
            'roles.create',
            'roles.edit',
            'roles.delete',

            // Admin User Management
            'admins.view',
            'admins.create',
            'admins.edit',
            'admins.delete',
        ];

        // Create permissions
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => $guard]);
        }

        // Create roles and assign permissions
        $superAdminRole = Role::firstOrCreate(['name' => 'Super Admin', 'guard_name' => $guard]);
        // Super Admin gets all permissions (via Gate::before in AppServiceProvider, but let's assign them too)
        $superAdminRole->syncPermissions(Permission::where('guard_name', $guard)->get());

        // Manager Role
        $managerRole = Role::firstOrCreate(['name' => 'Manager', 'guard_name' => $guard]);
        $managerRole->syncPermissions([
            'dashboard.view',
            'homepage-settings.view',
            'homepage-settings.edit',
            'settings.view',
            'projects.view', 'projects.create', 'projects.edit',
            'team-members.view', 'team-members.create', 'team-members.edit',
            'testimonials.view', 'testimonials.create', 'testimonials.edit', 'testimonials.delete',
            'properties.view', 'properties.create', 'properties.edit', 'properties.delete',
            'property-types.view', 'property-types.create', 'property-types.edit', 'property-types.delete',
            'submissions.view', 'submissions.delete',
            'newsletters.view', 'newsletters.delete',
            'services.view', 'services.create', 'services.edit',
            'amenities.view', 'amenities.create', 'amenities.edit',
            'about-settings.view', 'about-settings.edit',
            'journeys.view', 'journeys.create', 'journeys.edit',
            'contact-settings.view', 'contact-settings.edit',
            'galleries.view', 'galleries.create', 'galleries.edit', 'galleries.delete',
            'posts.view', 'posts.create', 'posts.edit', 'posts.delete',
            'faqs.view', 'faqs.create', 'faqs.edit', 'faqs.delete',
        ]);

        // Editor Role
        $editorRole = Role::firstOrCreate(['name' => 'Editor', 'guard_name' => $guard]);
        $editorRole->syncPermissions([
            'dashboard.view',
            'projects.view', 'projects.create', 'projects.edit',
            'properties.view', 'properties.create', 'properties.edit',
            'property-types.view', 'property-types.create', 'property-types.edit',
            'services.view', 'services.create', 'services.edit',
            'amenities.view', 'amenities.create', 'amenities.edit',
            'galleries.view', 'galleries.create', 'galleries.edit',
            'posts.view', 'posts.create', 'posts.edit',
            'faqs.view', 'faqs.create', 'faqs.edit',
        ]);

        // Viewer Role
        $viewerRole = Role::firstOrCreate(['name' => 'Viewer', 'guard_name' => $guard]);
        $viewerRole->syncPermissions([
            'dashboard.view',
            'homepage-settings.view',
            'settings.view',
            'projects.view',
            'team-members.view',
            'testimonials.view',
            'properties.view',
            'property-types.view',
            'submissions.view',
            'newsletters.view',
            'services.view',
            'amenities.view',
            'about-settings.view',
            'journeys.view',
            'contact-settings.view',
            'galleries.view',
            'posts.view',
            'faqs.view',
        ]);

        // Assign Super Admin role to default admin
        $admin = Admin::where('email', 'admin@example.com')->first();
        if ($admin) {
            $admin->assignRole($superAdminRole);
        }
    }
}
