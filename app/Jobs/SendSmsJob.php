<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Services\Sms\SmsManager;

class SendSmsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected string $to;
    protected string $templateKey;
    protected array $data;
    protected string $language;

    public function __construct(string $to, string $templateKey, array $data = [], string $language = 'en')
    {
        $this->to = $to;
        $this->templateKey = $templateKey;
        $this->data = $data;
        $this->language = $language;
    }

    public function handle(SmsManager $sms): void
    {
        $sms->sendTemplated($this->to, $this->templateKey, $this->data, $this->language);
    }

    public function failed(\Throwable $exception): void
    {
        \Log::error('SMS job failed', [
            'to' => $this->to,
            'template' => $this->templateKey,
            'error' => $exception->getMessage(),
        ]);
    }
}
