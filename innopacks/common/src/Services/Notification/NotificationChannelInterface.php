<?php
/**
 * Copyright (c) Since 2024 InnoShop - All Rights Reserved
 *
 * @link       https://www.innoshop.com
 * @author     InnoShop <team@innoshop.com>
 * @license    https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace InnoShop\Common\Services\Notification;

interface NotificationChannelInterface
{
    /**
     * Send a notification through this channel.
     *
     * @param  string  $title  Notification title
     * @param  string  $content  Notification content (markdown supported)
     * @param  string  $level  Level: info, warning, error
     * @return void
     */
    public function send(string $title, string $content, string $level = 'info'): void;
}
