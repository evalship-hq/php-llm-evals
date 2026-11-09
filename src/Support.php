<?php

namespace App;

use Anthropic\Client;

final class Support
{
    private Client $client;
    private string $prompt;

    public function __construct(?Client $client = null)
    {
        $this->client = $client ?? new Client(); // reads ANTHROPIC_API_KEY
        $this->prompt = file_get_contents(__DIR__ . '/../prompts/support.md');
    }

    public function answer(string $message): string
    {
        $reply = $this->client->messages->create(
            model: 'claude-opus-5-5',
            maxTokens: 1024,
            system: $this->prompt,
            // Thinking can't be turned off on this model; low effort keeps it short.
            outputConfig: ['effort' => 'low'],
            messages: [['role' => 'user', 'content' => $message]],
        );

        $text = '';
        foreach ($reply->content as $block) {
            if ($block->type === 'text') {
                $text .= $block->text;
            }
        }
        return $text;
    }
}
