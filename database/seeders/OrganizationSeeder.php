<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Organization;
use App\Models\User;
use App\Models\Account;
use App\Models\Beneficiary;
use Illuminate\Support\Str;

class OrganizationSeeder extends Seeder
{
    public function run(): void
    {
        // Create a demo organization
        $organization = Organization::create([
            'name' => 'Demo Company',
            'slug' => 'demo-company',
            'email' => 'contact@democompany.com',
            'phone' => '+1234567890',
            'address' => '123 Business Street, City, Country',
            'default_currency' => 'USD',
            'on_trial' => true,
            'trial_ends_at' => now()->addDays(30),
            'subscription_active' => false,
            'settings' => [
                'timezone' => 'UTC',
                'date_format' => 'Y-m-d',
                'decimal_places' => 2,
            ],
        ]);

        // Create manager user
        $manager = User::create([
            'name' => 'John Manager',
            'email' => 'manager@democompany.com',
            'password' => bcrypt('password'),
            'organization_id' => $organization->id,
            'role' => 'manager',
            'is_active' => true,
        ]);

        // Create cashier user
        $cashier = User::create([
            'name' => 'Jane Cashier',
            'email' => 'cashier@democompany.com',
            'password' => bcrypt('password'),
            'organization_id' => $organization->id,
            'role' => 'cashier',
            'is_active' => true,
        ]);

        // Create accounts
        Account::create([
            'organization_id' => $organization->id,
            'name' => 'Main Cash Account',
            'type' => 'cash',
            'currency' => 'USD',
            'balance' => 5000.00,
            'description' => 'Primary cash account for daily operations',
            'is_active' => true,
        ]);

        Account::create([
            'organization_id' => $organization->id,
            'name' => 'Business Bank Account',
            'type' => 'bank',
            'currency' => 'USD',
            'balance' => 25000.00,
            'account_number' => '1234567890',
            'description' => 'Main business bank account',
            'is_active' => true,
        ]);

        Account::create([
            'organization_id' => $organization->id,
            'name' => 'Mobile Money Account',
            'type' => 'mobile_money',
            'currency' => 'USD',
            'balance' => 1500.00,
            'account_number' => '+1234567890',
            'description' => 'Mobile money account for quick transactions',
            'is_active' => true,
        ]);

        // Create beneficiaries
        Beneficiary::create([
            'organization_id' => $organization->id,
            'name' => 'Office Supplies Ltd',
            'type' => 'company',
            'email' => 'sales@officesupplies.com',
            'phone' => '+1234567891',
            'address' => '456 Supply Street, City, Country',
            'tax_number' => 'TAX123456789',
            'bank_details' => [
                'bank_name' => 'Business Bank',
                'account_number' => '9876543210',
                'routing_number' => '123456789',
            ],
            'is_active' => true,
        ]);

        Beneficiary::create([
            'organization_id' => $organization->id,
            'name' => 'John Doe',
            'type' => 'individual',
            'email' => 'john.doe@email.com',
            'phone' => '+1234567892',
            'address' => '789 Individual Street, City, Country',
            'is_active' => true,
        ]);

        Beneficiary::create([
            'organization_id' => $organization->id,
            'name' => 'Tech Services Inc',
            'type' => 'company',
            'email' => 'billing@techservices.com',
            'phone' => '+1234567893',
            'address' => '321 Tech Avenue, City, Country',
            'tax_number' => 'TAX987654321',
            'bank_details' => [
                'bank_name' => 'Tech Bank',
                'account_number' => '1122334455',
                'routing_number' => '987654321',
            ],
            'is_active' => true,
        ]);
    }
}
