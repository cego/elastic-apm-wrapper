<?php

namespace Cego\ElasticApmWrapper;

use Closure;
use Elastic\Apm\ElasticApm;

class ApmWrapper
{
    private static function isDisabled(): bool
    {
        return ! class_exists(ElasticApm::class);
    }

    /**
     * Sets the end timestamp and finalizes this object's state to ended.
     *
     * @return void
     */
    public static function endCurrentTransaction(): void
    {
        if (self::isDisabled()) {
            return;
        }

        ElasticApm::getCurrentTransaction()->end();
    }

    /**
     * Starts a new transaction, and executes the given callback it its context.
     *
     * @param string $name
     * @param string $type
     * @param Closure $callback
     *
     * @return mixed
     */
    public static function captureCurrentTransaction(string $name, string $type, Closure $callback)
    {
        if (self::isDisabled()) {
            return $callback();
        }

        return ElasticApm::captureCurrentTransaction($name, $type, $callback);
    }

    /**
     * Starts a new span, and executes the given callback it its context.
     *
     * @param string $name
     * @param string $type
     * @param Closure $callback
     *
     * @return mixed
     */
    public static function captureCurrentSpan(string $name, string $type, Closure $callback)
    {
        if (self::isDisabled()) {
            return $callback();
        }

        return ElasticApm::getCurrentTransaction()->captureCurrentSpan($name, $type, $callback);
    }

    /**
     * Sets the name of the current transaction
     *
     * @param string $name
     *
     * @return void
     */
    public static function setCurrentTransactionName(string $name): void
    {
        if (self::isDisabled()) {
            return;
        }

        ElasticApm::getCurrentTransaction()->setName($name);
    }

    /**
     * Discards the current transaction
     *
     * @return void
     */
    public static function discardCurrentTransaction(): void
    {
        if (self::isDisabled()) {
            return;
        }

        ElasticApm::getCurrentTransaction()->discard();
    }

    /**
     * Sets the outcome of the current transaction
     *
     * @param bool $success
     * @param bool $allowOverwrite
     *
     * @return void
     */
    public static function setCurrentTransactionOutcome(bool $success, bool $allowOverwrite): void
    {
        if (self::isDisabled()) {
            return;
        }

        // Early return if we do not allow overwriting the outcome, and it is already set.
        if ( ! $allowOverwrite && ElasticApm::getCurrentTransaction()->getOutcome() !== null) {
            return;
        }

        // Set outcome.
        ElasticApm::getCurrentTransaction()->setOutcome($success ? 'success' : 'failure');
    }
}
