<?php

use App\Models\User;
use App\Models\PscmSession;
use App\Models\Session;

test('main layout includes katex css, defer scripts, and auto-render extension', function () {
    $response = $this->get(route('home'));
    $response->assertStatus(200);

    // KaTeX CDN links
    $response->assertSee('https://cdn.jsdelivr.net/npm/katex@0.16.9/dist/katex.min.css');
    $response->assertSee('https://cdn.jsdelivr.net/npm/katex@0.16.9/dist/katex.min.js');
    $response->assertSee('https://cdn.jsdelivr.net/npm/katex@0.16.9/dist/contrib/auto-render.min.js');
    $response->assertSee('renderMathInElement');
    $response->assertSee('Noto+Sans+Malayalam');
    $response->assertSee('Manjari');
    $response->assertSee('Gayathri');
});

test('admin layout includes katex css, defer scripts, and auto-render extension', function () {
    $admin = User::factory()->create(['is_admin' => true]);
    $response = $this->actingAs($admin)->get(route('admin.dashboard'));
    $response->assertStatus(200);

    $response->assertSee('https://cdn.jsdelivr.net/npm/katex@0.16.9/dist/katex.min.css');
    $response->assertSee('https://cdn.jsdelivr.net/npm/katex@0.16.9/dist/katex.min.js');
    $response->assertSee('https://cdn.jsdelivr.net/npm/katex@0.16.9/dist/contrib/auto-render.min.js');
    $response->assertSee('renderMathInElement');
});

test('pscm session runner includes katex math rendering and malayam font typography', function () {
    $session = PscmSession::create([
        'title' => 'Arithmetic Progression & Series Drill',
        'slug' => 'arithmetic-progression-series-drill',
        'category' => 'Maths',
        'status' => 'published',
        'access_level' => 'free',
        'hook_question' => [
            'question' => 'ഒരു സമാന്തര ശ്രേണിയുടെ ആദ്യ പദം $a$ ആയാൽ?',
            'options' => ['$a$', '$b$', '$c$', '$d$'],
            'correct_index' => 0,
        ],
        'practice_mcqs' => [
            [
                'question' => 'Formula for $n$-th term of AP?',
                'options' => ['$a + (n-1)d$', '$a + nd$', '$2a + nd$', '$a - nd$'],
                'correct_index' => 0,
            ]
        ],
        'lesson_content' => "### സമാന്തര ശ്രേണി\nആദ്യ പദം \$a\$ ഉം പൊതുവ്യത്യാസം \$d\$ ഉം ആയാൽ:\n\n\$\$x_n = a + (n - 1)d\$\$\n\nഇവിടെ \$x_n\$ എന്നത് \$\\text{n-ാം പദം}\$ ആണ്.",
    ]);

    $response = $this->get(route('learner.pscm.show', $session->slug));
    $response->assertStatus(200);

    // KaTeX Assets in <head>
    $response->assertSee('https://cdn.jsdelivr.net/npm/katex@0.16.9/dist/katex.min.css');
    $response->assertSee('https://cdn.jsdelivr.net/npm/katex@0.16.9/dist/katex.min.js');
    $response->assertSee('https://cdn.jsdelivr.net/npm/katex@0.16.9/dist/contrib/auto-render.min.js');
    $response->assertSee('renderMathInElement');

    // Lesson content and renderMarkdown call
    $response->assertSee('renderMarkdown(session.lesson_content)', false);
    $response->assertSee('triggerMathRender()', false);
});

test('css stylesheet defines malayalam font fallbacks for katex and text elements', function () {
    $cssContent = file_get_contents(resource_path('css/app.css'));

    expect($cssContent)->toContain('.katex');
    expect($cssContent)->toContain('KaTeX_Main');
    expect($cssContent)->toContain('Noto Sans Malayalam');
    expect($cssContent)->toContain('Manjari');
    expect($cssContent)->toContain('Gayathri');
    expect($cssContent)->toContain('.katex-display');
});
