<?php

declare(strict_types=1);

namespace Obeserva\Laravel\Listeners\Notifications;

use Illuminate\Notifications\Events\NotificationSending;
use Illuminate\Notifications\Events\NotificationSent;
use Obeserva\Contracts\Driver\TracerInterface;
use Obeserva\Contracts\Span\SpanKind;
use Obeserva\Laravel\Correlation\CorrelationContextStorage;

final readonly class TraceNotificationListener
{
    public function __construct(
        private TracerInterface $tracer,
        private CorrelationContextStorage $correlationStorage,
    ) {}

    public function handle(NotificationSending|NotificationSent $event): void
    {
        if ($event instanceof NotificationSending) {
            $this->handleSending($event);

            return;
        }

        $this->handleSent($event);
    }

    private function handleSending(NotificationSending $event): void
    {
        $span = $this->tracer->startSpan(
            'notification.send:'.class_basename($event->notification),
            SpanKind::Producer,
        );

        $span->setAttribute('notification.class', $event->notification::class);
        $span->setAttribute('notification.channel', (string) $event->channel);

        $correlationId = $this->correlationStorage->get();

        if ($correlationId !== null) {
            $span->setAttribute('correlation.id', $correlationId);
        }

        $span->addEvent('notification.sending');
        $span->end();
    }

    private function handleSent(NotificationSent $event): void
    {
        $span = $this->tracer->startSpan(
            'notification.sent:'.class_basename($event->notification),
            SpanKind::Internal,
        );

        $span->setAttribute('notification.class', $event->notification::class);
        $span->setAttribute('notification.channel', (string) $event->channel);
        $span->addEvent('notification.sent');
        $span->end();
    }
}
