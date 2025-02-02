<?php

declare(strict_types=1);

namespace Axleus\ConfigManager;

use Brick\VarExporter\ExportException;
use Brick\VarExporter\VarExporter;
use Laminas\ConfigAggregator\ConfigAggregator;
use Webimpress\SafeWriter\Exception\ExceptionInterface as FileWriterException;
use Webimpress\SafeWriter\FileWriter;

use function date;
use function sprintf;

final class ConfigWriter extends ConfigAggregator implements ConfigWriterInterface
{
    private const CONFIG_TEMPLATE = <<<'EOT'
<?php

declare(strict_types=1);

/**
 * This configuration file was generated by %s
 * at %s
 */
 %s

EOT;

    public function __construct(
        iterable $providers = [],
        array $postProcessors = [],
        array $preProcessors = [],
    ) {
        parent::__construct(
            providers: $providers,
            postProcessors: $postProcessors,
            preProcessors: $preProcessors
        );
    }

    public function writeConfig(string $targetFile): void
    {
        try {
            $config   = $this->getMergedConfig();
            $contents = sprintf(
                self::CONFIG_TEMPLATE,
                static::class,
                date('c'),
                VarExporter::export(
                    $config,
                    VarExporter::ADD_RETURN | VarExporter::CLOSURE_SNAPSHOT_USES | VarExporter::TRAILING_COMMA_IN_ARRAY
                )
            );
        } catch (ExportException $e) {
            throw ConfigCannotBeWrittenException::fromExporterException($e);
        }

        $mode = $config[self::CACHE_FILEMODE] ?? null;

        try {
            if ($mode !== null) {
                FileWriter::writeFile($targetFile, $contents, $mode);
            } else {
                FileWriter::writeFile($targetFile, $contents);
            }
        } catch (FileWriterException $e) {
            // ignore errors writing file
            throw $e;
        }
    }
}
