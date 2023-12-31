<?php
/**
 * @filesource Kotchasan/Log/AbstractLogger.php
 *
 * @copyright 2016 Goragod.com
 * @license https://www.kotchasan.com/license/
 * @author Goragod Wiriya <admin@goragod.com>
 * @package Kotchasan
 */

namespace Kotchasan\Log;

use Psr\Log\LogLevel;

/**
 * Kotchasan Logger Class (PSR-3)
 *
 * @see https://www.kotchasan.com/
 */
abstract class AbstractLogger
{
    /**
     * Action must be taken immediately
     *
     * Example: Entire website down, database unavailable, etc. This should
     * trigger the SMS alerts and wake you up
     *
     * @param string $message
     * @param array  $context
     */
    public function alert($message, array $context = [])
    {
        $this->log(LogLevel::ALERT, $message, $context);
    }

    /**
     * Critical conditions
     *
     * Example: Application component unavailable, unexpected exception
     *
     * @param string $message
     * @param array  $context
     */
    public function critical($message, array $context = [])
    {
        $this->log(LogLevel::CRITICAL, $message, $context);
    }

    /**
     * Detailed debug information
     *
     * @param string $message
     * @param array  $context
     */
    public function debug($message, array $context = [])
    {
        $this->log(LogLevel::DEBUG, $message, $context);
    }

    /**
     * System is unusable
     *
     * @param string $message
     * @param array  $context
     */
    public function emergency($message, array $context = [])
    {
        $this->log(LogLevel::EMERGENCY, $message, $context);
    }

    /**
     * Runtime errors that do not require immediate action but should typically
     * be logged and monitored
     *
     * @param string $message
     * @param array  $context
     */
    public function error($message, array $context = [])
    {
        $this->log(LogLevel::ERROR, $message, $context);
    }

    /**
     * Interesting events
     *
     * Example: User logs in, SQL logs
     *
     * @param string $message
     * @param array  $context
     */
    public function info($message, array $context = [])
    {
        $this->log(LogLevel::INFO, $message, $context);
    }

    /**
     * Logs with an arbitrary level
     *
     * @param mixed  $level
     * @param string $message
     * @param array  $context
     */
    abstract public function log($level, $message, array $context = []);

    /**
     * Normal but significant events
     *
     * @param string $message
     * @param array  $context
     */
    public function notice($message, array $context = [])
    {
        $this->log(LogLevel::NOTICE, $message, $context);
    }

    /**
     * Exceptional occurrences that are not errors
     *
     * Example: Use of deprecated APIs, poor use of an API, undesirable things
     * that are not necessarily wrong
     *
     * @param string $message
     * @param array  $context
     */
    public function warning($message, array $context = [])
    {
        $this->log(LogLevel::WARNING, $message, $context);
    }
}
