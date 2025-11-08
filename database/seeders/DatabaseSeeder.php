<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Document;
use App\Models\Transaction;
use App\Models\Purchase;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create admin user
        User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'is_admin' => true,
            'password' => Hash::make('password123'),
        ]);

        // Create regular users
        User::factory(5)->create();

        // Create sample documents
        $this->createSampleDocuments();

        // Create sample transactions and purchases
        $this->createSampleTransactions();
    }

    private function createSampleDocuments(): void
    {
        $documentTypes = ['Registry', 'Khasra', 'Khatauni', 'Jamabandi', 'Girdawari'];
        $districts = ['Jaipur', 'Jodhpur', 'Udaipur', 'Kota', 'Bikaner', 'Ajmer'];
        $anchals = ['Anchal 1', 'Anchal 2', 'Anchal 3', 'Anchal 4', 'Anchal 5'];
        $mauzas = ['Mauza A', 'Mauza B', 'Mauza C', 'Mauza D', 'Mauza E', 'Mauza F'];
        $thanaNumbers = ['101', '102', '103', '104', '105', '106'];

        foreach (range(1, 20) as $index) {
            Document::create([
                'title' => 'Document ' . $index,
                'document_type' => $documentTypes[array_rand($documentTypes)],
                'district' => $districts[array_rand($districts)],
                'anchal' => $anchals[array_rand($anchals)],
                'mauza' => $mauzas[array_rand($mauzas)],
                'thana_no' => $thanaNumbers[array_rand($thanaNumbers)],
                'file_path' => 'documents/sample_' . $index . '.pdf',
                'price' => rand(50, 500),
                'is_active' => true,
            ]);
        }
    }

    private function createSampleTransactions(): void
    {
        $users = User::where('is_admin', false)->get();
        $documents = Document::where('is_active', true)->get();

        foreach (range(1, 10) as $index) {
            $user = $users->random();
            $document = $documents->random();

            $transaction = Transaction::create([
                'user_id' => $user->id,
                'document_id' => $document->id,
                'amount' => $document->price,
                'status' => ['pending', 'completed', 'failed'][array_rand(['pending', 'completed', 'failed'])],
                'gateway' => 'razorpay',
                'gateway_order_id' => 'ORDER' . str_pad($index, 8, '0', STR_PAD_LEFT),
                'gateway_payment_id' => $index % 2 == 0 ? 'PAY' . str_pad($index, 8, '0', STR_PAD_LEFT) : null,
                'meta' => json_encode(['notes' => 'Sample transaction']),
            ]);

            // Create purchase record for completed transactions
            if ($transaction->status === 'completed') {
                Purchase::create([
                    'user_id' => $user->id,
                    'document_id' => $document->id,
                    'transaction_id' => $transaction->id,
                    'downloaded_at' => $index % 3 == 0 ? now() : null,
                ]);
            }
        }
    }
}
