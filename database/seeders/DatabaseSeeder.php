<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\Laboratory;
use App\Models\User;
use App\Models\Category;
use App\Models\Item;
use Faker\Factory as Faker;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Writer\PngWriter;
use Illuminate\Support\Facades\Storage;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $faker = Faker::create();

        // Seed Laboratories
        $laboratories = [
            [
                'name' => 'Biology Research Lab',
                'description' => 'A state-of-the-art facility for biological research and experiments.',
            ],
            [
                'name' => 'Chemistry Analysis Lab',
                'description' => 'Equipped for advanced chemical analysis and synthesis.',
            ],
            [
                'name' => 'Physics Experimental Lab',
                'description' => 'Dedicated to cutting-edge physics experiments and research.',
            ],
        ];

        foreach ($laboratories as $lab) {
            Laboratory::create($lab);
        }

        // Seed Admin User
        User::create([
            'first_name' => 'System',
            'last_name' => 'Administrator',
            'contact_no' => '09123456789',
            'email' => 'admin@university.edu',
            'username' => 'sysadmin',
            'password' => Hash::make('password'),
            'user_role' => 'Admin',
            'status' => true,
            'remember_token' => Str::random(10),
        ]);

        // Seed Laboratory Heads and Incharges
        $labs = Laboratory::all();
        $contact_counter = 9123456780;

        foreach ($labs as $index => $lab) {
            // Laboratory Head
            User::create([
                'first_name' => $faker->firstName,
                'middle_name' => $faker->randomElement([$faker->lastName, null]),
                'last_name' => $faker->lastName,
                'extension_name' => $faker->randomElement([null, 'Jr', 'Sr', 'II', 'III']),
                'contact_no' => '09' . ($contact_counter++),
                'email' => 'head' . ($index + 1) . '@university.edu',
                'username' => 'labhead' . ($index + 1),
                'password' => Hash::make('password'),
                'user_role' => 'Laboratory Head',
                'laboratory_id' => $lab->id,
                'status' => true,
                'remember_token' => Str::random(10),
            ]);

            // Laboratory Incharge
            User::create([
                'first_name' => $faker->firstName,
                'middle_name' => $faker->randomElement([$faker->lastName, null]),
                'last_name' => $faker->lastName,
                'extension_name' => $faker->randomElement([null, 'Jr', 'Sr', 'II', 'III']),
                'contact_no' => '09' . ($contact_counter++),
                'email' => 'incharge' . ($index + 1) . '@university.edu',
                'username' => 'incharge' . ($index + 1),
                'password' => Hash::make('password'),
                'user_role' => 'Laboratory In-charge',
                'laboratory_id' => $lab->id,
                'status' => true,
                'remember_token' => Str::random(10),
            ]);
        }

        // Seed Employees
        for ($i = 1; $i <= 2; $i++) {
            User::create([
                'first_name' => $faker->firstName,
                'middle_name' => $faker->randomElement([$faker->lastName, null]),
                'last_name' => $faker->lastName,
                'extension_name' => $faker->randomElement([null, 'Jr', 'Sr', 'II', 'III']),
                'contact_no' => '09' . ($contact_counter++),
                'email' => 'employee' . $i . '@university.edu',
                'username' => 'employee' . $i,
                'password' => Hash::make('password'),
                'user_role' => 'Employee',
                'status' => true,
                'remember_token' => Str::random(10),
            ]);
        }

        // Seed Borrowers
        for ($i = 1; $i <= 2; $i++) {
            User::create([
                'first_name' => $faker->firstName,
                'middle_name' => $faker->randomElement([$faker->lastName, null]),
                'last_name' => $faker->lastName,
                'extension_name' => $faker->randomElement([null, 'Jr', 'Sr', 'II', 'III']),
                'contact_no' => '09' . ($contact_counter++),
                'email' => 'borrower' . $i . '@university.edu',
                'username' => 'borrower' . $i,
                'password' => Hash::make('password'),
                'user_role' => 'Borrower',
                'status' => true,
                'remember_token' => Str::random(10),
            ]);
        }

        // Seed Categories
        $categories = [
            ['category_name' => 'Microscopes', 'category_type' => 'Equipment', 'laboratory_id' => 1],
            ['category_name' => 'Glassware', 'category_type' => 'Apparatus', 'laboratory_id' => 1],
            ['category_name' => 'Chemicals', 'category_type' => 'Apparatus', 'laboratory_id' => 2],
            ['category_name' => 'Measurement Tools', 'category_type' => 'Tools', 'laboratory_id' => 2],
            ['category_name' => 'Physics Apparatus', 'category_type' => 'Apparatus', 'laboratory_id' => 3],
            ['category_name' => 'Testing Equipment', 'category_type' => 'Equipment', 'laboratory_id' => 3],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }

        // Seed Items
        $items = [
            [
                'item_name' => 'Compound Microscope',
                'item_description' => 'High-resolution compound microscope for biological studies.',
                'item_price' => 1500.00,
                'category_id' => 1,
                'beginning_qty' => 10,
                'current_qty' => 10,
            ],
            [
                'item_name' => 'Beaker 500ml',
                'item_description' => 'Borosilicate glass beaker for laboratory use.',
                'item_price' => 25.00,
                'category_id' => 2,
                'beginning_qty' => 50,
                'current_qty' => 50,
            ],
            [
                'item_name' => 'Hydrochloric Acid',
                'item_description' => 'Concentrated HCl for chemical experiments.',
                'item_price' => 45.00,
                'category_id' => 3,
                'beginning_qty' => 20,
                'current_qty' => 20,
            ],
            [
                'item_name' => 'Digital Caliper',
                'item_description' => 'Precision measurement tool for small objects.',
                'item_price' => 80.00,
                'category_id' => 4,
                'beginning_qty' => 15,
                'current_qty' => 15,
            ],
            [
                'item_name' => 'Pendulum Apparatus',
                'item_description' => 'Apparatus for studying oscillatory motion.',
                'item_price' => 120.00,
                'category_id' => 5,
                'beginning_qty' => 8,
                'current_qty' => 8,
            ],
            [
                'item_name' => 'Oscilloscope',
                'item_description' => 'Digital oscilloscope for signal analysis.',
                'item_price' => 2000.00,
                'category_id' => 6,
                'beginning_qty' => 5,
                'current_qty' => 5,
            ],
        ];

        foreach ($items as $itemData) {
            $item = Item::create($itemData);

            // Prepare QR code content
            $qrCodeContent = json_encode([
                'item_id' => $item->id,
                'item_name' => $item->item_name,
            ]);

            // Generate QR code using Endroid Builder and GD renderer (PNG)
            $result = Builder::create()
                ->writer(new PngWriter()) // Use GD-based PNG writer
                ->data($qrCodeContent)
                ->size(200)
                ->margin(10)
                ->build();

            // Define temp path
            $tempPath = 'qrcodes/item_' . $item->id . '.png';

            // Store QR code image to disk
            Storage::disk('public')->put($tempPath, $result->getString());

            // Add to media library
            $item->addMedia(Storage::disk('public')->path($tempPath))
                ->toMediaCollection('qrcode');
        }

        // Seed Transactions
        // $borrowers = User::where('user_role', 'Borrower')->get();
        // $items_all = Item::all();

        // for ($i = 1; $i <= 5; $i++) {
        //     $item = $items_all->random();
        //     $borrower = $borrowers->random();

        //     $transaction = Transaction::create([
        //         'transaction_no' => 'TRN' . str_pad($i, 6, '0', STR_PAD_LEFT),
        //         'item_id' => $item->id,
        //         'user_id' => $borrower->id,
        //         'reserve_quantity' => $faker->numberBetween(1, 3),
        //         'approve_quantity' => $faker->numberBetween(1, 3),
        //         'date_of_usage' => $faker->dateTimeBetween('-1 month', 'now')->format('Y-m-d'),
        //         'date_of_return' => $faker->dateTimeBetween('now', '+1 month')->format('Y-m-d'),
        //         'time_of_return' => $faker->time(),
        //         'status' => $faker->randomElement(['Pending', 'Confirmed', 'Released', 'Returned', 'Rejected', 'Cancelled']),
        //     ]);

        //     // Seed Transaction Statuses
        //     TransactionStatus::create([
        //         'transaction_id' => $transaction->id,
        //         'item_id' => $item->id,
        //         'quantity' => $transaction->approve_quantity,
        //         'status' => $faker->randomElement(['Good', 'Lost', 'Damaged', 'For Repair', 'For Disposal']),
        //     ]);

        //     // Seed Transaction Penalties (for some transactions)
        //     if ($faker->boolean(30)) { // 30% chance of penalty
        //         TransactionPenalty::create([
        //             'transaction_id' => $transaction->id,
        //             'item_id' => $item->id,
        //             'user_id' => $borrower->id,
        //             'quantity' => $faker->numberBetween(1, $transaction->approve_quantity),
        //             'amount' => $faker->randomFloat(2, 10, 100),
        //             'status' => $faker->randomElement(['Lost', 'Damaged']),
        //             'remarks' => $faker->randomElement(['Replace', 'Pay']),
        //         ]);
        //     }
        // }
    }
}
