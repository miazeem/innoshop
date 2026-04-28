<?php
/**
 * Copyright (c) Since 2024 InnoShop - All Rights Reserved
 *
 * @link       https://www.innoshop.com
 * @author     InnoShop <team@innoshop.com>
 * @license    https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace InnoShop\Common\Models\Announcement;

use InnoShop\Common\Models\BaseModel;

class Translation extends BaseModel
{
    protected $table = 'announcement_translations';

    public $timestamps = false;

    protected $fillable = [
        'announcement_id', 'locale', 'text',
    ];
}
