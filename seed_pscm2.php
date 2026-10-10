<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\PscmSession;
use Illuminate\Support\Str;

$title = 'Water Conservation Techniques in Kerala';

$hook_question = [
    'question' => 'Among the techniques that is commonly used for water conservation in Kerala\'s watershed management programs, which one in the following is not preferred due to peculiar geographical conditions of Kerala?',
    'options' => ['Check dams', 'Borewell Recharge Shafts', 'Farm Ponds', 'Percolation pits'],
    'correct_index' => 1,
    'explanation' => 'Borewell recharge shafts are generally not preferred in many parts of Kerala due to the hard rock laterite terrain and high water table, which makes deep borewell recharging less effective compared to surface level percolation.'
];

$lesson_content = "### Watershed Management & Conservation in Kerala\n\nKerala's unique topography (sloping terrain from the Western Ghats to the Arabian Sea) requires specific water conservation techniques.\n\n**Preferred Techniques:**\n*   **Check Dams:** Very effective in slowing down water flow in streams and small rivers, allowing groundwater recharge.\n*   **Percolation Pits (Mazhakkuzhi):** Widely promoted in Kerala to catch rainwater runoff and allow it to seep into the soil.\n*   **Farm Ponds (Kulam):** Traditional method of storing rainwater for agricultural and domestic use.\n\n**Why Borewell Recharge Shafts are Not Preferred:**\n*   **Geographical Structure:** Kerala predominantly has a hard rock (laterite and crystalline) geology.\n*   **High Water Table:** In many areas, especially the midlands and coastal plains, the water table is already high during monsoons, rendering deep shafts redundant.\n*   **Contamination Risk:** Direct injection into deep aquifers bypasses the natural soil filtration process, increasing the risk of groundwater contamination.\n\n**Key Takeaway for PSC:** Always relate conservation methods to Kerala's specific lateritic soil and sloping terrain.";

$practice_mcqs = [
    [
        'question' => 'Which of the following is a traditional rainwater harvesting structure widely seen in Kerala?',
        'options' => ['Surangam', 'Khadin', 'Johad', 'Tankas'],
        'correct_index' => 0,
        'explanation' => 'Surangam is a traditional water harvesting system found in the Kasaragod district of Kerala.'
    ],
    [
        'question' => 'What is the primary purpose of constructing a check dam?',
        'options' => ['Generate hydroelectricity', 'Prevent soil erosion and recharge groundwater', 'Navigate ships', 'Divert water to other states'],
        'correct_index' => 1,
        'explanation' => 'Check dams slow down the velocity of water, prevent soil erosion, and help in groundwater recharge.'
    ],
    [
        'question' => 'Which type of soil is predominantly found in the midlands of Kerala, affecting groundwater recharge?',
        'options' => ['Black soil', 'Alluvial soil', 'Laterite soil', 'Desert soil'],
        'correct_index' => 2,
        'explanation' => 'Laterite soil is highly prevalent in the midland regions of Kerala.'
    ],
    [
        'question' => 'What is the Malayalam term commonly used for percolation pits dug for rainwater harvesting?',
        'options' => ['Thodu', 'Kulam', 'Mazhakkuzhi', 'Chira'],
        'correct_index' => 2,
        'explanation' => 'Mazhakkuzhi translates directly to rain-pits, widely promoted in Kerala for rainwater harvesting.'
    ]
];

$media = [
    'image_url' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/6/69/Check_dam_in_Kerala.jpg/320px-Check_dam_in_Kerala.jpg', // Placeholder
    'image_caption' => 'A typical check dam in Kerala slowing down stream flow.'
];

$session = PscmSession::create([
    'title' => $title,
    'slug' => Str::slug($title) . '-' . uniqid(),
    'subject' => 'Geography',
    'topic_id' => 'kerala-geography-water',
    'hook_question' => $hook_question,
    'lesson_content' => $lesson_content,
    'practice_mcqs' => $practice_mcqs,
    'media' => $media,
    'omr_config' => ['time_limit_seconds' => 300, 'negative_marking' => 0.33, 'total_marks' => 5],
    'status' => 'published'
]);

echo "Session created successfully!\n";
echo "URL Slug: " . $session->slug . "\n";
