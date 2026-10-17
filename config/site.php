<?php

return [
    'name' => env('SITE_NAME', 'Proverbs'),
    'tagline' => env('SITE_TAGLINE', ''),
    'url' => env('APP_URL'),
    'contact_email' => env('CONTACT_EMAIL'),
    'indexable' => (bool) env('SITE_INDEXABLE', false),
    'seo_min_enrichment_score' => (int) env('SEO_MIN_ENRICHMENT_SCORE', 3),
    'trust_after_approved' => (int) env('TRUST_AFTER_APPROVED', 5),
    'admin_path' => env('ADMIN_PATH', 'admin'),
    'adsense_enabled' => (bool) env('ADSENSE_ENABLED', false),
    'adsense_client' => env('ADSENSE_CLIENT'),
    'analytics_enabled' => (bool) env('ANALYTICS_ENABLED', true),
    'ga4_id' => env('GA4_MEASUREMENT_ID'),
    'cmp_provider' => env('CMP_PROVIDER'),
    'cmp_id' => env('CMP_ID'),
    'analytics_allow_without_cmp' => (bool) env('ANALYTICS_ALLOW_WITHOUT_CMP', false),
];
