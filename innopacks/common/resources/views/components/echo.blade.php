@if(config('broadcasting.default') === 'reverb')
<script src="{{ asset('vendor/pusher-js/pusher.min.js') }}"></script>
<script src="{{ asset('vendor/laravel-echo/echo.iife.js') }}"></script>
<script>
  (function() {
    try {
      var echoConfig = {
        broadcaster: 'reverb',
        key: '{{ config('broadcasting.connections.reverb.key') }}',
        wsHost: '{{ config('broadcasting.connections.reverb.options.host', 'localhost') }}',
        forceTLS: {{ config('broadcasting.connections.reverb.options.scheme') === 'https' ? 'true' : 'false' }},
        enabledTransports: ['ws', 'wss'],
        disabledTransports: ['sockjs'],
      };

      @php $frontendPort = env('REVERB_FRONTEND_PORT', env('REVERB_PORT')); @endphp
      @if($frontendPort)
      echoConfig.wsPort = {{ $frontendPort }};
      echoConfig.wssPort = {{ $frontendPort }};
      @endif

      window.Echo = new Echo.default(echoConfig);
    } catch (e) {
      console.warn('[Reverb] Echo initialization failed:', e.message);
    }
  })();
</script>
@endif
