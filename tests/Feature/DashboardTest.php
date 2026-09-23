<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_requires_login(): void
    {
        $this->get('/dashboard')->assertRedirect('/login');
        $this->post('/dashboard/facts', ['title' => 'Private'])->assertRedirect('/login');
    }

    public function test_admin_can_manage_portfolio_content(): void
    {
        $this->actingAs(User::factory()->create());

        $this->post('/dashboard/projects', [
            'title' => 'Example project',
            'link' => 'https://example.com',
            'categories' => 'Design, Development',
        ])->assertSessionHasNoErrors()->assertRedirect();

        $project = DB::table('projects')->where('title', 'Example project')->first();
        $this->assertNotNull($project);
        $this->assertSame(['Design', 'Development'], json_decode($project->categories, true));

        $this->get('/dashboard/projects')->assertOk();
        $this->put('/dashboard/projects/'.$project->id, [
            'title' => 'Updated project',
            'categories' => 'Laravel',
        ])->assertSessionHasNoErrors()->assertRedirect();
        $this->assertDatabaseHas('projects', ['id' => $project->id, 'title' => 'Updated project']);

        $this->delete('/dashboard/projects/'.$project->id)->assertRedirect();
        $this->assertDatabaseMissing('projects', ['id' => $project->id]);
    }

    public function test_messages_can_be_viewed_and_deleted_but_not_created_in_dashboard(): void
    {
        $this->actingAs(User::factory()->create());
        $id = DB::table('contacts')->insertGetId([
            'name' => 'Visitor', 'email' => 'visitor@example.com', 'text' => 'Hello',
            'created_at' => now(), 'updated_at' => now(),
        ]);

        $this->get('/dashboard/contacts')->assertOk();
        $this->post('/dashboard/contacts', ['name' => 'Other'])->assertNotFound();
        $this->delete('/dashboard/contacts/'.$id)->assertRedirect();
        $this->assertDatabaseMissing('contacts', ['id' => $id]);
    }

    public function test_fact_html_is_preserved_and_unsafe_markup_is_removed(): void
    {
        $this->actingAs(User::factory()->create());

        $this->post('/dashboard/facts', [
            'title' => 'Formatted fact',
            'text' => '<p onclick="alert(1)">First</p><script>alert(2)</script><p><strong>Second</strong></p>',
        ])->assertSessionHasNoErrors()->assertRedirect();

        $fact = DB::table('facts')->where('title', 'Formatted fact')->first();
        $this->assertSame('<p>First</p><p><strong>Second</strong></p>', $fact->text);

        $response = $this->get('/');
        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->where('facts.0.text', '<p>First</p><p><strong>Second</strong></p>')
        );
    }

    public function test_skill_can_be_saved_without_a_title(): void
    {
        $this->actingAs(User::factory()->create());

        $this->post('/dashboard/skills', [
            'title' => '',
            'image' => 'assets/skills/php.svg',
        ])->assertSessionHasNoErrors()->assertRedirect();

        $skill = DB::table('skills')->where('image', 'assets/skills/php.svg')->first();
        $this->assertNotNull($skill);
        $this->assertNull($skill->title);

        $this->put('/dashboard/skills/'.$skill->id, [
            'title' => '',
            'image' => 'assets/skills/laravel.svg',
        ])->assertSessionHasNoErrors()->assertRedirect();

        $this->assertDatabaseHas('skills', [
            'id' => $skill->id,
            'title' => null,
            'image' => 'assets/skills/laravel.svg',
        ]);
    }

    public function test_uploaded_skill_and_project_images_use_portable_paths(): void
    {
        Storage::fake('public');
        config(['portfolio.upload_disk' => 'public']);
        $this->actingAs(User::factory()->create());

        $this->post('/dashboard/skills', [
            'title' => '',
            'image' => '',
            'image_upload' => UploadedFile::fake()->image('skill.png'),
        ])->assertSessionHasNoErrors()->assertRedirect();

        $skill = DB::table('skills')->first();
        $this->assertStringStartsWith('/storage/skills/', $skill->image);
        Storage::disk('public')->assertExists(substr($skill->image, strlen('/storage/')));

        $this->post('/dashboard/projects', [
            'title' => 'Uploaded project',
            'desk_img' => '',
            'mobile_img' => '',
            'desk_upload' => UploadedFile::fake()->image('desktop.png'),
            'mobile_upload' => UploadedFile::fake()->image('mobile.png'),
        ])->assertSessionHasNoErrors()->assertRedirect();

        $project = DB::table('projects')->where('title', 'Uploaded project')->first();
        $this->assertStringStartsWith('/storage/projects/', $project->desk_img);
        $this->assertStringStartsWith('/storage/projects/', $project->mobile_img);
        Storage::disk('public')->assertExists(substr($project->desk_img, strlen('/storage/')));
        Storage::disk('public')->assertExists(substr($project->mobile_img, strlen('/storage/')));
    }

    public function test_uploaded_images_use_the_configured_cloud_disk(): void
    {
        Storage::fake('s3');
        config(['portfolio.upload_disk' => 's3']);
        $this->actingAs(User::factory()->create());

        $this->post('/dashboard/skills', [
            'image_upload' => UploadedFile::fake()->image('cloud-skill.png'),
        ])->assertSessionHasNoErrors()->assertRedirect();

        $skill = DB::table('skills')->first();
        $files = Storage::disk('s3')->allFiles('skills');

        $this->assertCount(1, $files);
        $this->assertStringEndsWith('/'.$files[0], $skill->image);
        Storage::disk('s3')->assertExists($files[0]);
    }
}
