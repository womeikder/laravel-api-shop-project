<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OrderPost extends Mailable
{
    use Queueable, SerializesModels;

    protected $order;

    /**
     * 构造器数据
     * @param $order
     */
    public function __construct($order)
    {
        $this->order = $order;
    }

    /**
     * 设置邮箱的发货数据(在env全局配置)
     * @return Envelope
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '订单发货',
        );
    }

    /**
     * 通过视图设置邮件的数据
     * @return Content
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.order-post',
            with: ['order' => $this->order]
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
