<?php

namespace Tests\Feature;

use App\Notifications\PlainMail;
use Illuminate\Notifications\AnonymousNotifiable;
use Tests\TestCase;

class ClaimMailReplyToTest extends TestCase
{
    public function test_plain_mail_can_include_reply_to(): void
    {
        $message = (new PlainMail(
            'Test subject',
            '<p>Test body</p>',
            [],
            [],
            [],
            'reply@autoschadeplan.nl'
        ))->toMail(new AnonymousNotifiable());

        $this->assertNotEmpty($message->replyTo);
        $this->assertSame('reply@autoschadeplan.nl', $message->replyTo[0][0]);
    }
}
