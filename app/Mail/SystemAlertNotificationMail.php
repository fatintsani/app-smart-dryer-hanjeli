<?php

namespace App\Mail;

use App\Models\SystemAlert;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SystemAlertNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $alertTitle;
    public string $alertMessage;
    public string $level;
    public string $category;
    public ?string $batchCode;
    public ?float $tempInternal;
    public ?float $humidityInternal;
    public ?float $grainMoisture;
    public ?string $recordedTime;
    public ?string $actionUrl;

    /**
     * Create a new message instance.
     */
    public function __construct(
        SystemAlert|array $alert,
        ?float $tempInternal = null,
        ?float $humidityInternal = null,
        ?float $grainMoisture = null,
        ?string $actionUrl = null
    ) {
        if ($alert instanceof SystemAlert) {
            $this->alertTitle = $alert->title;
            $this->alertMessage = $alert->message;
            $this->level = strtoupper($alert->level ?? 'INFO');
            $this->category = strtoupper($alert->category ?? 'SYSTEM');
            $this->batchCode = $alert->batch?->batch_code ?? null;
            $this->recordedTime = ($alert->created_at ?? now())->translatedFormat('d F Y, H:i') . ' WIB';
        } else {
            $this->alertTitle = $alert['title'] ?? 'Notifikasi Sistem';
            $this->alertMessage = $alert['message'] ?? '-';
            $this->level = strtoupper($alert['level'] ?? 'INFO');
            $this->category = strtoupper($alert['category'] ?? 'SYSTEM');
            $this->batchCode = $alert['batchCode'] ?? null;
            $this->recordedTime = $alert['recordedTime'] ?? (now()->translatedFormat('d F Y, H:i') . ' WIB');
        }

        $this->tempInternal = $tempInternal;
        $this->humidityInternal = $humidityInternal;
        $this->grainMoisture = $grainMoisture;
        $this->actionUrl = $actionUrl ?? url('/monitoring');
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $prefix = match ($this->level) {
            'CRITICAL', 'EMERGENCY' => '[KRITIS]',
            'WARNING' => '[PERINGATAN]',
            'SUCCESS' => '[SUKSES]',
            default => '[INFO]',
        };

        return new Envelope(
            subject: "{$prefix} {$this->alertTitle} — Smart Dryer Hanjeli",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.system_alert',
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
