<?php

declare(strict_types=1);

namespace Emizor;

use Emizor\Exceptions\ConfigurationException;

/**
 * Stores SDK configuration for the Emizor API.
 */
final readonly class Config
{
    public function __construct(
        public string $baseUrl,
        public string $apiToken,
        public string $apiSecret,
    ) {
        if ($this->baseUrl === '') {
            throw new ConfigurationException('The Emizor base URL cannot be empty.');
        }
        if ($this->apiToken === '') {
            throw new ConfigurationException('The Emizor API token cannot be empty.');
        }
        if ($this->apiSecret === '') {
            throw new ConfigurationException('The Emizor API secret cannot be empty.');
        }
    }

    /**
     * Builds configuration from environment variables.
     */
    public static function fromEnvironment(): self
    {
        return new self(
            self::readEnvironment('EMIZOR_BASE_URL'),
            self::readEnvironment('EMIZOR_API_TOKEN'),
            self::readEnvironment('EMIZOR_API_SECRET'),
        );
    }

    /**
     * Returns default request headers for the Emizor API.
     *
     * @return array<string, string>
     */
    public function headers(): array
    {
        return [
            'X-Api-Token' => $this->apiToken,
            'X-Api-Secret' => $this->apiSecret,
            'X-Requested-With' => 'XMLHttpRequest',
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ];
    }

    /**
     * Reads a non-empty environment variable.
     */
    private static function readEnvironment(string $name): string
    {
        $value = getenv($name);
        if ($value === false || trim($value) === '') {
            throw new ConfigurationException(sprintf('Environment variable "%s" is required.', $name));
        }

        return trim($value);
    }
}
