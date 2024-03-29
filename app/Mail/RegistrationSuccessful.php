<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Queue\SerializesModels;

class RegistrationSuccessful extends Mailable
{
    use Queueable, SerializesModels;// 
    /**
     * Create a new message instance.
     */
    public function __construct(
        public User $user
    ) {
    }
    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope // Đây là phương thức dùng để gửi email
    {
        return new Envelope(
            subject: 'Đăng ký tài khoản thành công',
            // tags: ['account'], // Đây là tag mà bạn muốn gửi kèm theo email
            // metadata: [ // Đây là thông tin mà bạn muốn gửi kèm theo email
            //     'user_id' => 1,
            // ],
            // replyTo: [
            //     new Address('gapgokienthuc@gmail.com', 'Gặp gỡ kiến thức'),
            // ],
            // from: new Address('uonghoanganh45@gmail.com', 'Hoàng Anh'),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.registration-successful',
            with: [
                'first_name' => $this->user->first_name,
                'last_name' => $this->user->last_name
            ]
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
