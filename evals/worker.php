<?php
// tinyeval worker: the app starts once, then answers each test case's input.
// In evals/support.sh:  worker php evals/worker.php
// The helper turns API failures that aren't the app's fault (no key, 401, 429, 5xx, timeout, network) into
// "couldn't run". Any other exception becomes the reply, so the eval fails.

require __DIR__ . '/../vendor/evalship/tinyeval/worker.php'; // first, so the app's output stays off the replies
require __DIR__ . '/../vendor/autoload.php';

$support = new App\Support();
Tinyeval\worker(fn (string $input): string => $support->answer($input));
