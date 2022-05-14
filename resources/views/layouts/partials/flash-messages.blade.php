@if (session('success'))
    <x-alert-flash :message="session('success')" type="success" dismissible="true" />
@endif

@if (session('error'))
    <x-alert-flash :message="session('error')" type="danger" dismissible="true" />
@endif

@if (session('warning'))
    <x-alert-flash :message="session('warning')" type="warning" dismissible="true" />
@endif

@if (session('info'))
    <x-alert-flash :message="session('info')" type="info" dismissible="true" />
@endif
