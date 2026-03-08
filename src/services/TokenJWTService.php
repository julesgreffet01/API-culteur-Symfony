<?php

namespace App\Services;

use Jose\Component\Core\AlgorithmManager;
use Jose\Component\Core\JWK;
use Jose\Component\Signature\Algorithm\HS256;
use Jose\Component\Signature\JWSBuilder;
use Jose\Component\Signature\JWSVerifier;
use Jose\Component\Signature\Serializer\CompactSerializer;

class TokenJWTService
{
    private JWK $jwk;
    private CompactSerializer $serializer;
    private JWSBuilder $jwsBuilder;
    private JWSVerifier $jwsVerifier;

    public function __construct(string $jwtJwkK)
    {
        $algorithmManager = new AlgorithmManager([
            new HS256(),
        ]);

        $this->jwk = new JWK([
            'kty' => 'oct',
            'k' => $jwtJwkK,
        ]);

        $this->serializer = new CompactSerializer();
        $this->jwsBuilder = new JWSBuilder($algorithmManager);
        $this->jwsVerifier = new JWSVerifier($algorithmManager);
    }

    public function createFromUserId(int $userId): string
    {
        $payload = json_encode([
            'id' => $userId,
            'exp' => time() + 10800,
        ], JSON_THROW_ON_ERROR);

        $jws = $this->jwsBuilder
            ->create()
            ->withPayload($payload)
            ->addSignature($this->jwk, ['alg' => 'HS256'])
            ->build();

        return $this->serializer->serialize($jws, 0);
    }

    public function getUserIdFromToken(string $token): int
    {
        $jws = $this->serializer->unserialize($token);

        $isValid = $this->jwsVerifier->verifyWithKey($jws, $this->jwk, 0);

        if (!$isValid) {
            throw new \RuntimeException('Token invalid.');
        }

        $payload = $jws->getPayload();

        if ($payload === null) {
            throw new \RuntimeException('Payload empty.');
        }

        $data = json_decode($payload, true, 512, JSON_THROW_ON_ERROR);

        if (!isset($data['id']) || !is_numeric($data['id'])) {
            throw new \RuntimeException('Claim id missing or invalid.');
        }

        return (int) $data['id'];
    }
}