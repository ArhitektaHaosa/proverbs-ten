@if(config('site.cmp_provider') && config('site.cmp_id'))
    {{-- Official Quantcast Choice or Google snippet goes here. Do not hand-roll TCF. --}}
    <script>
        // CMP_PROVIDER={{ config('site.cmp_provider') }}
        // CMP_ID loaded from config only.
    </script>
@endif
