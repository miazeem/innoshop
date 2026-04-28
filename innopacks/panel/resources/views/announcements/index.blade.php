@extends('panel::layouts.app')
@section('body-class', 'page-announcement')

@section('title', __('panel/menu.announcements'))

@section('page-title-right')
<a href="{{ panel_route('announcements.create') }}" class="btn btn-primary"><i class="bi bi-plus-square"></i>
  {{ __('common/base.create') }}</a>
@endsection

@section('content')
<div class="card h-min-600" id="app">
  <div class="card-body">

    <x-panel-data-data-search
      :action="panel_route('announcements.index')"
      :searchFields="$searchFields ?? []"
      :filters="$filterButtons ?? []"
      :enableDateRange="false"
    />

    @if ($announcements->count())
    <div class="table-responsive">
      <table class="table align-middle">
        <thead>
          <tr>
            <td>{{ __('common/base.id') }}</td>
            <td>{{ __('panel/announcement.text') }}</td>
            <td>{{ __('panel/announcement.url') }}</td>
            <td>{{ __('panel/announcement.sort_order') }}</td>
            <td>{{ __('panel/common.active') }}</td>
            <td>{{ __('panel/common.actions') }}</td>
          </tr>
        </thead>
        <tbody>
          @foreach($announcements as $item)
          <tr>
            <td>{{ $item->id }}</td>
            <td>{{ $item->translation->text ?? '' }}</td>
            <td>{{ $item->url }}</td>
            <td>{{ $item->sort_order }}</td>
            <td>
              @include('panel::shared.list_switch', ['value' => $item->active, 'url' => panel_route('announcements.active', $item->id)])</td>
            <td>
              <div class="d-flex gap-1">
                <a href="{{ panel_route('announcements.edit', [$item->id]) }}">
                  <el-button size="small" plain type="primary">{{ __('common/base.edit')}}</el-button>
                </a>
                <form ref="deleteForm" action="{{ panel_route('announcements.destroy', [$item->id]) }}" method="POST"
                  class="d-inline">
                  @csrf
                  @method('DELETE')
                  <el-button size="small" type="danger" plain @click="open({{$item->id}})">{{
                    __('common/base.delete')}}</el-button>
                </form>
              </div>
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
    {{ $announcements->withQueryString()->links('panel::vendor/pagination/bootstrap-4') }}
    @else
    <x-common-no-data />
    @endif
  </div>
</div>
@endsection

@push('footer')
<script>
  const { createApp, ref } = Vue;
    const { ElMessageBox, ElMessage } = ElementPlus;

    const app = createApp({
    setup() {
      const deleteForm = ref(null);

      const open = (index) => {
     ElMessageBox.confirm(
        '{{ __("common/base.hint_delete") }}',
        '{{ __("common/base.cancel") }}',
        {
        confirmButtonText: '{{ __("common/base.confirm")}}',
        cancelButtonText: '{{ __("common/base.cancel")}}',
        type: 'warning',
        }
      )
      .then(() => {
      const deleturl =urls.panel_base+'/announcements/'+index;
      deleteForm.value.action=deleturl;
      deleteForm.value.submit();
      })
      .catch(() => {

      });
      };

      return { open, deleteForm };
    }
    });

    app.use(ElementPlus);
    app.mount('#app');
</script>
@endpush
