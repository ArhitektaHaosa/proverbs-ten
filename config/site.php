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
];
