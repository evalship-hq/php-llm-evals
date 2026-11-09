<?php
// tinyeval worker: the app starts once, then answers each input that ends in a NUL byte.
// In evals/support.sh:  worker php evals/worker.php

use Anthropic\Core\Exceptions\APIConnectionException;
use Anthropic\Core\Exceptions\APIStatusException;
use Anthropic\Core\Exceptions\AuthenticationException;
use Anthropic\Core\Exceptions\InternalServerException;
use Anthropic\Core\Exceptions\RateLimitException;
use App\Support;

require __DIR__ . '/../vendor/autoload.php';

$support = new Support();

function reply(Support $support, string $input): string
{
    try {
        return $support->answer($input);
    } catch (AuthenticationException | RateLimitException | InternalServerException | APIConnectionException $e) {
        // Not the app's fault (no key, 401, 429, 5xx, timeout, network): couldn't run, not failed.
        $reason = $e instanceof APIStatusException
            ? "{$e->status} {$e->type?->value}: " . ($e->body['error']['message'] ?? '')
            : ($e->getPrevious()?->getMessage() ?? $e->getMessage());
        return "#tinyeval couldnt_run " . substr(preg_replace('/\s+/', ' ', $reason), 0, 200) . "\n";
    }
}

$rest = '';
while (!feof(STDIN)) {
    $inputs = explode("\0", $rest . fread(STDIN, 65536));
    $rest = array_pop($inputs);
    foreach ($inputs as $input) {
        fwrite(STDOUT, reply($support, $input) . "\0");
        fflush(STDOUT);
    }
}
