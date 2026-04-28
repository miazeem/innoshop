@extends('panel::layouts.blank')

@section('title', __('panel/menu.file_manager'))

@prepend('header')
  <script src="{{ asset('vendor/vue/3.5/vue.global' . (config('app.debug') ? '' : '.prod') . '.js') }}"></script>
  <script src="{{ asset('vendor/element-plus/index.full.js') }}"></script>
  <link rel="stylesheet" href="{{ asset('vendor/element-plus/index.css') }}">
  <script src="{{ asset('vendor/element-plus/icons.min.js') }}"></script>
  @if(str_starts_with(panel_locale_code(), 'zh'))
  <script src="{{ asset('vendor/element-plus/zh-cn.js') }}"></script>
  @endif
@endprepend

@prepend('header')
  <meta name="api-token" content="{{ auth()->user()->api_token }}">
  @php($enabledDrivers = $enabled_drivers ?? ['local'])
  <script>
    window.fileManagerConfig = Object.freeze({
      driver: '{{ $config['driver'] }}',
      endpoint: '{{ $config['endpoint'] }}',
      bucket: '{{ $config['bucket'] }}',
      baseUrl: '{{ $config['baseUrl'] }}',
      enabledDrivers: @json($enabledDrivers),
      multiple: {{ $multiple ? 'true' : 'false' }},
      type: '{{ $type }}',
      uploadMaxFileSize: '{{ $uploadMaxFileSize ?? "unknown" }}',
      postMaxSize: '{{ $postMaxSize ?? "unknown" }}'
    });
  </script>
@endprepend

@section('page-bottom-btns')
  <div class="page-bottom-btns" id="bottom-btns">
    <button class="btn btn-primary" @click="handleConfirm">{{ __('panel/file_manager.select_submit') }}</button>
  </div>
@endsection

@push('header')
  <style>
    body {
      display: flex;
      flex-direction: column;
      height: 100vh;
      margin: 0;
      padding: 0;
      overflow: hidden;
    }

    /* 主内容区域 */
    .content-wrapper {
      overflow: hidden;
      position: relative;
    }

    /* 文件管理器内容区域 */
    .file-manager {
      height: 100%;
      display: flex;
      flex-direction: column;
    }

    /* 文件列表区域 */
    .file-list {
      flex: 1;
      overflow-y: auto;
      padding: 20px;
    }

    /* 底部按钮固定在底部 */
    .page-bottom-btns {
      height: 60px;
      padding: 10px;
      background: #fff;
      border-top: 1px solid #EBEEF5;
      display: flex;
      align-items: center;
      justify-content: center;
      position: relative;
      z-index: 10;
    }

    /* 左侧文件夹树 */
    .folder-tree {
      height: 100%;
      border-right: 1px solid #EBEEF5;
      overflow-y: auto;
    }

    /* 工具栏样式 */
    .file-toolbar {
      padding: 15px 20px;
      border-bottom: 1px solid #EBEEF5;
      background: #fff;
      position: relative;
      z-index: 10;
    }
  </style>
@endpush

@push('footer')
  <script>
    // 创建底部按钮的 Vue 实例
    const __btnApp = Vue.createApp({
      methods: {
        handleConfirm() {
          // 获取主 Vue 实例并调用其方法
          const appEl = document.querySelector('#app');
          if (appEl && appEl.__vue_app__) {
            const mainApp = appEl.__vue_app__._instance.proxy;
            if (mainApp && typeof mainApp.confirmSelection === 'function') {
              mainApp.confirmSelection();
            }
          }
        }
      }
    });
    __btnApp.use(ElementPlus, window.ElementPlusLocaleZhCn ? { locale: ElementPlusLocaleZhCn } : {});
    for (const [key, component] of Object.entries(ElementPlusIconsVue)) {
      __btnApp.component(key, component);
    }
    __btnApp.mount('#bottom-btns');

    // 从父窗口获取 token
    window.getApiToken = () => {
      const token = window.parent?.document.querySelector('meta[name="api-token"]')?.getAttribute('content');
      return token;
    };
  </script>
@endpush

<div class="content-wrapper">
  @include('panel::file_manager.main')
</div>
