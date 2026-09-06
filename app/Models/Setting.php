<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = ['key', 'value', 'group'];

    public static function get($key, $default = null)
    {
        return static::getAllByGroup()[$key] ?? $default;
    }

    public static function set($key, $value, $group = 'general')
    {
        $setting = static::updateOrCreate(
            ['key' => $key],
            ['value' => $value, 'group' => $group]
        );

        Cache::forget('settings.all');
        Cache::forget('settings.group.' . $group);

        return $setting;
    }

    public static function getAllByGroup($group = null)
    {
        $cacheKey = $group ? 'settings.group.' . $group : 'settings.all';

        return Cache::remember($cacheKey, now()->addMinutes(10), function () use ($group) {
            $query = static::query();
            if ($group) {
                $query->where('group', $group);
            } else {
                $query->where('group', '!=', 'mailer');
            }
            return $query->pluck('value', 'key')->toArray();
        });
    }

    public static function getMailerSettings(): array
    {
        $values = static::where('group', 'mailer')->pluck('value', 'key')->toArray();
        $password = '';

        if (!empty($values['mail_password'])) {
            try {
                $password = Crypt::decryptString($values['mail_password']);
            } catch (\Throwable) {
                $password = $values['mail_password'];
            }
        }

        $recipients = json_decode($values['mail_order_recipients'] ?? '[]', true);

        return [
            'mailer' => $values['mail_mailer'] ?? config('mail.default', 'log'),
            'host' => $values['mail_host'] ?? config('mail.mailers.smtp.host'),
            'port' => (int) ($values['mail_port'] ?? config('mail.mailers.smtp.port', 465)),
            'scheme' => $values['mail_scheme'] ?? 'smtps',
            'username' => $values['mail_username'] ?? '',
            'password' => $password,
            'from_address' => $values['mail_from_address'] ?? config('mail.from.address'),
            'from_name' => $values['mail_from_name'] ?? config('mail.from.name'),
            'reply_to' => $values['mail_reply_to'] ?? '',
            'recipients' => is_array($recipients) ? array_values(array_filter($recipients)) : [],
            'order_notifications' => ($values['mail_order_notifications'] ?? '1') === '1',
            'customer_notifications' => ($values['mail_customer_notifications'] ?? '1') === '1',
        ];
    }
}
