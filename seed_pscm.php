<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\PscmSession;
use Illuminate\Support\Str;

$title = 'Famous Autobiographies - Indian Cricketers';

$hook_question = [
    'question' => 'Whose autobiography is "Stumped, Life behind and beyond Twenty Two Yards"?',
    'options' => ['Mahendra Singh Dhoni', 'Syed Kirmani', 'Nayan Mongia', 'Kiran More'],
    'correct_index' => 1,
    'explanation' => 'Stumped, Life behind and beyond Twenty Two Yards is the autobiography of former Indian cricketer and wicket-keeper Syed Kirmani. He was a key member of the 1983 World Cup winning team.'
];

$lesson_content = "### Famous Autobiographies of Indian Cricketers\n\nAutobiographies of famous sports personalities are a recurring topic in Kerala PSC exams. Let's look at some of the most important ones, particularly those of Indian cricketers.\n\n**Syed Kirmani**\n*   **Autobiography:** *Stumped: Life behind and beyond Twenty Two Yards*\n*   **Key Fact:** He was India's premier wicket-keeper in the 1970s and 1980s, and a key member of the 1983 World Cup-winning team.\n\n**Other Important Cricketing Autobiographies to Remember:**\n\n*   **Sachin Tendulkar:** *Playing It My Way*\n*   **Sunil Gavaskar:** *Sunny Days*\n*   **Kapil Dev:** *Straight from the Heart* & *By God's Decree*\n*   **Sanjay Manjrekar:** *Imperfect*\n*   **Yuvraj Singh:** *The Test of My Life*\n*   **Suresh Raina:** *Believe: What Life and Cricket Taught Me*\n*   **V. V. S. Laxman:** *281 and Beyond*\n*   **Sourav Ganguly:** *A Century is Not Enough*\n\n**Pro Tip for PSC:** Always associate the title of the book with a defining characteristic or famous achievement of the player (e.g., Laxman's famous 281 against Australia).";

$practice_mcqs = [
    [
        'question' => '"Playing It My Way" is the autobiography of which Indian cricketer?',
        'options' => ['Virat Kohli', 'Sachin Tendulkar', 'Rahul Dravid', 'V. V. S. Laxman'],
        'correct_index' => 1,
        'explanation' => 'Playing It My Way is the autobiography of Sachin Tendulkar, co-authored by Boria Majumdar.'
    ],
    [
        'question' => 'Who authored the book "Sunny Days"?',
        'options' => ['Sunil Gavaskar', 'Ravi Shastri', 'Kapil Dev', 'Mohinder Amarnath'],
        'correct_index' => 0,
        'explanation' => 'Sunny Days is the autobiography of legendary Indian batsman Sunil Gavaskar.'
    ],
    [
        'question' => 'The book "The Test of My Life" chronicles the struggles and triumphs of which cricketer?',
        'options' => ['Anil Kumble', 'Yuvraj Singh', 'Harbhajan Singh', 'Zaheer Khan'],
        'correct_index' => 1,
        'explanation' => 'The Test of My Life is Yuvraj Singh\'s autobiography, detailing his cricketing career and his battle with cancer.'
    ],
    [
        'question' => 'Which former Indian captain wrote the autobiography "A Century is Not Enough"?',
        'options' => ['Mahendra Singh Dhoni', 'Mohammad Azharuddin', 'Sourav Ganguly', 'Rahul Dravid'],
        'correct_index' => 2,
        'explanation' => 'A Century is Not Enough is the autobiography of Sourav Ganguly, co-authored with Gautam Bhattacharya.'
    ]
];

$media = [
    'image_url' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/d/d4/Syed_Kirmani.jpg/330px-Syed_Kirmani.jpg',
    'image_caption' => 'Syed Kirmani - 1983 World Cup Winning Wicket Keeper'
];

$session = PscmSession::create([
    'title' => $title,
    'slug' => Str::slug($title) . '-' . uniqid(),
    'subject' => 'General Knowledge',
    'topic_id' => 'sports-autobiographies',
    'hook_question' => $hook_question,
    'lesson_content' => $lesson_content,
    'practice_mcqs' => $practice_mcqs,
    'media' => $media,
    'omr_config' => ['time_limit_seconds' => 300, 'negative_marking' => 0.33, 'total_marks' => 5],
    'status' => 'published'
]);

echo "Session created successfully!\n";
echo "URL Slug: " . $session->slug . "\n";
