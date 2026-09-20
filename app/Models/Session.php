<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Session extends Model
{
    use HasFactory;

    protected $table = 'learning_sessions';

    protected $fillable = [
        'title',
        'title_malayalam',
        'feature_image',
        'feature_video',
        'slug',
        'category_id',
        'order',
        'xp_reward',
        'is_active',
        'access_level',
        'is_premium',
        'price',
        'in_general_stream',
        'general_stream_order',
        'creation_mode',
        'custom_html',
        'pass_mark',
        'time_limit_minutes',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'access_level' => 'string',
        'is_premium' => 'boolean',
        'price' => 'decimal:2',
        'order' => 'integer',
        'xp_reward' => 'integer',
        'pass_mark' => 'integer',
        'time_limit_minutes' => 'integer',
        'in_general_stream' => 'boolean',
        'general_stream_order' => 'integer',
        'creation_mode' => 'string',
    ];

    public function isCustomCode(): bool
    {
        return ($this->creation_mode ?? 'manual') === 'code';
    }

    public function isManual(): bool
    {
        return !$this->isCustomCode();
    }

    public function hasFeatureMedia(): bool
    {
        return !empty($this->feature_video) || !empty($this->feature_image);
    }

    public function isFeatureVideoEmbed(): bool
    {
        if (empty($this->feature_video)) {
            return false;
        }

        $url = strtolower($this->feature_video);
        return str_contains($url, 'youtube.com') 
            || str_contains($url, 'youtu.be') 
            || str_contains($url, 'vimeo.com');
    }

    public function getFeatureVideoEmbedUrl(): string
    {
        if (empty($this->feature_video)) {
            return '';
        }

        $url = trim($this->feature_video);

        // YouTube matches (watch?v=, youtu.be/, embed/, shorts/)
        if (preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?|shorts)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]+)/i', $url, $matches)) {
            return 'https://www.youtube.com/embed/' . $matches[1] . '?rel=0&modestbranding=1';
        }

        // Vimeo matches
        if (preg_match('/vimeo\.com\/(?:channels\/(?:\w+\/)?|groups\/([^\/]*)\/videos\/|album\/(\d+)\/video\/|video\/|)(\d+)/i', $url, $matches)) {
            $vimeoId = end($matches);
            return 'https://player.vimeo.com/video/' . $vimeoId;
        }

        return $url;
    }

    public function isGuest(): bool
    {
        return ($this->access_level ?? 'guest') === 'guest';
    }

    public function isRegisteredOnly(): bool
    {
        return ($this->access_level ?? 'guest') === 'registered';
    }

    public function isPremiumOnly(): bool
    {
        return ($this->access_level ?? 'guest') === 'premium' || $this->is_premium;
    }

    public function isFree(): bool
    {
        return !$this->isPremiumOnly();
    }

    public function getFormattedPriceAttribute(): string
    {
        if ($this->isFree() || !$this->price) {
            return 'FREE';
        }
        return '₹' . number_format($this->price, 0);
    }

    public function getPreviousSession(?string $stream = null): ?self
    {
        if ($stream === 'general' && $this->in_general_stream) {
            $prev = self::where('is_active', true)
                ->where('in_general_stream', true)
                ->where('general_stream_order', '<', $this->general_stream_order ?? 999999)
                ->orderBy('general_stream_order', 'desc')
                ->orderBy('id', 'desc')
                ->first();
            if ($prev) {
                return $prev;
            }
        }

        return self::where('is_active', true)
            ->where(function ($q) {
                if ($this->category_id) {
                    $q->where('category_id', $this->category_id);
                }
            })
            ->where('order', '<', $this->order)
            ->orderBy('order', 'desc')
            ->orderBy('id', 'desc')
            ->first() 
            ?? self::where('is_active', true)
                ->where('order', '<', $this->order)
                ->orderBy('order', 'desc')
                ->orderBy('id', 'desc')
                ->first();
    }

    public function getNextSession(?string $stream = null): ?self
    {
        if ($stream === 'general' && $this->in_general_stream) {
            $next = self::where('is_active', true)
                ->where('in_general_stream', true)
                ->where('general_stream_order', '>', $this->general_stream_order ?? 0)
                ->orderBy('general_stream_order', 'asc')
                ->orderBy('id', 'asc')
                ->first();
            if ($next) {
                return $next;
            }
        }

        return self::where('is_active', true)
            ->where(function ($q) {
                if ($this->category_id) {
                    $q->where('category_id', $this->category_id);
                }
            })
            ->where('order', '>', $this->order)
            ->orderBy('order', 'asc')
            ->orderBy('id', 'asc')
            ->first()
            ?? self::where('is_active', true)
                ->where('order', '>', $this->order)
                ->orderBy('order', 'asc')
                ->orderBy('id', 'asc')
                ->first();
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function contents(): HasMany
    {
        return $this->hasMany(SessionContent::class, 'session_id')->orderBy('order', 'asc');
    }

    public function questions(): HasMany
    {
        return $this->hasMany(Question::class, 'session_id');
    }

    public function diagnosticQuestion(): HasOne
    {
        return $this->hasOne(Question::class, 'session_id')->where('phase_type', 'diagnostic');
    }

    public function reinforcementQuestions(): HasMany
    {
        return $this->hasMany(Question::class, 'session_id')->where('phase_type', 'reinforcement');
    }

    public function omrQuestions(): HasMany
    {
        return $this->hasMany(Question::class, 'session_id')->where('phase_type', 'omr');
    }

    public function getEffectiveOmrQuestionsAttribute()
    {
        if ($this->omrQuestions->isNotEmpty()) {
            return $this->omrQuestions;
        }

        if ($this->reinforcementQuestions->isNotEmpty()) {
            return $this->reinforcementQuestions;
        }

        // Auto-extract from practice_mcq and hook_mcq blocks in session_contents
        $mcqBlocks = $this->contents()->whereIn('type', ['practice_mcq', 'hook_mcq'])->orderBy('unit_order', 'asc')->orderBy('order', 'asc')->get();
        if ($mcqBlocks->isNotEmpty()) {
            return $mcqBlocks->map(function ($block) {
                $data = $block->content_data ?? [];
                $q = new Question([
                    'session_id' => $this->id,
                    'phase_type' => 'omr',
                    'question_text' => $data['question_text'] ?? '',
                    'question_text_malayalam' => $data['question_text_malayalam'] ?? null,
                    'option_a' => $data['option_a'] ?? '',
                    'option_b' => $data['option_b'] ?? '',
                    'option_c' => $data['option_c'] ?? '',
                    'option_d' => $data['option_d'] ?? '',
                    'correct_option' => $data['correct_option'] ?? 'A',
                    'explanation' => $data['explanation'] ?? null,
                    'explanation_malayalam' => $data['explanation_malayalam'] ?? null,
                    'trap_warning' => $data['trap_warning'] ?? null,
                    'psc_exam_reference' => $data['psc_reference'] ?? null,
                ]);
                $q->id = $block->id;
                return $q;
            });
        }

        return collect();
    }

    public function getEffectiveReinforcementQuestionsAttribute()
    {
        return $this->reinforcementQuestions->isNotEmpty()
            ? $this->reinforcementQuestions
            : collect();
    }

    public function progress(): HasMany
    {
        return $this->hasMany(UserSessionProgress::class, 'session_id');
    }

    /**
     * Build the structured modular Unit hierarchy:
     * Track -> Session -> Units -> Blocks
     * Final Unit is always the Capstone OMR Exam Sheet.
     */
    public function getStructuredUnitsAttribute(): array
    {
        $units = [];
        $rawContents = $this->contents()->orderBy('unit_order', 'asc')->orderBy('order', 'asc')->get();
        $diagnosticQ = $this->diagnosticQuestion;
        $reinforcementQs = $this->effective_reinforcement_questions;
        $omrQs = $this->effective_omr_questions;

        // Group content blocks by unit_order
        $grouped = $rawContents->groupBy(fn($item) => $item->unit_order ?: 1);

        if ($grouped->isEmpty()) {
            // Default Unit 1 if no content blocks exist yet
            $grouped = collect([1 => collect()]);
        }

        $unitCounter = 1;
        foreach ($grouped as $unitOrder => $items) {
            $firstItem = $items->first();
            $unitTitle = ($firstItem && !empty($firstItem->unit_title)) 
                ? $firstItem->unit_title 
                : "Unit {$unitCounter}: Concept & Study Notes";

            $blocks = [];

            // If Unit 1 and we have a Hook MCQ (diagnostic question), insert Hook MCQ block at the top of Unit 1 ONLY IF NOT ALREADY IN BLOCKS
            if ($unitCounter === 1 && $diagnosticQ && !$items->contains(fn($b) => $b->type === 'hook_mcq')) {
                $blocks[] = [
                    'id' => 'hook_mcq_' . $diagnosticQ->id,
                    'type' => 'hook_mcq',
                    'title' => 'Hook Question (Concept Challenge)',
                    'title_malayalam' => 'ഹുക്ക് ചോദ്യം (പ്രിലിമിനറി വെല്ലുവിളി)',
                    'content_data' => [
                        'question_id' => $diagnosticQ->id,
                        'question_text' => $diagnosticQ->question_text,
                        'question_text_malayalam' => $diagnosticQ->question_text_malayalam,
                        'option_a' => $diagnosticQ->option_a,
                        'option_b' => $diagnosticQ->option_b,
                        'option_c' => $diagnosticQ->option_c,
                        'option_d' => $diagnosticQ->option_d,
                        'options' => $diagnosticQ->resolved_options,
                        'correct_option' => $diagnosticQ->correct_option,
                        'explanation' => $diagnosticQ->explanation,
                        'explanation_malayalam' => $diagnosticQ->explanation_malayalam,
                        'trap_warning' => $diagnosticQ->resolved_trap_warning,
                        'psc_reference' => $diagnosticQ->psc_exam_reference,
                    ],
                ];
            }

            foreach ($items as $item) {
                $blocks[] = [
                    'id' => 'block_' . $item->id,
                    'type' => $item->type,
                    'content_data' => $item->content_data,
                    'order' => $item->order,
                ];
            }

            $units[] = [
                'unit_number' => $unitCounter,
                'title' => $unitTitle,
                'is_omr_unit' => false,
                'blocks' => $blocks,
            ];
            $unitCounter++;
        }

        // Add Practice MCQ units if there are reinforcement questions NOT already added inside unit content blocks
        $existingPracticeQTexts = $rawContents->where('type', 'practice_mcq')
            ->map(fn($item) => trim($item->content_data['question_text'] ?? ''))
            ->filter()
            ->all();

        $standalonePracticeQs = $reinforcementQs->filter(function ($q) use ($existingPracticeQTexts) {
            return !in_array(trim($q->question_text), $existingPracticeQTexts);
        });

        if ($standalonePracticeQs->isNotEmpty()) {
            foreach ($standalonePracticeQs->values() as $idx => $q) {
                $units[] = [
                    'unit_number' => $unitCounter,
                    'title' => "Unit {$unitCounter}: Practice Drill #" . ($idx + 1),
                    'is_omr_unit' => false,
                    'blocks' => [
                        [
                            'id' => 'practice_mcq_' . $q->id,
                            'type' => 'practice_mcq',
                            'title' => 'Speed Practice MCQ (1 Question Per Screen)',
                            'title_malayalam' => 'റാപ്പിഡ് പ്രാക്ടീസ് ചോദ്യം',
                            'content_data' => [
                                'question_id' => $q->id,
                                'question_text' => $q->question_text,
                                'question_text_malayalam' => $q->question_text_malayalam,
                                'option_a' => $q->option_a,
                                'option_b' => $q->option_b,
                                'option_c' => $q->option_c,
                                'option_d' => $q->option_d,
                                'options' => $q->resolved_options,
                                'correct_option' => $q->correct_option,
                                'explanation' => $q->explanation,
                                'explanation_malayalam' => $q->explanation_malayalam,
                                'trap_warning' => $q->resolved_trap_warning,
                                'psc_reference' => $q->psc_exam_reference,
                            ],
                        ]
                    ],
                ];
                $unitCounter++;
            }
        }

        // Final Unit: CAPSTONE OMR ASSESSMENT (Grouped single-page layout displaying all questions together)
        $units[] = [
            'unit_number' => $unitCounter,
            'title' => "Unit {$unitCounter}: Capstone OMR Sheet Exam (Final)",
            'title_malayalam' => 'ഫൈനൽ ഒ.എം.ആർ പരീക്ഷാ ഷീറ്റ്',
            'is_omr_unit' => true,
            'questions' => $omrQs->map(function ($q) {
                return [
                    'id' => $q->id,
                    'question_text' => $q->question_text,
                    'question_text_malayalam' => $q->question_text_malayalam,
                    'option_a' => $q->option_a,
                    'option_b' => $q->option_b,
                    'option_c' => $q->option_c,
                    'option_d' => $q->option_d,
                    'options' => $q->resolved_options,
                    'correct_option' => strtoupper(trim($q->correct_option)),
                    'explanation' => $q->explanation,
                    'explanation_malayalam' => $q->explanation_malayalam,
                    'trap_warning' => $q->resolved_trap_warning,
                    'psc_reference' => $q->psc_exam_reference,
                ];
            })->values()->all(),
            'blocks' => [],
        ];

        return $units;
    }

    /**
     * Compute Cumulative Score Ledger for the current learning track.
     * e.g., Session 1: 4/5 + Session 2: 5/5 = 9/10
     */
    public function getCumulativeLedger(?int $userId = null, ?string $guestToken = null, string $stream = 'subject'): array
    {
        $query = self::where('is_active', true);
        if ($stream === 'general' && $this->in_general_stream) {
            $query->where('in_general_stream', true)->orderBy('general_stream_order', 'asc');
            $trackName = 'General Stream (Mixed Master Track)';
        } else {
            if ($this->category_id) {
                $query->where('category_id', $this->category_id);
                $trackName = ($this->category ? $this->category->name : 'Subject') . ' Learning Track';
            } else {
                $trackName = 'General Learning Track';
            }
            $query->orderBy('order', 'asc');
        }

        $trackSessions = $query->orderBy('id', 'asc')->get();

        $sessionEntries = [];
        $runningScore = 0.0;
        $runningMax = 0.0;
        $completedCount = 0;

        foreach ($trackSessions as $idx => $s) {
            $progressQuery = UserSessionProgress::where('session_id', $s->id);
            if ($userId) {
                $progressQuery->where('user_id', $userId);
            } elseif ($guestToken) {
                $progressQuery->where('guest_token', $guestToken);
            } else {
                $progressQuery->whereRaw('1 = 0');
            }
            $prog = $progressQuery->first();

            $totalQuestions = $s->effective_omr_questions->count() ?: 5;
            $maxPossibleMarks = $totalQuestions * 1.00;
            $isCompleted = $prog && ($prog->completed_at !== null || $prog->current_phase === 'summary');
            $netMarks = ($isCompleted && $prog) ? (float) ($prog->net_marks ?? 0.0) : null;

            if ($isCompleted && $netMarks !== null) {
                $runningScore += max(0, $netMarks);
                $runningMax += $maxPossibleMarks;
                $completedCount++;
            }

            $sessionEntries[] = [
                'session_id' => $s->id,
                'session_title' => $s->title,
                'session_title_malayalam' => $s->title_malayalam,
                'slug' => $s->slug,
                'order' => $stream === 'general' ? ($s->general_stream_order ?? ($idx + 1)) : ($s->order ?? ($idx + 1)),
                'is_current' => ($s->id === $this->id),
                'is_completed' => $isCompleted,
                'net_marks' => $netMarks,
                'max_marks' => $maxPossibleMarks,
                'percentage' => ($netMarks !== null && $maxPossibleMarks > 0) ? round(($netMarks / $maxPossibleMarks) * 100, 1) : null,
            ];
        }

        return [
            'track_name' => $trackName,
            'category_id' => $this->category_id,
            'current_session_id' => $this->id,
            'total_sessions' => $trackSessions->count(),
            'completed_sessions' => $completedCount,
            'cumulative_score' => round($runningScore, 2),
            'cumulative_max' => round($runningMax, 2),
            'cumulative_percentage' => ($runningMax > 0) ? round(($runningScore / $runningMax) * 100, 1) : 0,
            'sessions' => $sessionEntries,
        ];
    }
}
