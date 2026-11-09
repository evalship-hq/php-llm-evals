# LLM evals for PHP

A PHP support bot whose AI call is tested on every pull request with [tinyeval](https://tinyeval.com).

## Run it

Clone the repo, or use it as a template. Then:

```sh
composer install
export ANTHROPIC_API_KEY=sk-ant-...
./tinyeval
```

```
support
  ✓ refund after the window 5.9s
  ✓ missing package 4.5s
  ✓ refund inside the window 4.7s

All 3 evals passed (eval-results.json)
```

## What's here

- `src/Support.php`: the bot. `answer()` sends one message to Claude with the Anthropic PHP SDK.
- `prompts/support.md`: its system prompt.
- `evals/support.sh`: three test cases and the checks on each reply.
- `evals/worker.php`: starts the bot once and answers every test case.
- `tinyeval`: the eval runner, a single bash file.
- `.github/workflows/evals.yml`: runs the evals on each pull request and on main.

## Use it for your app

Replace `answer()` with your own AI call. Keep the worker, so your app starts once per run and not once per test case. It also turns API failures that aren't your app's fault, such as a missing key, a rate limit or an outage, into "couldn't run" instead of a failed eval.

Write your own test cases in `evals/`. Each one has an input and lines that grade the reply, such as `matches`, `json` and `judge`. See [Writing evals](https://github.com/evalship-hq/tinyeval#writing-evals).

In a Laravel app, boot the app once at the top of the worker:

```php
require __DIR__.'/../vendor/autoload.php';
$app = require __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
```

Then call your service from the loop. Config, the container and the database are ready for every test case.

## In CI

Add `ANTHROPIC_API_KEY` as a repository secret. The judge uses the same key. The workflow installs PHP and the Composer packages, then runs each test case 3 times with `./tinyeval -n 3`. A test case passes when at least half of its runs pass. A failing eval fails the job, so you can make it a required check. The results are uploaded as the `eval-results` artifact, also when the job fails. Without the secret, the workflow skips the evals and says so.

## Compare pull requests with main

A fresh CI run has no earlier run to compare with. [evalship](https://evalship.com) reads `eval-results.json`, compares each pull request with main, tells flaky evals from real drops and comments on the pull request.

## License

MIT, see [LICENSE](LICENSE).
