<?php

namespace App\Services;

class BunnyStreamService
{
    protected string $libraryId;
    protected string $apiKey;
    protected string $tokenAuthKey;

    public function __construct()
    {
        $this->libraryId = config('services.bunny.library_id', env('BUNNY_LIBRARY_ID', ''));
        $this->apiKey = config('services.bunny.api_key', env('BUNNY_API_KEY', ''));
        $this->tokenAuthKey = config('services.bunny.token_key', env('BUNNY_TOKEN_KEY', ''));
    }

    /**
     * Generate signed direct video upload URL or signature for Bunny Stream API.
     */
    public function generateUploadSignature(string $title): array
    {
        $expiration = time() + 3600; // 1 hour expiration
        $libraryId = $this->libraryId;

        // Signature hash format for Bunny Direct Uploads
        $signature = hash('sha256', $libraryId . $this->apiKey . $expiration);

        return [
            'library_id' => $libraryId,
            'expiration' => $expiration,
            'signature' => $signature,
            'upload_endpoint' => "https://video.bunnycdn.com/library/{$libraryId}/videos",
        ];
    }

    /**
     * Generate secure player embed URL for a given video ID.
     */
    public function generateEmbedUrl(string $videoId, int $expiresInSeconds = 86400): string
    {
        $libraryId = $this->libraryId;
        
        if (empty($this->tokenAuthKey)) {
            return "https://iframe.mediadelivery.net/embed/{$libraryId}/{$videoId}";
        }

        $expires = time() + $expiresInSeconds;
        $hashableBase = $this->tokenAuthKey . $videoId . $expires;
        $token = hash('sha256', $hashableBase);

        return "https://iframe.mediadelivery.net/embed/{$libraryId}/{$videoId}?token={$token}&expires={$expires}";
    }
}
