<?php
/**
 * Copyright (c) Since 2024 InnoShop - All Rights Reserved
 *
 * @link       https://www.innoshop.com
 * @author     InnoShop <team@innoshop.com>
 * @license    https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace InnoShop\Common\Services\Notification;

use Illuminate\Support\Facades\Log;

class NotificationManager
{
    private static ?NotificationManager $instance = null;

    /** @var array<string, NotificationChannelInterface> */
    private array $channels = [];

    private function __construct() {}

    /**
     * Get singleton instance.
     */
    public static function getInstance(): NotificationManager
    {
        if (self::$instance === null) {
            self::$instance = new self;
        }

        return self::$instance;
    }

    /**
     * Register a notification channel.
     *
     * @param  string  $name  Unique channel name (e.g. 'wechat_work', 'slack', 'email')
     * @param  NotificationChannelInterface  $channel
     * @return void
     */
    public function registerChannel(string $name, NotificationChannelInterface $channel): void
    {
        $this->channels[$name] = $channel;
    }

    /**
     * Remove a registered channel.
     */
    public function removeChannel(string $name): void
    {
        unset($this->channels[$name]);
    }

    /**
     * Get all registered channel names.
     *
     * @return string[]
     */
    public function getChannels(): array
    {
        return array_keys($this->channels);
    }

    /**
     * Check if a channel is registered.
     */
    public function hasChannel(string $name): bool
    {
        return isset($this->channels[$name]);
    }

    /**
     * Send notification to all registered channels.
     *
     * @param  string  $title  Notification title
     * @param  string  $content  Notification content (markdown supported)
     * @param  string  $level  Level: info, warning, error
     * @return void
     */
    public function notify(string $title, string $content, string $level = 'info'): void
    {
        if (empty($this->channels)) {
            return;
        }

        foreach ($this->channels as $name => $channel) {
            try {
                $channel->send($title, $content, $level);
            } catch (\Throwable $e) {
                Log::warning("Notification channel [{$name}] failed: ".$e->getMessage());
            }
        }
    }

    /**
     * Reset singleton (useful for testing).
     */
    public static function resetInstance(): void
    {
        self::$instance = null;
    }
}
