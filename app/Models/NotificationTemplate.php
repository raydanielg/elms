<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NotificationTemplate extends Model
{
    protected $fillable = ['key', 'event', 'channel', 'language', 'subject', 'body', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];

    public static function render(string $key, array $data = [], string $language = 'en'): ?array
    {
        $template = static::where('key', $key)->where('language', $language)->where('is_active', true)->first()
            ?? static::where('key', $key)->where('is_active', true)->first();

        if (!$template) return null;

        $subject = $template->subject;
        $body = $template->body;
        foreach ($data as $placeholder => $value) {
            $subject = str_replace('{{' . $placeholder . '}}', $value, $subject);
            $body = str_replace('{{' . $placeholder . '}}', $value, $body);
        }
        return ['subject' => $subject, 'body' => $body, 'channel' => $template->channel];
    }
}
