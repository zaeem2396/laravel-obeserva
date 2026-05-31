<?php

declare(strict_types=1);

namespace Obeserva\ScoutDriver;

use Obeserva\Contracts\Span\SpanInterface;
use Obeserva\Contracts\Span\SpanKind;

final readonly class ScoutContextBridge
{
    public function __construct(
        private ScoutApmClientInterface $client,
        private ScoutConfig $config,
    ) {}

    public function applyDefaultTags(): void
    {
        foreach ($this->config->defaultTags as $tag => $value) {
            $this->client->tagRequest($tag, $value);
        }

        if ($this->config->applicationName !== '') {
            $this->client->tagRequest('obeserva.application', $this->config->applicationName);
        }
    }

    public function bridgeSpanContext(SpanInterface $span): void
    {
        $this->client->addContext('trace.id', $span->getTraceId());
        $this->client->addContext('span.id', $span->getSpanId());

        if ($span->getParentSpanId() !== null) {
            $this->client->addContext('trace.parent_span_id', $span->getParentSpanId());
        }
    }

    public function bridgeSpanAttributes(SpanInterface $span): void
    {
        foreach ($span->getAttributes() as $key => $value) {
            if (! is_scalar($value) && $value !== null) {
                continue;
            }

            $this->client->addContext((string) $key, (string) $value);
        }

        if ($this->shouldTagRequest($span, $span->getKind())) {
            foreach ($span->getAttributes() as $key => $value) {
                if (! is_scalar($value) && $value !== null) {
                    continue;
                }

                $this->client->tagRequest((string) $key, (string) $value);
            }
        }
    }

    public function isRootSpan(SpanInterface $span): bool
    {
        return $span->getParentSpanId() === null && $span->getName() !== '';
    }

    public function shouldTagRequest(SpanInterface $span): bool
    {
        return $span->getKind() === SpanKind::Server || $this->isRootSpan($span);
    }
}
