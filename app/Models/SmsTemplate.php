<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SmsTemplate extends Model
{
    protected $fillable = ['key', 'event', 'category', 'language', 'template', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];

    public static function render(string $key, array $data = [], string $language = 'en'): ?string
    {
        $template = static::where('key', $key)->where('language', $language)->where('is_active', true)->first()
            ?? static::where('key', $key)->where('is_active', true)->first();

        if (!$template) return null;

        $body = $template->template;
        foreach ($data as $placeholder => $value) {
            $body = str_replace('{{' . $placeholder . '}}', $value, $body);
        }
        return $body;
    }
}
