<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use OpenApi\Analysers\AttributeAnnotationFactory;
use OpenApi\Analysers\DocBlockAnnotationFactory;
use OpenApi\Analysers\ReflectionAnalyser;
use OpenApi\Annotations\OpenApi;
use OpenApi\Generator;
use OpenApi\SourceFinder;

class GenerateOpenApiDocsCommand extends Command
{
    protected $signature = 'openapi:generate
                            {--output=public/openapi.json : Chemin du fichier de sortie}
                            {--format=json : Format de sortie (json ou yaml)}';

    protected $description = 'Génère la documentation OpenAPI à partir des attributs du code';

    public function handle(): int
    {
        $outputPath = base_path((string) $this->option('output'));
        $format = strtolower((string) $this->option('format'));

        $analyser = new ReflectionAnalyser([
            new AttributeAnnotationFactory,
            new DocBlockAnnotationFactory,
        ]);

        $generator = new Generator;
        $analyser->setGenerator($generator);

        $openapi = $generator
            ->setAnalyser($analyser)
            ->generate(new SourceFinder([base_path('app')]));

        if (! $openapi instanceof OpenApi) {
            $this->error('No OpenAPI documentation could be generated.');

            return self::FAILURE;
        }

        $openapi->saveAs($outputPath, $format);

        $this->info("Documentation OpenAPI générée : {$this->option('output')}");

        return self::SUCCESS;
    }
}
