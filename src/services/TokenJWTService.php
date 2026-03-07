<?php
use Jose\Component\Signature\JWSBuilder;
use Jose\Component\Signature\JWSVerifier;

class TokenService
{
    public function __construct(
        private JWSBuilder $jwsBuilder,
        private JWSVerifier $jwsVerifier
    ) {}

    public function createToken(array $claims): string
    {
        // Your token creation logic
    }
}
?>