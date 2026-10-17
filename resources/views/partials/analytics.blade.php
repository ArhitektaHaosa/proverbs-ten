@if(config('site.analytics_enabled') && config('site.ga4_id') && (config('site.cmp_id') || config('site.analytics_allow_without_cmp')))
    {{-- GA4 with Consent Mode v2. Consent denied by default for EEA/UK. --}}
    <script async src="https://www.googletagmanager.com/gtag/js?id={{ config('site.ga4_id') }}"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('consent', 'default', {
            'ad_storage': 'denied',
            'analytics_storage': 'denied',
            'wait_for_update': 500
        });
        gtag('js', new Date());
        gtag('config', '{{ config('site.ga4_id') }}');
    </script>
@endif
