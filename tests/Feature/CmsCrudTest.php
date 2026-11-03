<?php

use App\Models\Admin;
use App\Models\Amenity;
use App\Models\Faq;
use App\Models\FeaturedProject;
use App\Models\Post;
use App\Models\Property;
use App\Models\PropertyType;
use App\Models\Service;
use App\Models\TeamMember;
use App\Models\Testimonial;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    $this->seed(DatabaseSeeder::class);
    Storage::fake('public');
});

test('cms property type: create, update, and delete work properly', function () {
    $admin = Admin::where('email', 'admin@example.com')->first();

    // 1. CREATE
    $file = UploadedFile::fake()->image('prop_type.jpg', 200, 200);
    $createResponse = $this->actingAs($admin, 'admin')->post(route('admin.property-types.store'), [
        'title'      => 'Luxury Villa',
        'image'      => $file,
        'icon_class' => 'bi bi-house',
    ]);
    $createResponse->assertRedirect(route('admin.property-types.index'));
    $this->assertDatabaseHas('property_types', ['title' => 'Luxury Villa']);

    $type = PropertyType::where('title', 'Luxury Villa')->first();

    // 2. UPDATE
    $updateResponse = $this->actingAs($admin, 'admin')->put(route('admin.property-types.update', $type), [
        'title'      => 'Ultra Luxury Villa',
        'icon_class' => 'bi bi-building',
    ]);
    $updateResponse->assertRedirect(route('admin.property-types.index'));
    $this->assertDatabaseHas('property_types', ['title' => 'Ultra Luxury Villa']);

    // 3. DELETE
    $deleteResponse = $this->actingAs($admin, 'admin')->delete(route('admin.property-types.destroy', $type));
    $deleteResponse->assertRedirect(route('admin.property-types.index'));
    $this->assertDatabaseMissing('property_types', ['id' => $type->id]);
});

test('cms property: create, update, and delete work properly', function () {
    $admin = Admin::where('email', 'admin@example.com')->first();

    // 1. CREATE
    $file = UploadedFile::fake()->image('property.jpg', 600, 400);
    $createResponse = $this->actingAs($admin, 'admin')->post(route('admin.properties.store'), [
        'type'      => 'Duplex Penthouse',
        'image'     => $file,
        'is_active' => true,
    ]);
    $this->assertDatabaseHas('properties', ['type' => 'Duplex Penthouse']);

    $property = Property::where('type', 'Duplex Penthouse')->first();
    $createResponse->assertRedirect(route('admin.properties.edit', $property->id));

    // 2. UPDATE
    $updateResponse = $this->actingAs($admin, 'admin')->put(route('admin.properties.update', $property), [
        'type'      => 'Updated Duplex Penthouse',
        'is_active' => true,
    ]);
    $updateResponse->assertRedirect(route('admin.properties.edit', $property->id));
    $this->assertDatabaseHas('properties', ['type' => 'Updated Duplex Penthouse']);

    // 3. DELETE
    $deleteResponse = $this->actingAs($admin, 'admin')->delete(route('admin.properties.destroy', $property));
    $deleteResponse->assertRedirect(route('admin.properties.index'));
    $this->assertDatabaseMissing('properties', ['id' => $property->id]);
});

test('cms featured project: create, update, and delete work properly', function () {
    $admin = Admin::where('email', 'admin@example.com')->first();

    // 1. CREATE
    $file = UploadedFile::fake()->image('project.jpg', 600, 400);
    $createResponse = $this->actingAs($admin, 'admin')->post(route('admin.featured-project.store'), [
        'title'     => 'Aurelia Skyrise',
        'location'  => 'Gulshan 2, Dhaka',
        'price'     => '৳ 4.5 Crore',
        'beds'      => 4,
        'baths'     => 4,
        'area'      => 3200,
        'image'     => $file,
        'is_active' => true,
    ]);
    $createResponse->assertRedirect(route('admin.featured-project.index'));
    $this->assertDatabaseHas('featured_projects', ['title' => 'Aurelia Skyrise']);

    $project = FeaturedProject::where('title', 'Aurelia Skyrise')->first();

    // 2. UPDATE
    $updateResponse = $this->actingAs($admin, 'admin')->put(route('admin.featured-project.update', $project), [
        'title'     => 'Aurelia Skyrise Tower',
        'location'  => 'Gulshan 2, Dhaka',
        'price'     => '৳ 5.0 Crore',
        'beds'      => 4,
        'baths'     => 4,
        'area'      => 3400,
        'is_active' => true,
    ]);
    $updateResponse->assertRedirect(route('admin.featured-project.index'));
    $this->assertDatabaseHas('featured_projects', ['title' => 'Aurelia Skyrise Tower', 'price' => '৳ 5.0 Crore']);

    // 3. DELETE
    $deleteResponse = $this->actingAs($admin, 'admin')->delete(route('admin.featured-project.destroy', $project));
    $deleteResponse->assertRedirect(route('admin.featured-project.index'));
    $this->assertDatabaseMissing('featured_projects', ['id' => $project->id]);
});

test('cms amenity: create, update, and delete work properly', function () {
    $admin = Admin::where('email', 'admin@example.com')->first();

    // 1. CREATE
    $createResponse = $this->actingAs($admin, 'admin')->post(route('admin.amenities.store'), [
        'title'      => 'Infinity Rooftop Pool',
        'icon'       => 'fa-solid fa-water-ladder',
        'sort_order' => 1,
        'is_active'  => true,
    ]);
    $createResponse->assertRedirect(route('admin.amenities.index'));
    $this->assertDatabaseHas('amenities', ['title' => 'Infinity Rooftop Pool']);

    $amenity = Amenity::where('title', 'Infinity Rooftop Pool')->first();

    // 2. UPDATE
    $updateResponse = $this->actingAs($admin, 'admin')->put(route('admin.amenities.update', $amenity), [
        'title'      => 'Heated Infinity Pool',
        'icon'       => 'fa-solid fa-water-ladder',
        'sort_order' => 2,
        'is_active'  => true,
    ]);
    $updateResponse->assertRedirect(route('admin.amenities.index'));
    $this->assertDatabaseHas('amenities', ['title' => 'Heated Infinity Pool']);

    // 3. DELETE
    $deleteResponse = $this->actingAs($admin, 'admin')->delete(route('admin.amenities.destroy', $amenity));
    $deleteResponse->assertRedirect(route('admin.amenities.index'));
    $this->assertDatabaseMissing('amenities', ['id' => $amenity->id]);
});

test('cms service: create, update, and delete work properly', function () {
    $admin = Admin::where('email', 'admin@example.com')->first();

    // 1. CREATE
    $createResponse = $this->actingAs($admin, 'admin')->post(route('admin.services.store'), [
        'title'             => 'Interior Design Consultancy',
        'short_description' => 'Bespoke interior luxury design for penthouses and flats.',
        'description'       => 'Full detailed description for bespoke interior luxury design.',
        'icon'              => 'fa-solid fa-couch',
        'sort_order'        => 1,
        'is_active'         => true,
    ]);
    $createResponse->assertRedirect(route('admin.services.index'));
    $this->assertDatabaseHas('services', ['title' => 'Interior Design Consultancy']);

    $service = Service::where('title', 'Interior Design Consultancy')->first();

    // 2. UPDATE
    $updateResponse = $this->actingAs($admin, 'admin')->put(route('admin.services.update', $service), [
        'title'             => 'Premium Interior Architecture',
        'short_description' => 'Comprehensive interior design and 3D visualization.',
        'description'       => 'Full updated description for interior architecture.',
        'icon'              => 'fa-solid fa-couch',
        'sort_order'        => 1,
        'is_active'         => true,
    ]);
    $updateResponse->assertRedirect(route('admin.services.index'));
    $this->assertDatabaseHas('services', ['title' => 'Premium Interior Architecture']);

    // 3. DELETE
    $deleteResponse = $this->actingAs($admin, 'admin')->delete(route('admin.services.destroy', $service));
    $deleteResponse->assertRedirect(route('admin.services.index'));
    $this->assertDatabaseMissing('services', ['id' => $service->id]);
});

test('cms team member: create, update, and delete work properly', function () {
    $admin = Admin::where('email', 'admin@example.com')->first();

    // 1. CREATE
    $createResponse = $this->actingAs($admin, 'admin')->post(route('admin.team-members.store'), [
        'name'      => 'Farhana Rahman',
        'role'      => 'Senior Lead Architect',
        'email'     => 'farhana@example.com',
        'is_active' => true,
    ]);
    $createResponse->assertRedirect(route('admin.team-members.index'));
    $this->assertDatabaseHas('team_members', ['name' => 'Farhana Rahman', 'role' => 'Senior Lead Architect']);

    $member = TeamMember::where('name', 'Farhana Rahman')->first();

    // 2. UPDATE
    $updateResponse = $this->actingAs($admin, 'admin')->put(route('admin.team-members.update', $member), [
        'name'      => 'Farhana Rahman, AIA',
        'role'      => 'Principal Architect',
        'email'     => 'farhana.aia@example.com',
        'is_active' => true,
    ]);
    $updateResponse->assertRedirect(route('admin.team-members.index'));
    $this->assertDatabaseHas('team_members', ['name' => 'Farhana Rahman, AIA', 'role' => 'Principal Architect']);

    // 3. DELETE
    $deleteResponse = $this->actingAs($admin, 'admin')->delete(route('admin.team-members.destroy', $member));
    $deleteResponse->assertRedirect(route('admin.team-members.index'));
    $this->assertDatabaseMissing('team_members', ['id' => $member->id]);
});

test('cms testimonial: create, update, and delete work properly', function () {
    $admin = Admin::where('email', 'admin@example.com')->first();

    // 1. CREATE
    $createResponse = $this->actingAs($admin, 'admin')->post(route('admin.testimonials.store'), [
        'name'      => 'Brig. Gen. (Retd.) Mostafa',
        'role'      => 'Apartment Buyer',
        'quote'     => 'Flawless construction quality and transparent documentation.',
        'rating'    => 5,
        'is_active' => true,
    ]);
    $createResponse->assertRedirect(route('admin.testimonials.index'));
    $this->assertDatabaseHas('testimonials', ['name' => 'Brig. Gen. (Retd.) Mostafa']);

    $testimonial = Testimonial::where('name', 'Brig. Gen. (Retd.) Mostafa')->first();

    // 2. UPDATE
    $updateResponse = $this->actingAs($admin, 'admin')->put(route('admin.testimonials.update', $testimonial), [
        'name'      => 'Brig. Gen. (Retd.) Mostafa Kamal',
        'role'      => 'Skyline Tower Resident',
        'quote'     => 'Exceptional hand-over on time!',
        'rating'    => 5,
        'is_active' => true,
    ]);
    $updateResponse->assertRedirect(route('admin.testimonials.index'));
    $this->assertDatabaseHas('testimonials', ['name' => 'Brig. Gen. (Retd.) Mostafa Kamal']);

    // 3. DELETE
    $deleteResponse = $this->actingAs($admin, 'admin')->delete(route('admin.testimonials.destroy', $testimonial));
    $deleteResponse->assertRedirect(route('admin.testimonials.index'));
    $this->assertDatabaseMissing('testimonials', ['id' => $testimonial->id]);
});

test('cms blog post: create, update, and delete work properly', function () {
    $admin = Admin::where('email', 'admin@example.com')->first();

    // 1. CREATE
    $createResponse = $this->actingAs($admin, 'admin')->post(route('admin.posts.store'), [
        'title'        => 'Dhaka Real Estate Trends 2026',
        'author'       => 'Chief Market Analyst',
        'summary'      => 'Comprehensive overview of property valuation in Gulshan and Banani.',
        'content'      => '<p>Detailed article content with investment return analysis.</p>',
        'published_at' => now()->toDateString(),
        'is_active'    => true,
    ]);
    $createResponse->assertRedirect(route('admin.posts.index'));
    $this->assertDatabaseHas('posts', ['title' => 'Dhaka Real Estate Trends 2026']);

    $post = Post::where('title', 'Dhaka Real Estate Trends 2026')->first();

    // 2. UPDATE
    $updateResponse = $this->actingAs($admin, 'admin')->put(route('admin.posts.update', $post), [
        'title'        => 'Dhaka Real Estate Outlook 2026-2027',
        'author'       => 'Senior Research Lead',
        'summary'      => 'Updated market forecast.',
        'content'      => '<p>Updated article content.</p>',
        'is_active'    => true,
    ]);
    $updateResponse->assertRedirect(route('admin.posts.index'));
    $this->assertDatabaseHas('posts', ['title' => 'Dhaka Real Estate Outlook 2026-2027']);

    // 3. DELETE
    $deleteResponse = $this->actingAs($admin, 'admin')->delete(route('admin.posts.destroy', $post));
    $deleteResponse->assertRedirect(route('admin.posts.index'));
    $this->assertDatabaseMissing('posts', ['id' => $post->id]);
});

test('cms faq: create, update, and delete work properly', function () {
    $admin = Admin::where('email', 'admin@example.com')->first();

    // 1. CREATE
    $createResponse = $this->actingAs($admin, 'admin')->post(route('admin.faqs.store'), [
        'question'   => 'What is the standard payment installment structure?',
        'answer'     => 'We offer 24 to 60 equal monthly installments linked to construction milestones.',
        'sort_order' => 1,
        'is_active'  => true,
    ]);
    $createResponse->assertRedirect(route('admin.faqs.index'));
    $this->assertDatabaseHas('faqs', ['question' => 'What is the standard payment installment structure?']);

    $faq = Faq::where('question', 'What is the standard payment installment structure?')->first();

    // 2. UPDATE
    $updateResponse = $this->actingAs($admin, 'admin')->put(route('admin.faqs.update', $faq), [
        'question'   => 'What is the revised payment schedule structure?',
        'answer'     => 'Down payment 20%, followed by 36 equal monthly installments.',
        'sort_order' => 1,
        'is_active'  => true,
    ]);
    $updateResponse->assertRedirect(route('admin.faqs.index'));
    $this->assertDatabaseHas('faqs', ['question' => 'What is the revised payment schedule structure?']);

    // 3. DELETE
    $deleteResponse = $this->actingAs($admin, 'admin')->delete(route('admin.faqs.destroy', $faq));
    $deleteResponse->assertRedirect(route('admin.faqs.index'));
    $this->assertDatabaseMissing('faqs', ['id' => $faq->id]);
});

test('cms role: create, update, and delete work properly', function () {
    $admin = Admin::where('email', 'admin@example.com')->first();

    // 1. CREATE
    $createResponse = $this->actingAs($admin, 'admin')->post(route('admin.roles.store'), [
        'name'        => 'Site Supervisor',
        'permissions' => ['properties.view'],
    ]);
    $createResponse->assertRedirect(route('admin.roles.index'));
    $this->assertDatabaseHas('roles', ['name' => 'Site Supervisor', 'guard_name' => 'admin']);

    $role = Role::where(['name' => 'Site Supervisor', 'guard_name' => 'admin'])->first();

    // 2. UPDATE
    $updateResponse = $this->actingAs($admin, 'admin')->put(route('admin.roles.update', $role), [
        'name'        => 'Lead Site Supervisor',
        'permissions' => ['properties.view', 'projects.view'],
    ]);
    $updateResponse->assertRedirect(route('admin.roles.index'));
    $this->assertDatabaseHas('roles', ['name' => 'Lead Site Supervisor']);

    // 3. DELETE
    $deleteResponse = $this->actingAs($admin, 'admin')->delete(route('admin.roles.destroy', $role));
    $deleteResponse->assertRedirect(route('admin.roles.index'));
    $this->assertDatabaseMissing('roles', ['id' => $role->id]);
});

test('cms admin user: create, update, and delete work properly', function () {
    $superAdmin = Admin::where('email', 'admin@example.com')->first();

    // 1. CREATE
    $createResponse = $this->actingAs($superAdmin, 'admin')->post(route('admin.admins.store'), [
        'name'                  => 'Test Operator',
        'email'                 => 'operator@example.com',
        'password'              => 'Operator123!',
        'password_confirmation' => 'Operator123!',
        'roles'                 => ['Manager'],
    ]);
    $createResponse->assertRedirect(route('admin.admins.index'));
    $this->assertDatabaseHas('admins', ['email' => 'operator@example.com', 'name' => 'Test Operator']);

    $operator = Admin::where('email', 'operator@example.com')->first();

    // 2. UPDATE
    $updateResponse = $this->actingAs($superAdmin, 'admin')->put(route('admin.admins.update', $operator), [
        'name'                  => 'Senior Operator',
        'email'                 => 'operator@example.com',
        'password'              => null,
        'password_confirmation' => null,
        'roles'                 => ['Manager'],
    ]);
    $updateResponse->assertRedirect(route('admin.admins.index'));
    $this->assertDatabaseHas('admins', ['email' => 'operator@example.com', 'name' => 'Senior Operator']);

    // 3. DELETE
    $deleteResponse = $this->actingAs($superAdmin, 'admin')->delete(route('admin.admins.destroy', $operator));
    $deleteResponse->assertRedirect(route('admin.admins.index'));
    $this->assertDatabaseMissing('admins', ['id' => $operator->id]);
});
