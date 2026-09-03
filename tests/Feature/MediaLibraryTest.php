<?php

use App\Models\MediaFile;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

test('unauthenticated users are redirected from media library', function () {
    $response = $this->get(route('admin.media.index'));
    $response->assertRedirect(route('login'));
});

test('authenticated user can view media library', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('admin.media.index'));
    $response->assertStatus(200);
    $response->assertSee('Media Library');
});

test('authenticated user can upload an image file to media library', function () {
    Storage::fake('public');
    $user = User::factory()->create();

    $file = UploadedFile::fake()->create('test-mnemonic.png', 500, 'image/png');

    $response = $this->actingAs($user)->post(route('admin.media.store'), [
        'file' => $file,
        'title' => 'Test Mnemonic Graphic',
    ]);

    $response->assertRedirect(route('admin.media.index'));

    $this->assertDatabaseHas('media_files', [
        'name' => 'Test Mnemonic Graphic',
        'file_type' => 'image',
    ]);

    $media = MediaFile::first();
    Storage::disk('public')->assertExists($media->file_path);
});

test('media api list returns json items for content builder picker', function () {
    $user = User::factory()->create();

    MediaFile::create([
        'name' => 'Audio Summary 1',
        'file_path' => 'media/audios/sample.mp3',
        'url' => '/storage/media/audios/sample.mp3',
        'file_type' => 'audio',
        'mime_type' => 'audio/mpeg',
        'file_size' => 10240,
        'uploaded_by' => $user->id,
    ]);

    $response = $this->actingAs($user)->getJson(route('admin.media.api-list', ['type' => 'audio']));
    $response->assertStatus(200);
    $response->assertJson([
        'success' => true,
        'files' => [
            [
                'name' => 'Audio Summary 1',
                'file_type' => 'audio',
            ]
        ]
    ]);
});

test('media file can be deleted', function () {
    Storage::fake('public');
    $user = User::factory()->create();

    $path = 'media/images/delete-me.jpg';
    Storage::disk('public')->put($path, 'dummy content');

    $media = MediaFile::create([
        'name' => 'To Delete',
        'file_path' => $path,
        'url' => '/storage/' . $path,
        'file_type' => 'image',
        'mime_type' => 'image/jpeg',
        'file_size' => 100,
        'uploaded_by' => $user->id,
    ]);

    $response = $this->actingAs($user)->delete(route('admin.media.destroy', $media));
    $response->assertRedirect(route('admin.media.index'));

    $this->assertDatabaseMissing('media_files', ['id' => $media->id]);
    Storage::disk('public')->assertMissing($path);
});
