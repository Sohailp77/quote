<?php

namespace App\Services\Mail;

use App\Models\SmtpConfiguration;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Mailer\Transport\Dsn;
use Symfony\Component\Mailer\Transport\Smtp\EsmtpTransportFactory;

class MultiSmtpMailer
{
    /**
     * Send an email using the multi-SMTP failover logic.
     * 
     * @param mixed $notifiable
     * @param \Illuminate\Notifications\Notification $notification
     * @return bool
     */
    public function send($notifiable, $notification)
    {
        /** @var \Illuminate\Database\Eloquent\Collection<int, \App\Models\SmtpConfiguration> $configs */
        $configs = SmtpConfiguration::where('is_active', true)
            ->orderBy('priority')
            ->orderBy('last_used_at', 'asc')
            ->get();

        if ($configs->isEmpty()) {
            Log::error("MultiSmtpMailer: No active SMTP configurations found. Falling back to default mailer.");
            $notifiable->notify($notification);
            return false;
        }

        foreach ($configs as $config) {
            try {
                $this->applyConfig($config);
                
                $notifiable->notify($notification);
                
                $config->update(['last_used_at' => now(), 'fail_count' => 0]);
                return true;
            } catch (\Exception $e) {
                Log::warning("MultiSmtpMailer: Failed sending with SMTP [{$config->name}]. Error: {$e->getMessage()}");
                
                $config->increment('fail_count');
                $config->update([
                    'last_fail_at' => now(),
                    'last_error' => $e->getMessage(),
                    'is_active' => $config->fail_count < 5 // Deactivate after 5 failures
                ]);
            }
        }

        Log::error("MultiSmtpMailer: All SMTP configurations failed.");
        return false;
    }

    /**
     * Dynamically apply SMTP configuration to the mailer.
     */
    protected function applyConfig(\App\Models\SmtpConfiguration $config)
    {
        Config::set('mail.mailers.smtp_dynamic', [
            'transport' => 'smtp',
            'host' => $config->host,
            'port' => $config->port,
            'encryption' => $config->encryption,
            'username' => $config->username,
            'password' => $config->password,
            'timeout' => null,
            'local_domain' => env('MAIL_EHLO_DOMAIN'),
        ]);

        Config::set('mail.from.address', $config->from_address);
        Config::set('mail.from.name', $config->from_name);
        Config::set('mail.default', 'smtp_dynamic');
        
        // Force re-resolve the mailer to apply new config
        if (app()->resolved('mail.manager')) {
            app()->make('mail.manager')->forgetMailers();
        }
    }
}
