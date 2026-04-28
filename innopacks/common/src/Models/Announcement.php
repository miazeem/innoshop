<?php
/**
 * Copyright (c) Since 2024 InnoShop - All Rights Reserved
 *
 * @link       https://www.innoshop.com
 * @author     InnoShop <team@innoshop.com>
 * @license    https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace InnoShop\Common\Models;

use Illuminate\Database\Eloquent\Model;
use InnoShop\Common\Traits\Translatable;

class Announcement extends Model
{
    use Translatable;

    protected $table = 'announcements';

    protected $fillable = [
        'plugin_code',
        'url',
        'sort_order',
        'active',
    ];

    protected $casts = [
        'active'     => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Get active announcements sorted by sort_order, resolved to current locale.
     */
    public static function getActiveItems(): array
    {
        return static::with('translation')
            ->where('active', true)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get()
            ->map(fn ($row) => [
                'text' => $row->translation->text ?? $row->translations->first()?->text ?? '',
                'url'  => $row->url ?: '',
            ])
            ->filter(fn ($item) => $item['text'] !== '')
            ->values()
            ->all();
    }
}
