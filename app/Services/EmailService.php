<?php

namespace App\Services;

class EmailService
{
    protected $email;

    public function __construct()
    {
        $this->email = service('email');
    }

    public function sendWelcomeEmail(array $customer): bool
    {
        try {
            if (empty($customer['email'])) {
                log_message(
                    'error',
                    'Welcome email failed: customer email is missing.'
                );

                return false;
            }

            $this->email->clear();

            $this->email->setTo($customer['email']);
            $this->email->setSubject('Welcome to Our Company');

            $message = view('emails/welcome', [
                'name' => $customer['name'] ?? 'Customer',
            ]);

            $this->email->setMessage($message);

            if (!$this->email->send()) {
                log_message(
                    'error',
                    'Welcome email failed for: ' . $customer['email']
                );

                return false;
            }

            log_message(
                'info',
                'Welcome email sent to: ' . $customer['email']
            );

            return true;

        } catch (\Throwable $e) {

            log_message(
                'error',
                'Welcome email exception: ' . $e->getMessage()
            );

            return false;
        }
    }
}