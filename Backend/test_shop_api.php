<?php

require_once 'vendor/autoload.php';

use App\Models\Shop;
use App\Models\ShopPaymentMethod;

// Test data
$shopData = [
    'name' => 'Quality Bake',
    'address' => 'hmt Road',
    'contact_number' => '9656416829',
    'notes' => 'test',
    'payment_methods' => ['card', 'upi', 'bank_transfer', 'card']
];

echo "Testing Shop Creation with Payment Methods\n";
echo "==========================================\n\n";

try {
    // Create shop
    $shop = Shop::create([
        'name' => $shopData['name'],
        'address' => $shopData['address'],
        'contact_number' => $shopData['contact_number'],
        'notes' => $shopData['notes']
    ]);
    
    echo "✓ Shop created successfully with ID: {$shop->shop_id}\n";
    
    // Add payment methods
    $paymentMethods = array_unique($shopData['payment_methods']); // Remove duplicates
    foreach ($paymentMethods as $method) {
        ShopPaymentMethod::create([
            'shop_id' => $shop->shop_id,
            'payment_method' => $method
        ]);
        echo "✓ Payment method '{$method}' added\n";
    }
    
    // Verify payment methods
    $shop->load('paymentMethods');
    $methods = $shop->getPaymentMethodsByType();
    echo "\n✓ Shop payment methods: " . implode(', ', $methods) . "\n";
    
    // Show shop data
    echo "\nShop Details:\n";
    echo "Name: {$shop->name}\n";
    echo "Address: {$shop->address}\n";
    echo "Contact: {$shop->contact_number}\n";
    echo "Notes: {$shop->notes}\n";
    echo "Payment Methods: " . implode(', ', $methods) . "\n";
    
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
} 