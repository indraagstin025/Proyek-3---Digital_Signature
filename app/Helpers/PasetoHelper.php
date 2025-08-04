<?php

namespace App\Helpers;

use ParagonIE\Paseto\Builder;
use ParagonIE\Paseto\Keys\SymmetricKey;
use ParagonIE\Paseto\Parser;
use ParagonIE\Paseto\Purpose;
use ParagonIE\Paseto\JsonToken;
use Carbon\Carbon;

class PasetoHelper
{
    /**
     * Get the symmetric key for PASETO.
     * @return SymmetricKey
     */
    private static function getKey(): SymmetricKey
    {

        $keyString = env('PASETO_KEY');
        if (empty($keyString)) {
            throw new \Exception('PASETO_KEY Belum di set di .env file');
        }

        $decodedKey = base64_decode($keyString);

        if (strlen($decodedKey) !== 32) {
            throw new \Exception('Invalid PASETO key length. Key must be 32 bytes after base64 decoding.');
        }

        return new SymmetricKey($decodedKey);
    }

    /**
     * Create a PASETO token.
     * @param array $payload
     * @return string
     */
    public static function createToken(array $payload): string
    {
        $builder = new Builder();
        $builder->setKey(self::getKey());
        $builder->setPurpose(Purpose::local());
        $builder->setExpiration(new \DateTimeImmutable('+1 day'));
        $builder->setClaims($payload);

        return $builder->toString();
    }

    /**
     * Parse and validate a PASETO token.
     * @param string $token
     * @return JsonToken
     * @throws \ParagonIE\Paseto\Exception\PasetoException
     */
    public static function parseToken(string $token): JsonToken
    {
        $parser = new Parser();
        $parser->setKey(self::getKey());
        $parser->setPurpose(Purpose::local());
        return $parser->parse($token);
    }

    /**
     * Fungsi untuk mendapatkan sisa waktu kadaluarsa token dalam detik.
     * @param string $token
     * @return int
     */
    public static function getRemainingExpiry(string $token): int
    {
        try {
            $parsedToken = self::parseToken($token);
            $claims = $parsedToken->getClaims();

            $expiryString = $claims['exp'];
            $expiryTime = new Carbon($expiryString);
            $now = Carbon::now();

            if ($now->gte($expiryTime)) {
                return 0;
            }

            return $expiryTime->diffInSeconds($now);
        } catch (\Throwable $th) {
            return 0;
        }
    }
}