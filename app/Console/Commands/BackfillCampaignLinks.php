<?php

namespace App\Console\Commands;

use App\Models\ElevenlabsConversation;
use App\Services\ElevenlabsService;
use Illuminate\Console\Command;

class BackfillCampaignLinks extends Command
{
    protected $signature = 'elevenlabs:backfill-campaign-links
                            {--chunk=200 : Tamaño del lote para recorrer las conversaciones}
                            {--max=0 : Máximo de conversaciones a intentar (0 = sin límite)}';

    protected $description = 'Vincula conversaciones existentes de ElevenLabs con sus campañas y llamadas';

    public function handle(): int
    {
        $chunkSize = max((int) $this->option('chunk'), 50);
        $max = max((int) $this->option('max'), 0);

        $service = app(ElevenlabsService::class);

        $stats = [
            'processed' => 0,
            'linked' => 0,
            'already_linked' => 0,
            'missing_metadata' => 0,
            'no_match_found' => 0,
        ];

        $bar = $this->output->createProgressBar();
        $bar->start();

        $keepGoing = true;

        ElevenlabsConversation::query()
            ->whereNull('campaign_id')
            ->orderBy('id')
            ->chunkById($chunkSize, function ($conversations) use (&$stats, $service, $max, $bar, &$keepGoing) {
                foreach ($conversations as $conversation) {
                    if (!$keepGoing) {
                        return false;
                    }

                    if ($max > 0 && $stats['processed'] >= $max) {
                        $keepGoing = false;
                        return false;
                    }

                    $stats['processed']++;

                    if (!$conversation->metadata || !is_array($conversation->metadata)) {
                        $stats['missing_metadata']++;
                        $bar->advance();
                        continue;
                    }

                    if ($conversation->campaign_call_id) {
                        $stats['already_linked']++;
                        $bar->advance();
                        continue;
                    }

                    try {
                        $linked = $service->linkExistingConversation($conversation);

                        if ($linked) {
                            $stats['linked']++;
                        } else {
                            $stats['no_match_found']++;
                        }
                    } catch (\Throwable $e) {
                        $stats['no_match_found']++;
                        // Log eliminado para evitar saturar los logs
                    }

                    $bar->advance();
                }

                return $keepGoing;
            });

        $bar->finish();

        return Command::SUCCESS;
    }
}

