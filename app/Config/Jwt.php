<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class Jwt extends BaseConfig
{
    public string $secretKey;

    public string $algorithm = 'HS256';

    public int $expiration = 3600;

    public function __construct()
    {
        parent::__construct();

        $this->secretKey = env('JWT_SECRET_KEY');

        if (empty($this->secretKey)) {
            throw new \RuntimeException('JWT_SECRET_KEY is not configured.');
        }
    }
}