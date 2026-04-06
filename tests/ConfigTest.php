<?php

declare(strict_types=1);

namespace Emizor\Tests;

use Emizor\Config;
use Emizor\Exceptions\ConfigurationException;
use PHPUnit\Framework\TestCase;

final class ConfigTest extends TestCase
{
    protected function tearDown(): void
    {
        putenv('EMIZOR_BASE_URL');
        putenv('EMIZOR_API_TOKEN');
        putenv('EMIZOR_API_SECRET');
    }

    public function test_it_builds_headers(): void
    {
        $config = new Config('https://felapp2.emizor.com', 'token', 'secret');

        self::assertSame([
            'X-Api-Token' => 'token',
            'X-Api-Secret' => 'secret',
            'X-Requested-With' => 'XMLHttpRequest',
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ], $config->headers());
    }

    public function test_it_builds_configuration_from_environment(): void
    {
        putenv('EMIZOR_BASE_URL=https://felapp2.emizor.com');
        putenv('EMIZOR_API_TOKEN=token');
        putenv('EMIZOR_API_SECRET=secret');

        $config = Config::fromEnvironment();

        self::assertSame('https://felapp2.emizor.com', $config->baseUrl);
        self::assertSame('token', $config->apiToken);
        self::assertSame('secret', $config->apiSecret);
    }

    public function test_it_requires_environment_variables(): void
    {
        $this->expectException(ConfigurationException::class);
        $this->expectExceptionMessage('Environment variable "EMIZOR_BASE_URL" is required.');

        Config::fromEnvironment();
    }

    public function test_it_rejects_empty_constructor_values(): void
    {
        $this->expectException(ConfigurationException::class);
        $this->expectExceptionMessage('The Emizor base URL cannot be empty.');

        new Config('', 'token', 'secret');
    }

    public function test_it_rejects_an_empty_api_token(): void
    {
        $this->expectException(ConfigurationException::class);
        $this->expectExceptionMessage('The Emizor API token cannot be empty.');

        new Config('https://felapp2.emizor.com', '', 'secret');
    }

    public function test_it_rejects_an_empty_api_secret(): void
    {
        $this->expectException(ConfigurationException::class);
        $this->expectExceptionMessage('The Emizor API secret cannot be empty.');

        new Config('https://felapp2.emizor.com', 'token', '');
    }
}
