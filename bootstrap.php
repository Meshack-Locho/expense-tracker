<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['audit_logs'])) {
    $_SESSION['audit_logs'] = [];
}

// -------------------------
// Pricing Tiers
// -------------------------
$pricingTiers = [
    1 => ["name"=>"Gold","discount"=>10],
    2 => ["name"=>"Silver","discount"=>5],
    3 => ["name"=>"Bronze","discount"=>0],
];

// -------------------------
// Products
// -------------------------
// $products = [
//     ["id"=>"KV-001","name"=>"Fanta Orange 500ml","unit"=>"bottle","price"=>80,"warehouse"=>"Nairobi","tier_id"=>1],
//     ["id"=>"KV-002","name"=>"Coca-Cola 1.5L","unit"=>"bottle","price"=>150,"warehouse"=>"Mombasa","tier_id"=>2],
//     ["id"=>"KV-003","name"=>"Afya Mineral Water 500ml","unit"=>"bottle","price"=>50,"warehouse"=>"Nairobi","tier_id"=>1],
//     ["id"=>"KV-004","name"=>"MT Kenya Water 1L","unit"=>"bottle","price"=>70,"warehouse"=>"Nairobi","tier_id"=>1],
// ];

$vat = 16;

$customers = [
    ["id" => 1, "name"=>"Pick n Peel", "pricing_tier"=> 1, "warehouse" => 'Nairobi'],
    ["id" => 2, "name"=>"Afya Kenya", "pricing_tier"=> 2, "warehouse" => 'Mombasa'],
];


if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
    $_SESSION['total'] = 0;
}

$invoices = [
    [
        "invoice_no" => "INV-2026-001",
        "order_ref" => "ORD-002",
        "customer_id" => 1,
        "invoice_date" => "2026-02-16",
        "due_date" => "2026-03-16",
        "status" => "Unpaid",
        "total" => 78000,
        "balance" => 78000,
        "items" => [
            [
                "sku" => "KV-001",
                "name" => "Fanta Orange 500ml",
                "unit" => "bottle",
                "quantity" => 300,
                "unit_price" => 72,
                "line_total" => 21600
            ],
            [
                "sku" => "KV-003",
                "name" => "Afya Mineral Water 500ml",
                "unit" => "bottle",
                "quantity" => 500,
                "unit_price" => 45,
                "line_total" => 22500
            ]
        ]
    ],
    [
        "invoice_no" => "INV-2026-002",
        "order_ref" => "ORD-003",
        "customer_id" => 2,
        "invoice_date" => "2026-02-10",
        "due_date" => "2026-03-10",
        "status" => "Paid",
        "total" => 108000,
        "balance" => 0,
        "items" => [
            [
                "sku" => "KV-002",
                "name" => "Coca-Cola 1.5L",
                "unit" => "bottle",
                "quantity" => 600,
                "unit_price" => 135,
                "line_total" => 81000
            ]
        ]
    ]
];

$creditNotes = [
    [
        "credit_no" => "CN-2026-001",
        "invoice_ref" => "INV-2026-001",
        "customer_id" => 1,
        "issue_date" => "2026-02-20",
        "reason" => "Damaged goods return",
        "status" => "Approved",
        "total" => 5000,
        "tax" => 800,
        "applied_amount" => 2000,
        "balance" => 3800,
        "items" => [
            [
                "sku" => "KV-001",
                "name" => "Fanta Orange 500ml",
                "unit" => "bottle",
                "quantity" => 50,
                "unit_price" => 72,
                "line_total" => 3600
            ]
        ]
    ]
];