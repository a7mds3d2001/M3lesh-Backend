<?php

namespace App\Services\Fcm;

use Illuminate\Support\Facades\Http;
use RuntimeException;

class FcmService
{
    /**
     * @param  array<int, string>  $tokens
     * @param  array<string, mixed>  $notification  { title, body }
     * @param  array<string, mixed>  $data
     * @return array{success:int,failure:int,invalid_tokens:array<int,string>,responses:array<int,mixed>}
     */
    public function sendToTokens(array $tokens, array $notification, array $data = []): array
    {
        $tokens = array_values(array_unique(array_filter($tokens)));

        if ($tokens === []) {
            return [
                'success' => 0,
                'failure' => 0,
                'invalid_tokens' => [],
                'responses' => [],
            ];
        }

        $serverKey = config('fcm.server_key');
        if (! is_string($serverKey) || $serverKey === '') {
            throw new RuntimeException('FCM_SERVER_KEY is not configured.');
        }

        $url = (string) config('fcm.api_url');

        $invalidTokens = [];
        $responses = [];
        $success = 0;
        $failure = 0;

        foreach (array_chunk($tokens, 1000) as $chunk) {
            $payload = [
                'registration_ids' => $chunk,
                'notification' => array_filter($notification, fn ($v) => $v !== null && $v !== ''),
                // FCM prefers string values in data payload.
                'data' => $this->stringifyData($data),
            ];

            $response = Http::withHeaders([
                'Authorization' => 'key='.$serverKey,
                'Content-Type' => 'application/json',
            ])->post($url, $payload);

            $json = $response->json();
            $responses[] = $json;

            $success += (int) ($json['success'] ?? 0);
            $failure += (int) ($json['failure'] ?? 0);

            $results = $json['results'] ?? [];
            foreach ($results as $i => $result) {
                $error = $result['error'] ?? null;
                if (! $error) {
                    continue;
                }

                if (in_array($error, ['NotRegistered', 'InvalidRegistration'], true)) {
                    $invalidTokens[] = $chunk[$i] ?? null;
                }
            }
        }

        $invalidTokens = array_values(array_filter(array_unique($invalidTokens)));

        return [
            'success' => $success,
            'failure' => $failure,
            'invalid_tokens' => $invalidTokens,
            'responses' => $responses,
        ];
    }

    /**
     * Send a broadcast notification to a single FCM topic.
     *
     * @param  array<string, mixed>  $notification  { title, body, image? }
     * @param  array<string, mixed>  $data
     * @return array{success:int,failure:int,responses:array<int,mixed>}
     */
    public function sendToTopic(string $topic, array $notification, array $data = []): array
    {
        $serverKey = config('fcm.server_key');
        if (! is_string($serverKey) || $serverKey === '') {
            throw new RuntimeException('FCM_SERVER_KEY is not configured.');
        }

        $url = (string) config('fcm.api_url');

        $payload = [
            'to' => '/topics/'.$topic,
            'notification' => array_filter($notification, fn ($v) => $v !== null && $v !== ''),
            'data' => $this->stringifyData($data),
        ];

        $response = Http::withHeaders([
            'Authorization' => 'key='.$serverKey,
            'Content-Type' => 'application/json',
        ])->post($url, $payload);

        $json = $response->json();

        return [
            'success' => (int) ($json['success'] ?? 0),
            'failure' => (int) ($json['failure'] ?? 0),
            'responses' => [$json],
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, string>
     */
    protected function stringifyData(array $data): array
    {
        $out = [];
        foreach ($data as $k => $v) {
            if ($v === null) {
                continue;
            }

            if (is_bool($v)) {
                $out[(string) $k] = $v ? '1' : '0';

                continue;
            }

            if (is_scalar($v)) {
                $out[(string) $k] = (string) $v;

                continue;
            }

            $out[(string) $k] = json_encode($v, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?: '';
        }

        return $out;
    }
}
