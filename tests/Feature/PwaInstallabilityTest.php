<?php

test('manifest.json is accessible and valid PWA config', function () {
    $manifestPath = public_path('manifest.json');
    expect(file_exists($manifestPath))->toBeTrue();

    $content = file_get_contents($manifestPath);
    $data = json_decode($content, true);

    expect($data)->toBeArray();
    expect($data['display'])->toBe('standalone');
    expect($data['start_url'])->toBe('/?source=pwa');
    expect($data['icons'])->not->toBeEmpty();
});

test('service worker sw.js file exists and is accessible', function () {
    $swPath = public_path('sw.js');
    expect(file_exists($swPath))->toBeTrue();

    $content = file_get_contents($swPath);
    expect($content)->toContain('CACHE_NAME');
    expect($content)->toContain('addEventListener');
});

test('home and course pages contain pwa tags and install banner', function () {
    $response = $this->get(route('home'));
    $response->assertStatus(200);
    $response->assertSee('<link rel="manifest" href="/manifest.json">', false);
    $response->assertSee('pwaInstaller()');
    $response->assertSee('Install PSCRanker App');
});
