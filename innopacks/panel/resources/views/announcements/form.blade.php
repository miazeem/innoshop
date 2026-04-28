@extends('panel::layouts.app')

@section('title', __('panel/menu.announcements'))

<x-panel::form.right-btns />

@section('content')
<form class="needs-validation" novalidate id="app-form"
  action="{{ $announcement->id ? panel_route('announcements.update', [$announcement->id]) : panel_route('announcements.store') }}" method="POST">
  @csrf
  @method($announcement->id ? 'PUT' : 'POST')

  <div class="row">
    <div class="col-12 col-md-9">
      <div class="card mb-3 h-min-400">
        <div class="card-header">
          <h5 class="card-title mb-0">{{ __('panel/common.basic_info') }}</h5>
        </div>
        <div class="card-body">
          <div class="mb-3 col-12 col-md-8">
            <label class="form-label">{{ __('panel/announcement.text') }}</label>
            <x-common-form-locale-input
              name="text"
              :translations="locale_field_data($announcement, 'text')"
              :required="true"
              :label="__('panel/announcement.text')"
              :placeholder="__('panel/announcement.text')"
            />
          </div>
          <div class="mb-3 col-12 col-md-8">
            <x-common-form-input
              title="{{ __('panel/announcement.url') }}"
              name="url"
              :value="old('url', $announcement->url ?? '')"
              placeholder="https://"
            />
          </div>
        </div>
      </div>
    </div>

    <div class="col-12 col-md-3 ps-md-0">
      <div class="card">
        <div class="card-body">
          <x-common-form-switch-radio
            title="{{ __('panel/common.whether_enable') }}"
            name="active"
            :value="old('active', $announcement->active ?? true)"
            placeholder="{{ __('panel/common.whether_enable') }}"
          />
          <x-common-form-input
            title="{{ __('panel/announcement.sort_order') }}"
            name="sort_order"
            :value="old('sort_order', $announcement->sort_order ?? 0)"
          />
        </div>
      </div>
    </div>
  </div>

  <button type="submit" class="d-none"></button>
</form>
@endsection
