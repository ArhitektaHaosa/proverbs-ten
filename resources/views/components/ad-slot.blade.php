@props(['name'])

@if(config('site.adsense_enabled') && config('site.adsense_client'))
    {{-- Real AdSense slot. Never render on noindex, empty or error pages. --}}
    <div class="ad-slot" data-slot="{{ $name }}"></div>
@endif
