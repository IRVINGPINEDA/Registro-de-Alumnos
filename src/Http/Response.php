<?php
declare(strict_types=1);

namespace App\Http;

final class Response
{
    /**
     * @param array<string,string> $headers
     */
    public function __construct(
        public int $status = 200,
        public array $headers = ['Content-Type' => 'text/html; charset=utf-8'],
        public string $body = ''
    ) {}

    public static function html(string $body, int $status = 200): self
    {
        return new self($status, ['Content-Type' => 'text/html; charset=utf-8'], $body);
    }

    /**
     * @param array<mixed> $data
     */
    public static function json(array $data, int $status = 200): self
    {
        return new self(
            $status,
            ['Content-Type' => 'application/json; charset=utf-8'],
            json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?: '{}'
        );
    }

    public static function redirect(string $to, int $status = 302): self
    {
        return new self($status, ['Location' => $to], '');
    }
}

