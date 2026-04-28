<?php
/**
 * Copyright (c) Since 2024 InnoShop - All Rights Reserved
 *
 * @link       https://www.innoshop.com
 * @author     InnoShop <team@innoshop.com>
 * @license    https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace InnoShop\Common\Repositories;

use Exception;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use InnoShop\Common\Models\Announcement;
use Throwable;

class AnnouncementRepo extends BaseRepo
{
    /**
     * @return array[]
     */
    public static function getCriteria(): array
    {
        return [
            ['name' => 'text', 'type' => 'input', 'label' => trans('panel/announcement.text')],
        ];
    }

    /**
     * Get search field options for data_search component.
     */
    public static function getSearchFieldOptions(): array
    {
        $options = [
            ['value' => '', 'label' => trans('panel/common.all_fields')],
            ['value' => 'text', 'label' => trans('panel/announcement.text')],
        ];

        return fire_hook_filter('common.repo.announcement.search_field_options', $options);
    }

    /**
     * Get filter button options for data_search component.
     */
    public static function getFilterButtonOptions(): array
    {
        $filters = [
            [
                'name'    => 'active',
                'label'   => trans('panel/common.status'),
                'type'    => 'button',
                'options' => [
                    ['value' => '', 'label' => trans('panel/common.all')],
                    ['value' => '1', 'label' => trans('panel/common.active_yes')],
                    ['value' => '0', 'label' => trans('panel/common.active_no')],
                ],
            ],
        ];

        return fire_hook_filter('common.repo.announcement.filter_button_options', $filters);
    }

    /**
     * @param  $filters
     * @return LengthAwarePaginator
     *
     * @throws Exception
     */
    public function list($filters = []): LengthAwarePaginator
    {
        return $this->builder($filters)->paginate();
    }

    /**
     * Get query builder.
     */
    public function builder(array $filters = []): Builder
    {
        $builder = Announcement::query()->with(['translation']);

        if (isset($filters['active'])) {
            $builder->where('active', (bool) $filters['active']);
        }

        $keyword     = $filters['keyword'] ?? '';
        $searchField = $filters['search_field'] ?? '';

        if ($keyword && $searchField === 'text') {
            $builder->whereHas('translation', function ($query) use ($keyword) {
                $query->where('text', 'like', "%{$keyword}%");
            });
        } elseif ($keyword) {
            $builder->where(function ($query) use ($keyword) {
                $query->where('plugin_code', 'like', "%{$keyword}%")
                    ->orWhereHas('translation', function ($q) use ($keyword) {
                        $q->where('text', 'like', "%{$keyword}%");
                    });
            });
        }

        return fire_hook_filter('repo.announcement.builder', $builder);
    }

    /**
     * @param  $data
     * @return Announcement
     *
     * @throws Exception|Throwable
     */
    public function create($data): Announcement
    {
        $item = new Announcement;

        return $this->createOrUpdate($item, $data);
    }

    /**
     * @param  $item
     * @param  $data
     * @return mixed
     *
     * @throws Exception|Throwable
     */
    public function update($item, $data): mixed
    {
        return $this->createOrUpdate($item, $data);
    }

    /**
     * @param  Announcement  $item
     * @param  $data
     * @return mixed
     *
     * @throws Exception|Throwable
     */
    private function createOrUpdate(Announcement $item, $data): mixed
    {
        DB::beginTransaction();

        try {
            $itemData = [
                'plugin_code' => $data['plugin_code'] ?? null,
                'url'         => $data['url'] ?? null,
                'sort_order'  => (int) ($data['sort_order'] ?? 0),
                'active'      => (bool) ($data['active'] ?? true),
            ];
            $item->fill($itemData);
            $item->saveOrFail();

            $translations = $data['translations'] ?? [];
            if ($translations) {
                $item->translations()->delete();
                foreach ($translations as $locale => $fields) {
                    if (is_array($fields) && ! empty($fields['text'])) {
                        $item->translations()->create([
                            'locale' => $locale,
                            'text'   => $fields['text'],
                        ]);
                    }
                }
            }

            DB::commit();

            return $item;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * @param  $item
     * @return void
     */
    public function destroy($item): void
    {
        $item->translations()->delete();
        $item->delete();
    }
}
