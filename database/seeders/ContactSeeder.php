<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Contact;
use App\Models\ContactTag;
use App\Models\User;

class ContactSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get admin user for assignment
        $admin = User::where('email', 'admin@labelsalesagents.com')->first();

        // Create tags
        $tags = [
            ['name' => 'VIP', 'color' => '#dc2626'],
            ['name' => 'Hot Lead', 'color' => '#f59e0b'],
            ['name' => 'First Time Buyer', 'color' => '#3b82f6'],
            ['name' => 'Investor', 'color' => '#8b5cf6'],
            ['name' => 'Luxury', 'color' => '#ec4899'],
            ['name' => 'Commercial', 'color' => '#10b981'],
            ['name' => 'Referral', 'color' => '#06b6d4'],
        ];

        $createdTags = [];
        foreach ($tags as $tag) {
            $createdTags[] = ContactTag::create($tag);
        }

        // Create sample contacts
        $contacts = [
            [
                'first_name' => 'John',
                'last_name' => 'Smith',
                'email' => 'john.smith@example.com',
                'phone' => '(555) 123-4567',
                'address' => '123 Main Street',
                'city' => 'Los Angeles',
                'state' => 'CA',
                'zip' => '90001',
                'contact_type' => 'buyer',
                'status' => 'lead',
                'source' => 'Website',
                'priority' => 'high',
                'assigned_to' => $admin->id,
                'last_contact_date' => now()->subDays(1),
            ],
            [
                'first_name' => 'Sarah',
                'last_name' => 'Johnson',
                'email' => 'sarah.j@example.com',
                'phone' => '(555) 234-5678',
                'address' => '456 Oak Avenue',
                'city' => 'Beverly Hills',
                'state' => 'CA',
                'zip' => '90210',
                'contact_type' => 'seller',
                'status' => 'contacted',
                'source' => 'Referral',
                'priority' => 'high',
                'assigned_to' => $admin->id,
                'last_contact_date' => now()->subHours(6),
            ],
            [
                'first_name' => 'Michael',
                'last_name' => 'Brown',
                'email' => 'mbrown@example.com',
                'phone' => '(555) 345-6789',
                'address' => '789 Pine Road',
                'city' => 'Santa Monica',
                'state' => 'CA',
                'zip' => '90401',
                'contact_type' => 'investor',
                'status' => 'qualified',
                'source' => 'Open House',
                'priority' => 'medium',
                'assigned_to' => $admin->id,
                'last_contact_date' => now()->subDays(3),
            ],
            [
                'first_name' => 'Emily',
                'last_name' => 'Davis',
                'email' => 'emily.davis@example.com',
                'phone' => '(555) 456-7890',
                'address' => '321 Elm Street',
                'city' => 'Pasadena',
                'state' => 'CA',
                'zip' => '91101',
                'contact_type' => 'buyer',
                'status' => 'showing',
                'source' => 'Facebook Ads',
                'priority' => 'high',
                'assigned_to' => $admin->id,
                'last_contact_date' => now()->subHours(12),
            ],
            [
                'first_name' => 'David',
                'last_name' => 'Wilson',
                'email' => 'dwilson@example.com',
                'phone' => '(555) 567-8901',
                'address' => '654 Maple Drive',
                'city' => 'Long Beach',
                'state' => 'CA',
                'zip' => '90802',
                'contact_type' => 'seller',
                'status' => 'offer',
                'source' => 'Cold Call',
                'priority' => 'medium',
                'assigned_to' => $admin->id,
                'last_contact_date' => now()->subDays(2),
            ],
            [
                'first_name' => 'Jennifer',
                'last_name' => 'Martinez',
                'email' => 'jmartinez@example.com',
                'phone' => '(555) 678-9012',
                'address' => '987 Cedar Lane',
                'city' => 'Glendale',
                'state' => 'CA',
                'zip' => '91201',
                'contact_type' => 'buyer',
                'status' => 'contract',
                'source' => 'Zillow',
                'priority' => 'high',
                'assigned_to' => $admin->id,
                'last_contact_date' => now()->subHours(4),
            ],
            [
                'first_name' => 'Robert',
                'last_name' => 'Anderson',
                'email' => 'randerson@example.com',
                'phone' => '(555) 789-0123',
                'address' => '147 Birch Court',
                'city' => 'Burbank',
                'state' => 'CA',
                'zip' => '91502',
                'contact_type' => 'investor',
                'status' => 'closed',
                'source' => 'LinkedIn',
                'priority' => 'low',
                'assigned_to' => $admin->id,
                'last_contact_date' => now()->subWeeks(2),
            ],
            [
                'first_name' => 'Lisa',
                'last_name' => 'Taylor',
                'email' => 'ltaylor@example.com',
                'phone' => '(555) 890-1234',
                'address' => '258 Spruce Street',
                'city' => 'Torrance',
                'state' => 'CA',
                'zip' => '90501',
                'contact_type' => 'buyer',
                'status' => 'lead',
                'source' => 'Google Search',
                'priority' => 'medium',
                'assigned_to' => $admin->id,
                'last_contact_date' => now()->subDays(5),
            ],
            [
                'first_name' => 'James',
                'last_name' => 'Thomas',
                'email' => 'jthomas@example.com',
                'phone' => '(555) 901-2345',
                'address' => '369 Willow Way',
                'city' => 'Anaheim',
                'state' => 'CA',
                'zip' => '92801',
                'contact_type' => 'seller',
                'status' => 'prospect',
                'source' => 'Instagram',
                'priority' => 'low',
                'assigned_to' => $admin->id,
                'last_contact_date' => now()->subDays(7),
            ],
            [
                'first_name' => 'Patricia',
                'last_name' => 'White',
                'email' => 'pwhite@example.com',
                'phone' => '(555) 012-3456',
                'address' => '741 Ash Boulevard',
                'city' => 'Irvine',
                'state' => 'CA',
                'zip' => '92602',
                'contact_type' => 'buyer',
                'status' => 'lost',
                'source' => 'Email Campaign',
                'priority' => 'low',
                'assigned_to' => $admin->id,
                'last_contact_date' => now()->subMonths(1),
            ],
        ];

        foreach ($contacts as $index => $contactData) {
            $contact = Contact::create($contactData);

            // Attach random tags
            $randomTags = $createdTags;
            shuffle($randomTags);
            $tagsToAttach = array_slice($randomTags, 0, rand(1, 3));
            $contact->tags()->attach(array_map(fn($tag) => $tag->id, $tagsToAttach));

            // Add sample notes
            $contact->notes()->create([
                'user_id' => $admin->id,
                'content' => 'Initial contact made. Very interested in ' . ($contactData['contact_type'] === 'buyer' ? 'buying' : 'selling') . ' property.',
                'is_pinned' => $index % 3 === 0, // Pin every 3rd note
            ]);

            if ($index % 2 === 0) {
                $contact->notes()->create([
                    'user_id' => $admin->id,
                    'content' => 'Follow-up scheduled for next week. Looking for properties in the $500k-$750k range.',
                    'is_pinned' => false,
                ]);
            }
        }
    }
}
