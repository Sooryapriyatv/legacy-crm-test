<?php

namespace App\Libraries;

use Config\Jwt as JwtConfig;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class JwtService
{
    protected JwtConfig $config;

    public function __construct()
    {
        $this->config = new JwtConfig();
    }

    public function generateToken(array $user): array
    {
        $issuedAt = time();
        $expiration = $issuedAt + $this->config->expiration;

        $payload = [
            'iat'   => $issuedAt,
            'exp'   => $expiration,
            'sub'   => (string) $user['id'],
            'email' => $user['email'],
            'role'  => $user['role'],
        ];

        $token = JWT::encode(
            $payload,
            $this->config->secretKey,
            $this->config->algorithm
        );

        return [
            'token'      => $token,
            'expires_at' => date('c', $expiration),
            'expires_in' => $this->config->expiration,
        ];
    }

    public function validateToken(string $token): object
    {
        return JWT::decode(
            $token,
            new Key(
                $this->config->secretKey,
                $this->config->algorithm
            )
        );
    }
}