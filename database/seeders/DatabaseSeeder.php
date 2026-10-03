<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Workshop;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     * Document 3 Section 2.1: First workshop and Owner created once by seeder.
     */
    public function run(): void
    {
        $workshop = Workshop::firstOrCreate(
            ['name' => 'ورشة الأمل لصناديق السيارات'],
            [
                'owner_phone' => '01012345678',
                'subscription_tier' => 'standard',
            ]
        );

        $owner = User::firstOrCreate(
            ['email' => 'owner@workshop.com'],
            [
                'workshop_id' => $workshop->id,
                'name' => 'صاحب الورشة (المدير)',
                'phone' => '01012345678',
                'password' => Hash::make('password123'),
                'role' => 'owner',
                'is_active' => true,
            ]
        );

        $frontDesk = User::firstOrCreate(
            ['email' => 'frontdesk@workshop.com'],
            [
                'workshop_id' => $workshop->id,
                'name' => 'موظف الاستقبال',
                'phone' => '01098765432',
                'password' => Hash::make('password123'),
                'role' => 'front_desk',
                'is_active' => true,
            ]
        );

        $technician = User::firstOrCreate(
            ['email' => 'technician@workshop.com'],
            [
                'workshop_id' => $workshop->id,
                'name' => 'الفني أحمد',
                'phone' => '01122334455',
                'password' => Hash::make('password123'),
                'role' => 'technician',
                'is_active' => true,
            ]
        );
    }
}
