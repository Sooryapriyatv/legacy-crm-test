<?php

namespace App\Filters;

use App\Libraries\JwtService;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\SignatureInvalidException;
use Throwable;

class JwtAuthFilter implements FilterInterface
{
    public function before(
        RequestInterface $request,
        $arguments = null
    ) {
        $header = $request->getHeaderLine('Authorization');

        if (empty($header)) {
            return service('response')
                ->setStatusCode(401)
                ->setJSON([
                    'status' => false,
                    'message' => 'Authorization token is required.'
                ]);
        }

        if (!preg_match('/Bearer\s+(.+)/i', $header, $matches)) {
            return service('response')
                ->setStatusCode(401)
                ->setJSON([
                    'status' => false,
                    'message' => 'Invalid authorization header.'
                ]);
        }

        $token = trim($matches[1]);

        try {
            $jwtService = new JwtService();

            $decoded = $jwtService->validateToken($token);

            $request->jwtUser = $decoded;

        } catch (ExpiredException $e) {

            return service('response')
                ->setStatusCode(401)
                ->setJSON([
                    'status' => false,
                    'message' => 'Token has expired.'
                ]);

        } catch (
            SignatureInvalidException |
            Throwable $e
        ) {

            return service('response')
                ->setStatusCode(401)
                ->setJSON([
                    'status' => false,
                    'message' => 'Invalid token.'
                ]);
        }
    }

    public function after(
        RequestInterface $request,
        ResponseInterface $response,
        $arguments = null
    ) {
    }
}