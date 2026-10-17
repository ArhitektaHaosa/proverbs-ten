<?php

namespace App\Jobs;

use App\Models\AiDraft;
use App\Models\Proverb;
use App\Services\Ai\AiRouter;
use App\Services\Ai\DraftSchema;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class GenerateDraftJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public int $proverbId) {}

    public function handle(AiRouter $router, DraftSchema $schema): void
    {
        $proverb = Proverb::findOrFail($this->proverbId);

        // Privacy: only text + language sent.
        $messages = [
            ['role' => 'system', 'content' => file_get_contents(resource_path('prompts/enrich.txt'))],
            ['role' => 'user', 'content' => $proverb->text . "\nLanguage: " . $proverb->language->code],
        ];

        $result = $router->complete($messages);

        $draft = AiDraft::create([
            'proverb_id' => $proverb->id,
            'provider' => config('ai.primary'),
            'model' => config('ai.groq.model'),
            'prompt_version' => config('ai.prompt_version'),
            'input_hash' => hash('sha256', $proverb->text),
            'status' => 'queued',
        ]);

        if (! $result->ok()) {
            $draft->update(['status' => 'failed', 'error' => $result->error]);
            return;
        }

        $data = json_decode($result->content, true);

        if (! $schema->validate($data ?? [])) {
            $draft->update(['status' => 'invalid', 'output' => $data]);
            return;
        }

        // Step 2: link selection from candidates only (not invented).
        $draft->update(['status' => 'ok', 'output' => $data, 'tokens' => $result->tokens]);
    }
}
