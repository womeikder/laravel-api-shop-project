<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;

class EmailCode extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct()
    {
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '邮箱验证码',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        // 生成4位验证码
        $code = rand(1000, 9999);
        $email = $this->to[0]['address'];
        // 将邮箱缓存
        Cache::put('update_email'.$email, $code, now()->addMinute(15));

        return new Content(
            view: 'emails.email-code',
            with: ['code' => $code]
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
