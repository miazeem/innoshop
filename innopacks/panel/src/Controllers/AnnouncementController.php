<?php
/**
 * Copyright (c) Since 2024 InnoShop - All Rights Reserved
 *
 * @link       https://www.innoshop.com
 * @author     InnoShop <team@innoshop.com>
 * @license    https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace InnoShop\Panel\Controllers;

use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use InnoShop\Common\Models\Announcement;
use InnoShop\Common\Repositories\AnnouncementRepo;

class AnnouncementController extends BaseController
{
    public function index(Request $request): mixed
    {
        $filters = $request->all();
        $data    = [
            'searchFields'  => AnnouncementRepo::getSearchFieldOptions(),
            'filterButtons' => AnnouncementRepo::getFilterButtonOptions(),
            'announcements' => AnnouncementRepo::getInstance()->list($filters),
        ];

        return inno_view('panel::announcements.index', $data);
    }

    public function create(): mixed
    {
        $data = ['announcement' => new Announcement];

        return inno_view('panel::announcements.form', $data);
    }

    public function store(Request $request): RedirectResponse
    {
        try {
            $data = $request->all();
            AnnouncementRepo::getInstance()->create($data);

            return redirect(panel_route('announcements.index'))
                ->with('success', common_trans('base.updated_success'));
        } catch (Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }

    public function edit(Announcement $announcement): mixed
    {
        $data = ['announcement' => $announcement];

        return inno_view('panel::announcements.form', $data);
    }

    public function update(Request $request, Announcement $announcement): RedirectResponse
    {
        try {
            $data = $request->all();
            AnnouncementRepo::getInstance()->update($announcement, $data);

            return back()->with('success', common_trans('base.updated_success'));
        } catch (Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }

    public function destroy(Announcement $announcement): RedirectResponse
    {
        try {
            AnnouncementRepo::getInstance()->destroy($announcement);

            return back()->with('success', common_trans('base.deleted_success'));
        } catch (Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }
}
