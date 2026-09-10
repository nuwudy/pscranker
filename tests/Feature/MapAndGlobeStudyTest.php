<?php

use App\Models\Category;
use App\Models\Session;
use App\Models\SessionContent;
use App\Models\User;

test('map study lab loads successfully with default pacific reality case', function () {
    $response = $this->get(route('map.study'));

    $response->assertStatus(200);
    $response->assertSee('PSC 3D Globe &amp; Map Study Lab', false);
    $response->assertSee('The Pacific Reality: USA &amp; Asia Neighbors', false);
    $response->assertSee('Bering Strait (82 km)');
    $response->assertSee('Pearl Harbor (Hawaii)');
});

test('map study lab switches between signature cases correctly', function () {
    // 1. German Blitzkrieg
    $resGerman = $this->get(route('map.study', ['case' => 'german_invasion']));
    $resGerman->assertStatus(200);
    $resGerman->assertSee('German Blitzkrieg &amp; WWII Invasion Routes', false);
    $resGerman->assertSee('Berlin (Nazi Germany)');
    $resGerman->assertSee('Poland (Warsaw)');

    // 2. Red Sea & Choke points
    $resRedSea = $this->get(route('map.study', ['case' => 'red_sea']));
    $resRedSea->assertStatus(200);
    $resRedSea->assertSee('Suez Canal');
    $resRedSea->assertSee('Bab-el-Mandeb');

    // 3. Nelson Mandela
    $resMandela = $this->get(route('map.study', ['case' => 'mandela']));
    $resMandela->assertStatus(200);
    $resMandela->assertSee('Mvezo');
    $resMandela->assertSee('Robben Island');

    // 4. Kerala Rivers
    $resKerala = $this->get(route('map.study', ['case' => 'kerala_rivers']));
    $resKerala->assertStatus(200);
    $resKerala->assertSee('Periyar (244 km)');
    $resKerala->assertSee('Palakkad Gap');
});

test('admin can create and save a session with map_globe content block', function () {
    $admin = User::factory()->create();
    $category = Category::create(['name' => 'World History', 'slug' => 'world-history', 'order' => 1]);

    $contents = [
        [
            'type' => 'map_globe',
            'order' => 1,
            'content_data' => [
                'mode' => '3d_globe',
                'preset' => 'german_invasion',
                'title' => 'WWII German Blitzkrieg Invasions',
                'title_malayalam' => 'ജർമ്മൻ അധിനിവേശ പാതകൾ',
                'center_lat' => 52.0,
                'center_lng' => 15.0,
                'zoom' => 2.2,
                'description' => 'Spatial trajectory of Blitzkrieg from Berlin into Poland and France.',
                'markers' => [
                    ['label' => 'Berlin', 'lat' => 52.52, 'lng' => 13.41, 'note' => 'Command center']
                ]
            ]
        ]
    ];

    $response = $this->actingAs($admin)->post(route('admin.sessions.store'), [
        'title' => 'WWII German Invasions Spatial Unit',
        'title_malayalam' => 'രണ്ടാം ലോകമഹായുദ്ധം മാപ്പ് പഠനം',
        'slug' => 'wwii-german-invasions-spatial',
        'category_id' => $category->id,
        'order' => 1,
        'xp_reward' => 250,
        'is_active' => true,
        'contents_json' => json_encode($contents),
    ]);

    $response->assertSessionHasNoErrors();

    $session = Session::where('slug', 'wwii-german-invasions-spatial')->first();
    expect($session)->not->toBeNull();

    $contentBlock = $session->contents()->first();
    expect($contentBlock->type)->toBe('map_globe');
    expect($contentBlock->content_data['preset'])->toBe('german_invasion');
    expect($contentBlock->content_data['title'])->toBe('WWII German Blitzkrieg Invasions');
    expect($contentBlock->content_data['markers'][0]['label'])->toBe('Berlin');
});

test('session runner renders map_globe block in phase 2', function () {
    $category = Category::firstOrCreate(['slug' => 'geography-map'], ['name' => 'Geography Map', 'order' => 1]);

    $session = Session::create([
        'title' => 'Red Sea Chokepoint Unit',
        'slug' => 'red-sea-chokepoint-unit',
        'category_id' => $category->id,
        'order' => 1,
        'xp_reward' => 200,
        'is_active' => true,
    ]);

    SessionContent::create([
        'session_id' => $session->id,
        'type' => 'map_globe',
        'order' => 1,
        'content_data' => [
            'mode' => '3d_globe',
            'preset' => 'red_sea',
            'title' => 'Red Sea and Bab-el-Mandeb Strait',
            'title_malayalam' => 'ചെങ്കടലും ബാബ് അൽ മന്ദബും',
            'center_lat' => 20.0,
            'center_lng' => 41.0,
            'zoom' => 2.0,
            'description' => 'Maritime choke points connecting Mediterranean and Arabian Sea.',
            'markers' => [
                ['label' => 'Suez Canal', 'lat' => 30.7, 'lng' => 32.34, 'note' => 'Opened 1869']
            ]
        ]
    ]);

    $response = $this->get(route('session.show', ['slug' => $session->slug]));

    $response->assertStatus(200);
    $response->assertSee('Red Sea and Bab-el-Mandeb Strait');
    $response->assertSee('map_globe');
    $response->assertSee('session-globe-canvas-');
});
