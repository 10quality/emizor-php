<?php

declare(strict_types=1);

namespace Emizor\Dto;

/**
 * Encapsulates invoice creation query parameters.
 */
final readonly class InvoiceOptions
{
    public function __construct(
        public bool $shouldEmit = true,
        public bool $paid = false,
        public bool $sendMail = true,
    ) {
    }

    /**
     * Converts the DTO to Emizor query parameters.
     *
     * @return array<string, string>
     */
    public function toQuery(): array
    {
        return [
            'should_emit' => $this->shouldEmit ? 'true' : 'false',
            'paid' => $this->paid ? 'true' : 'false',
            'send_mail' => $this->sendMail ? 'true' : 'false',
        ];
    }
}
