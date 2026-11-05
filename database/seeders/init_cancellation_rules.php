<?php

require __DIR__ . '/../../vendor/autoload.php';
$app = require_once __DIR__ . '/../../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\CancellationRule;
use App\Models\Setting;

echo "Seeding default cancellation rules...\n";

$tiers = [
    [
        'hours_before_checkin' => 72,
        'refund_percentage' => 100.00,
        'description' => '100% Full Cashback if cancelled 72+ hours prior to check-in (2:00 PM).',
        'sort_order' => 1,
    ],
    [
        'hours_before_checkin' => 48,
        'refund_percentage' => 75.00,
        'description' => '75% Cashback if cancelled between 48 to 72 hours prior to check-in.',
        'sort_order' => 2,
    ],
    [
        'hours_before_checkin' => 24,
        'refund_percentage' => 50.00,
        'description' => '50% Cashback if cancelled between 24 to 48 hours prior to check-in.',
        'sort_order' => 3,
    ],
    [
        'hours_before_checkin' => 0,
        'refund_percentage' => 0.00,
        'description' => 'Non-refundable within 24 hours of arrival or after check-in time.',
        'sort_order' => 4,
    ],
];

foreach ($tiers as $tier) {
    CancellationRule::firstOrCreate(
        [
            'branch_id' => null,
            'hours_before_checkin' => $tier['hours_before_checkin'],
        ],
        [
            'refund_percentage' => $tier['refund_percentage'],
            'description' => $tier['description'],
            'is_active' => true,
            'sort_order' => $tier['sort_order'],
        ]
    );
}

// Seed or update cancellation policy setting
Setting::firstOrCreate(
    ['key' => 'cancellation_policy_description'],
    [
        'group' => 'cancellation',
        'value' => 'At Krishna Resorts, we understand travel plans may shift. We provide a fully automated, tier-based refund guarantee: cancellations made at least 72 hours prior to 2:00 PM on arrival date receive a 100% full refund. Cancellations made 48-72 hours prior receive 75% cashback, and 24-48 hours prior receive 50% cashback. Paybacks are initiated instantaneously to your source payment method upon confirmation.',
        'description' => 'Public resort cancellation and automated cashback policy terms',
    ]
);

echo "Cancellation rules seeded. Count: " . CancellationRule::count() . "\n";
