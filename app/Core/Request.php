<?php

namespace App\Core;

class Request
{
    private array $get;
    private array $post;
    private array $server;
    private array $files;
    private ?array $jsonBody = null;

    public function __construct(
        ?array $get = null,
        ?array $post = null,
        ?array $server = null,
        ?array $files = null
    ) {
        $this->get = $get ?? $_GET;
        $this->post = $post ?? $_POST;
        $this->server = $server ?? $_SERVER;
        $this->files = $files ?? $_FILES;
    }

    public static function capture(): self
    {
        return new self();
    }

    public function method(): string
    {
        $method = strtoupper($this->server['REQUEST_METHOD'] ?? 'GET');

        if ($method === 'POST') {
            if (isset($this->post['_method'])) {
                $override = strtoupper($this->post['_method']);
                if (in_array($override, ['PUT', 'PATCH', 'DELETE', 'HEAD', 'OPTIONS'])) {
                    return $override;
                }
            }
            if (isset($this->server['HTTP_X_HTTP_METHOD_OVERRIDE'])) {
                $override = strtoupper($this->server['HTTP_X_HTTP_METHOD_OVERRIDE']);
                if (in_array($override, ['PUT', 'PATCH', 'DELETE'])) {
                    return $override;
                }
            }
        }

        return $method;
    }

    public function uri(): string
    {
        $uri = $this->server['REQUEST_URI'] ?? '/';
        $path = parse_url($uri, PHP_URL_PATH) ?? '/';

        // Support for subfolder running (e.g., http://localhost/leadpulse/public)
        $scriptName = $this->server['SCRIPT_NAME'] ?? '';
        $scriptDir = dirname($scriptName);

        if ($scriptDir !== '/' && $scriptDir !== '\\' && str_starts_with($path, $scriptDir)) {
            $path = substr($path, strlen($scriptDir));
        }

        $path = rtrim($path, '/');
        return $path === '' ? '/' : $path;
    }

    public function all(): array
    {
        $data = array_merge($this->get, $this->post, $this->json());
        unset($data['_token'], $data['_method']);
        return $data;
    }

    public function input(string $key, mixed $default = null): mixed
    {
        $all = $this->all();
        return $all[$key] ?? $default;
    }

    public function query(string $key, mixed $default = null): mixed
    {
        return $this->get[$key] ?? $default;
    }

    public function postData(?string $key = null, mixed $default = null): mixed
    {
        if ($key === null) {
            return $this->post;
        }
        return $this->post[$key] ?? $default;
    }

    public function has(string $key): bool
    {
        $all = $this->all();
        return array_key_exists($key, $all);
    }

    public function json(): array
    {
        if ($this->jsonBody !== null) {
            return $this->jsonBody;
        }

        $contentType = $this->server['CONTENT_TYPE'] ?? '';
        if (str_contains(strtolower($contentType), 'application/json')) {
            $content = file_get_contents('php://input');
            $decoded = json_decode($content, true);
            $this->jsonBody = is_array($decoded) ? $decoded : [];
        } else {
            $this->jsonBody = [];
        }

        return $this->jsonBody;
    }

    public function ip(): string
    {
        return $this->server['HTTP_X_FORWARDED_FOR'] ??
               $this->server['HTTP_CLIENT_IP'] ??
               $this->server['REMOTE_ADDR'] ?? '127.0.0.1';
    }

    public function userAgent(): string
    {
        return $this->server['HTTP_USER_AGENT'] ?? 'Unknown';
    }

    public function csrfToken(): ?string
    {
        return $this->post['_token'] ??
               $this->server['HTTP_X_CSRF_TOKEN'] ??
               null;
    }

    public function isMethod(string $method): bool
    {
        return $this->method() === strtoupper($method);
    }
}
