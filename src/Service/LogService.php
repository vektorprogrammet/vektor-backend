<?php

namespace App\Service;

use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class LogService implements LoggerInterface
{
    /**
     * LogService constructor.
     */
    public function __construct(private readonly LoggerInterface $monoLogger, private readonly SlackMessenger $slackMessenger, private readonly UserService $userService, private readonly RequestStack $requestStack, private readonly string $env)
    {
    }

    /**
     * System is unusable.
     *
     * @param string $message
     */
    public function emergency($message, array $context = []): void
    {
        $this->monoLogger->emergency($message, $context);
        $this->log('EMERGENCY', $message, $context);
    }

    /**
     * Action must be taken immediately.
     *
     * Example: Entire website down, database unavailable, etc. This should
     * trigger the SMS alerts and wake you up.
     *
     * @param string $message
     */
    public function alert($message, array $context = []): void
    {
        $this->monoLogger->alert($message, $context);
        $this->log('ALERT', $message, $context);
    }

    /**
     * Critical conditions.
     *
     * Example: Application component unavailable, unexpected exception.
     *
     * @param string $message
     */
    public function critical($message, array $context = []): void
    {
        $this->monoLogger->critical($message, $context);
        $this->log('CRITICAL', $message, $context);
    }

    /**
     * Runtime errors that do not require immediate action but should typically
     * be logged and monitored.
     *
     * @param string $message
     */
    public function error($message, array $context = []): void
    {
        $this->monoLogger->error($message, $context);
        $this->log('ERROR', $message, $context);
    }

    /**
     * Exceptional occurrences that are not errors.
     *
     * Example: Use of deprecated APIs, poor use of an API, undesirable things
     * that are not necessarily wrong.
     *
     * @param string $message
     */
    public function warning($message, array $context = []): void
    {
        $this->monoLogger->warning($message, $context);
        $this->log('WARNING', $message, $context);
    }

    /**
     * Normal but significant events.
     *
     * @param string $message
     */
    public function notice($message, array $context = []): void
    {
        $this->monoLogger->notice($message, $context);
        $this->log('NOTICE', $message, $context);
    }

    /**
     * Interesting events.
     *
     * Example: User log in, SQL log.
     *
     * @param string $message
     */
    public function info($message, array $context = []): void
    {
        $this->monoLogger->info($message, $context);
        $this->log('INFO', $message, $context);
    }

    /**
     * Detailed debug information.
     *
     * @param string $message
     */
    public function debug($message, array $context = []): void
    {
        $this->monoLogger->debug($message, $context);
        $this->log('DEBUG', $message, $context);
    }

    /**
     * Logs with an arbitrary level.
     *
     * @param string $message
     */
    public function log($level, $message, array $context = []): void
    {
        $this->monoLogger->log(200, $message, $context);
        $this->slackMessenger->log('', $this->createAttachmentData($level, $message, $context));
    }

    private function createAttachmentData($level, $message, array $data): array
    {
        $request = $this->requestStack->getMainRequest();
        $method = $request ? $request->getMethod() : '';
        $path = $request ? $request->getPathInfo() : '???';
        if ('staging' === $this->env) {
            $path = $request ? $request->getUri() : '???';
        }

        $default = [
            'color' => $this->getLogColor($level),
            'author_name' => $this->userService->getCurrentUserNameAndDepartment(),
            'author_icon' => $this->userService->getCurrentProfilePicture(),
            'text' => "$message",
            'footer' => "$level - $method $path",
        ];

        return array_merge($default, $data);
    }

    private function getLogColor($level): string
    {
        switch ($level) {
            case 'INFO':
                return '#6fceee';
            case 'WARNING':
                return '#fd7e14';
            case 'CRITICAL':
            case 'ERROR':
            case 'ALERT':
            case 'EMERGENCY':
                return '#dc3545';
            default:
                return '#007bff';
        }
    }
}
