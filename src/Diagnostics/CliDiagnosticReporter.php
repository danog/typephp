<?php

namespace TypePhp\Diagnostics;

use League\CLImate\CLImate;
use PhpParser\Node;

final readonly class CliDiagnosticReporter implements DiagnosticReporter
{
    public function __construct(
        private CLImate $climate,
        private bool $printBacktrace = false,
    ) {
    }

    public function fatal(string $message): never
    {
        // TYPEPHP_COLLECT_ERRORS=<file>: append the error to <file>, skip the
        // offending source file and keep going, so that one run reports every
        // rejected construct of a large code base instead of only the first.
        $collect = getenv('TYPEPHP_COLLECT_ERRORS');
        if (is_string($collect) && $collect !== '') {
            file_put_contents($collect, $message . "\n", FILE_APPEND);
            throw new \TypePhp\Exception\Unsupported($message);
        }
        $this->climate->red("Fatal error: {$message}");
        if ($this->printBacktrace) {
            debug_print_backtrace();
        }
        exit(255);
    }

    public function warning(Node $node, string $file, string $message): void
    {
        $this->climate->magenta("{$message} in {$file}:{$node->getStartLine()}");
    }
}
