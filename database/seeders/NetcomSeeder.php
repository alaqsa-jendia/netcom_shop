<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\Package;
use App\Models\PaymentMethod;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class NetcomSeeder extends Seeder
{
    public function run(): void
    {
        Admin::create([
            'username' => 'ajendia',
            'password' => Hash::make('iycD@ycdi'),
            'name' => 'مدير النظام',
            'role' => 'super_admin',
            'is_active' => true,
        ]);

        Package::create([
            'name' => 'باقة Basic',
            'price' => 10.00,
            'quantity' => 1,
            'icon' => 'fa fa-box',
            'is_active' => true,
        ]);

        Package::create([
            'name' => 'باقة Standard',
            'price' => 25.00,
            'quantity' => 3,
            'icon' => 'fa fa-boxes',
            'is_active' => true,
        ]);

        Package::create([
            'name' => 'باقة Premium',
            'price' => 50.00,
            'quantity' => 7,
            'icon' => 'fa fa-gem',
            'is_active' => true,
        ]);

        PaymentMethod::create([
            'name' => 'بال snatched',
            'account_name' => 'احمد محمد',
            'account_number' => '0591234567',
            'is_active' => true,
            'order' => 1,
        ]);

        PaymentMethod::create([
            'name' => 'محفظة PalPay',
            'account_name' => 'احمد محمد',
            'account_number' => '0561234567',
            'is_active' => true,
            'order' => 2,
        ]);
    }
}
