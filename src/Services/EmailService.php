<?php

declare(strict_types=1);

namespace OrchidHelpers\Services;

use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\Mail;

class EmailService
{
    public static function make(): static
    {
        return new static();
    }

    /**
     * Send email
     *
     * @param  string|array  $to
     * @param  Mailable|string  $mailable
     * @param  array  $data
     * @param  callable|null  $callback
     * @return bool
     */
    public function send($to, $mailable, array $data = [], ?callable $callback = null): bool
    {
        try {
            if (is_string($mailable)) {
                $mailableInstance = new $mailable(...$data);
            } elseif ($mailable instanceof Mailable) {
                $mailableInstance = $mailable;
            } else {
                throw new \InvalidArgumentException('Mailable must be a string class name or Mailable instance');
            }
            
            if ($callback) {
                $callback($mailableInstance);
            }
            
            Mail::to($to)->send($mailableInstance);
            
            return true;
        } catch (\Exception $e) {
            // Log error if needed
            return false;
        }
    }

    /**
     * Send email to multiple recipients
     *
     * @param  array  $recipients
     * @param  Mailable|string  $mailable
     * @param  array  $data
     * @return bool
     */
    public function sendMultiple(array $recipients, $mailable, array $data = []): bool
    {
        $success = true;
        
        foreach ($recipients as $recipient) {
            if (!$this->send($recipient, $mailable, $data)) {
                $success = false;
            }
        }
        
        return $success;
    }

    /**
     * Send email with CC
     *
     * @param  string|array  $to
     * @param  string|array  $cc
     * @param  Mailable|string  $mailable
     * @param  array  $data
     * @return bool
     */
    public function sendWithCc($to, $cc, $mailable, array $data = []): bool
    {
        try {
            if (is_string($mailable)) {
                $mailableInstance = new $mailable(...$data);
            } elseif ($mailable instanceof Mailable) {
                $mailableInstance = $mailable;
            } else {
                throw new \InvalidArgumentException('Mailable must be a string class name or Mailable instance');
            }
            
            Mail::to($to)->cc($cc)->send($mailableInstance);
            
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Send email with BCC
     *
     * @param  string|array  $to
     * @param  string|array  $bcc
     * @param  Mailable|string  $mailable
     * @param  array  $data
     * @return bool
     */
    public function sendWithBcc($to, $bcc, $mailable, array $data = []): bool
    {
        try {
            if (is_string($mailable)) {
                $mailableInstance = new $mailable(...$data);
            } elseif ($mailable instanceof Mailable) {
                $mailableInstance = $mailable;
            } else {
                throw new \InvalidArgumentException('Mailable must be a string class name or Mailable instance');
            }
            
            Mail::to($to)->bcc($bcc)->send($mailableInstance);
            
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Send email with attachments
     *
     * @param  string|array  $to
     * @param  Mailable|string  $mailable
     * @param  array  $attachments
     * @param  array  $data
     * @return bool
     */
    public function sendWithAttachments($to, $mailable, array $attachments, array $data = []): bool
    {
        try {
            if (is_string($mailable)) {
                $mailableInstance = new $mailable(...$data);
            } elseif ($mailable instanceof Mailable) {
                $mailableInstance = $mailable;
            } else {
                throw new \InvalidArgumentException('Mailable must be a string class name or Mailable instance');
            }
            
            foreach ($attachments as $attachment) {
                if (is_array($attachment)) {
                    $mailableInstance->attach($attachment['path'], $attachment['options'] ?? []);
                } else {
                    $mailableInstance->attach($attachment);
                }
            }
            
            Mail::to($to)->send($mailableInstance);
            
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Queue email for later sending
     *
     * @param  string|array  $to
     * @param  Mailable|string  $mailable
     * @param  array  $data
     * @param  string|null  $queue
     * @return bool
     */
    public function queue($to, $mailable, array $data = [], ?string $queue = null): bool
    {
        try {
            if (is_string($mailable)) {
                $mailableInstance = new $mailable(...$data);
            } elseif ($mailable instanceof Mailable) {
                $mailableInstance = $mailable;
            } else {
                throw new \InvalidArgumentException('Mailable must be a string class name or Mailable instance');
            }
            
            if ($queue) {
                $mailableInstance->onQueue($queue);
            }
            
            Mail::to($to)->queue($mailableInstance);
            
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Send email later with delay
     *
     * @param  string|array  $to
     * @param  Mailable|string  $mailable
     * @param  \DateTimeInterface|\DateInterval|int  $delay
     * @param  array  $data
     * @return bool
     */
    public function later($to, $mailable, $delay, array $data = []): bool
    {
        try {
            if (is_string($mailable)) {
                $mailableInstance = new $mailable(...$data);
            } elseif ($mailable instanceof Mailable) {
                $mailableInstance = $mailable;
            } else {
                throw new \InvalidArgumentException('Mailable must be a string class name or Mailable instance');
            }
            
            Mail::to($to)->later($delay, $mailableInstance);
            
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Send raw email
     *
     * @param  string|array  $to
     * @param  string  $subject
     * @param  string  $body
     * @param  array  $headers
     * @return bool
     */
    public function sendRaw($to, string $subject, string $body, array $headers = []): bool
    {
        try {
            Mail::raw($body, function ($message) use ($to, $subject, $headers) {
                $message->to($to)
                        ->subject($subject);
                
                foreach ($headers as $key => $value) {
                    $message->getHeaders()->addTextHeader($key, $value);
                }
            });
            
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Send HTML email
     *
     * @param  string|array  $to
     * @param  string  $subject
     * @param  string  $html
     * @param  string|null  $plainText
     * @param  array  $headers
     * @return bool
     */
    public function sendHtml($to, string $subject, string $html, ?string $plainText = null, array $headers = []): bool
    {
        try {
            Mail::send([], [], function ($message) use ($to, $subject, $html, $plainText, $headers) {
                $message->to($to)
                        ->subject($subject)
                        ->html($html);
                
                if ($plainText) {
                    $message->text($plainText);
                }
                
                foreach ($headers as $key => $value) {
                    $message->getHeaders()->addTextHeader($key, $value);
                }
            });
            
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Send email using template
     *
     * @param  string|array  $to
     * @param  string  $subject
     * @param  string  $view
     * @param  array  $data
     * @param  array  $headers
     * @return bool
     */
    public function sendTemplate($to, string $subject, string $view, array $data = [], array $headers = []): bool
    {
        try {
            Mail::send($view, $data, function ($message) use ($to, $subject, $headers) {
                $message->to($to)
                        ->subject($subject);
                
                foreach ($headers as $key => $value) {
                    $message->getHeaders()->addTextHeader($key, $value);
                }
            });
            
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Validate email address
     *
     * @param  string  $email
     * @return bool
     */
    public function validateEmail(string $email): bool
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    /**
     * Validate multiple email addresses
     *
     * @param  array  $emails
     * @return array
     */
    public function validateEmails(array $emails): array
    {
        $valid = [];
        $invalid = [];
        
        foreach ($emails as $email) {
            if ($this->validateEmail($email)) {
                $valid[] = $email;
            } else {
                $invalid[] = $email;
            }
        }
        
        return [
            'valid' => $valid,
            'invalid' => $invalid,
            'valid_count' => count($valid),
            'invalid_count' => count($invalid),
        ];
    }

    /**
     * Get email domain
     *
     * @param  string  $email
     * @return string|null
     */
    public function getDomain(string $email): ?string
    {
        if (!$this->validateEmail($email)) {
            return null;
        }
        
        $parts = explode('@', $email);
        return $parts[1] ?? null;
    }

    /**
     * Get email local part
     *
     * @param  string  $email
     * @return string|null
     */
    public function getLocalPart(string $email): ?string
    {
        if (!$this->validateEmail($email)) {
            return null;
        }
        
        $parts = explode('@', $email);
        return $parts[0] ?? null;
    }

    /**
     * Check if email is disposable
     *
     * @param  string  $email
     * @return bool
     */
    public function isDisposable(string $email): bool
    {
        $domain = $this->getDomain($email);
        
        if (!$domain) {
            return false;
        }
        
        $disposableDomains = [
            'tempmail.com', 'mailinator.com', 'guerrillamail.com',
            '10minutemail.com', 'throwawaymail.com', 'yopmail.com',
            'trashmail.com', 'fakeinbox.com', 'getairmail.com',
        ];
        
        return in_array(strtolower($domain), $disposableDomains);
    }

    /**
     * Get email service provider
     *
     * @param  string  $email
     * @return string|null
     */
    public function getServiceProvider(string $email): ?string
    {
        $domain = $this->getDomain($email);
        
        if (!$domain) {
            return null;
        }
        
        $providers = [
            'gmail.com' => 'Google',
            'googlemail.com' => 'Google',
            'yahoo.com' => 'Yahoo',
            'outlook.com' => 'Microsoft',
            'hotmail.com' => 'Microsoft',
            'live.com' => 'Microsoft',
            'icloud.com' => 'Apple',
            'me.com' => 'Apple',
            'aol.com' => 'AOL',
            'protonmail.com' => 'ProtonMail',
            'zoho.com' => 'Zoho',
        ];
        
        return $providers[strtolower($domain)] ?? 'Other';
    }

    /**
     * Get failed email jobs count
     *
     * @return int
     */
    public function getFailedJobsCount(): int
    {
        if (class_exists('Illuminate\Queue\Failed\FailedJobProviderInterface')) {
            $failedJobProvider = app('Illuminate\Queue\Failed\FailedJobProviderInterface');
            return method_exists($failedJobProvider, 'count') ? $failedJobProvider->count() : 0;
        }
        
        return 0;
    }

    /**
     * Get email configuration
     *
     * @return array
     */
    public function getConfig(): array
    {
        return [
            'driver' => config('mail.default'),
            'host' => config('mail.mailers.smtp.host'),
            'port' => config('mail.mailers.smtp.port'),
            'encryption' => config('mail.mailers.smtp.encryption'),
            'from' => config('mail.from'),
            'reply_to' => config('mail.reply_to'),
        ];
    }

    /**
     * Test email connection
     *
     * @return bool
     */
    public function testConnection(): bool
    {
        try {
            Mail::raw('Test email', function ($message) {
                $message->to('test@example.com')
                        ->subject('Test Connection');
            });
            
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Create mailable from array data
     *
     * @param  array  $data
     * @return Mailable
     */
    public function createMailableFromArray(array $data): Mailable
    {
        return new class($data) extends Mailable {
            private $data;
            
            public function __construct(array $data)
            {
                $this->data = $data;
            }
            
            public function build()
            {
                $view = $this->data['view'] ?? 'emails.default';
                $subject = $this->data['subject'] ?? 'Email';
                
                return $this->subject($subject)
                           ->view($view, $this->data['view_data'] ?? []);
            }
        };
    }
}