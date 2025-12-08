<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class GenerateApiDocs extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'generate:api-docs';

    /**
     * The console command description.
     */
    protected $description = 'Generate both general and mobile API documentation using Scribe and copy the logo';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('📁 Starting full API documentation generation process...');

        // Generate General API Docs
        $this->info('📘 Generating General API docs (config: scribe)');
        $this->generateDocs('scribe');

        // Generate Mobile API Docs
        $this->info('📱 Generating Mobile API docs (config: scribe_mobile)');
        $this->generateDocs('scribe_mobile');

        $this->info('🎉 All API documentation has been successfully generated!');
    }

    /**
     * Helper function to generate documentation using a specified Scribe config.
     */
    protected function generateDocs(string $configName)
    {
        $this->line("🚧 Running: scribe:generate --config={$configName}");

        $exitCode = Artisan::call('scribe:generate', [
            '--config' => $configName,
            '-v' => true,
        ]);

        $output = Artisan::output();
        $this->line($output);

        if ($exitCode !== 0) {
            $this->error("❌ Failed to generate API docs for config: {$configName} (exit code: {$exitCode}).");
        } else {
            $this->info("✅ Successfully generated API docs using '{$configName}' config.");
            $this->copyLogoFor($configName);
        }
    }

    /**
     * Copy the logo to the appropriate docs/img folder based on the config name.
     */
    protected function copyLogoFor(string $configName)
    {
        $source = public_path('images/logo.png');

        // Set output path depending on config name
        $outputDir = match ($configName) {
            'scribe' => public_path('docs/api'),
            'scribe_mobile' => public_path('docs/mobile'),
            default => public_path("docs/{$configName}"),
        };

        $imgDir = $outputDir . '/images';
        $destination = $imgDir . '/logo.png';

        if (!file_exists($source)) {
            $this->warn("⚠️ Logo not found at {$source}, skipping copy.");
            return;
        }

        if (!is_dir($imgDir)) {
            mkdir($imgDir, 0755, true);
        }

        if (copy($source, $destination)) {
            $this->info("🖼️ Logo copied to: {$destination}");
        } else {
            $this->warn("⚠️ Failed to copy logo to {$destination}");
        }
    }
}
