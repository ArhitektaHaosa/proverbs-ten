<?php

namespace App\Services\Quiz;

use App\Models\Proverb;
use App\Models\QuizRun;
use Illuminate\Support\Str;

class QuizBuilder
{
    public function start(string $type, int $playerId, ?string $date = null): QuizRun
    {
        $date = $date ?? now()->toDateString();
        $seed = hash('sha256', $type . '|' . $date . '|' . $playerId);

        $built = $this->buildQuestions($type, $seed);

        return QuizRun::updateOrCreate(
            ['player_id' => $playerId, 'quiz_type' => $type, 'run_date' => $date],
            [
                'seed' => $seed,
                'questions' => $built['public'],
                // key stored server-side only; never returned
                'answers' => null,
            ]
        );
    }

    public function score(QuizRun $run, array $submitted): int
    {
        $key = $this->rebuildKey($run->seed, $run->quiz_type);
        $correct = 0;
        foreach ($key as $id => $answer) {
            if (($submitted[$id] ?? null) === $answer) {
                $correct++;
            }
        }
        $score = (int) round(($correct / max(count($key), 1)) * 100);

        $run->update([
            'answers' => $submitted,
            'score' => $score,
            'duration_ms' => $run->created_at->diffInMilliseconds(now()),
            'completed_at' => now(),
        ]);

        return $score;
    }

    private function buildQuestions(string $type, string $seed): array
    {
        mt_srand(crc32($seed));
        $proverbs = Proverb::public()
            ->where('enrichment_score', '>=', config('site.seo_min_enrichment_score'))
            ->inRandomOrder()
            ->limit(10)
            ->get();

        $public = [];
        $key = [];
        foreach ($proverbs as $p) {
            $qid = (string) Str::uuid();
            $public[] = [
                'id' => $qid,
                'text' => $p->text,
                'options' => $this->distractors($p),
            ];
            $key[$qid] = $p->id; // real impl stores the correct option id
        }

        return ['public' => $public, 'key' => $key];
    }

    private function rebuildKey(string $seed, string $type): array
    {
        return $this->buildQuestions($type, $seed)['key'];
    }

    private function distractors(Proverb $proverb): array
    {
        return [$proverb->meaning ?? 'True', 'Distractor A', 'Distractor B', 'Distractor C'];
    }
}
