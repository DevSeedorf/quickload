<?php

use Livewire\Volt\Component;
use Illuminate\Support\Facades\Http;
use App\Models\ReservedAccount;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use TallStackUi\Traits\Interactions;


new class extends Component {
    use Interactions;
    
    public $accessToken;
    public $reservedAccount;

    public $accountName = "Quickload Wallet";
    public $currencyCode = "NGN";
    public $contractCode = "0095184541";
    public $customerEmail;
    public $customerName;
    public $bvn;
    public $getAllAvailableBanks = true;


    public function mount()
    {
        $apiKey = 'MK_TEST_HZC39DHVH3';
        $secretKey = 'SF7S597VHA05ZFFNNSVH69C2TRECJEUL';
        $url = 'https://sandbox.monnify.com/api/v1/auth/login';

        // Base64 encode the API Key and Secret Key
        $authHeader = base64_encode("$apiKey:$secretKey");

        $response = Http::withHeaders([
            'Authorization' => "Basic $authHeader",
            'Content-Type' => 'application/json',
        ])->post($url);

        if ($response->successful()) {
            $data = $response->json();
            $token = $data['responseBody']['accessToken'];
            session(['monnify_access_token' => $token]);
            $this->accessToken = 'Access Token: ' . $token;
        } else {
            $this->accessToken = 'Error: ' . $response->status() . ' - ' . $response->body();
        }
    }

    public function createReservedAccount()
    {
        $url = 'https://sandbox.monnify.com/api/v2/bank-transfer/reserved-accounts';

        // Retrieve the access token from the session
        $accessToken = session('monnify_access_token');

        if (!$accessToken) {
            $this->accessToken = 'Error: Access token not found. Please authenticate first.';
            return;
        }
        // Generate a random account reference with "QL" as the prefix
        $accountReference = 'QL' . strtoupper(Str::random(8));
        // Request payload
        $payload = [
            "accountReference" => $accountReference,
            "accountName" => $this->accountName,
            "currencyCode" => $this->currencyCode,
            "contractCode" => $this->contractCode,
            "customerEmail" => $this->customerEmail,
            "customerName" => $this->customerName,
            "bvn" => $this->bvn,
            "getAllAvailableBanks" => $this->getAllAvailableBanks,
        ];
        // Make the HTTP request
        try {
        
            $response = Http::withToken($accessToken)
                ->withHeaders(['Content-Type' => 'application/json'])
                ->post($url, $payload);
                
                if ($response->json()['responseMessage'] === 'success') {

                $data = $response->json()['responseBody'];
                // Loop through the accounts array and save each account to the database
                foreach ($data['accounts'] as $account) {
                    ReservedAccount::create([
                        'user_id' => Auth::user()->id,
                        'contract_code' => $data['contractCode'],
                        'account_reference' => $data['accountReference'],
                        'account_name' => $data['accountName'],
                        'currency_code' => $data['currencyCode'],
                        'customer_email' => $data['customerEmail'],
                        'customer_name' => $data['customerName'],
                        'bank_code' => $account['bankCode'],
                        'bank_name' => $account['bankName'],
                        'account_number' => $account['accountNumber'],
                        'reservation_reference' => $data['reservationReference'],
                        'reserved_account_type' => $data['reservedAccountType'],
                        'status' => $data['status'],
                    ]);
                }
                $this->toast()->success('Success', 'Wallet Created Successfully!')->flash()->send();
                return redirect()->route('dashboard');
            } else {
                $this->toast()->error('Error', ''. $response->json()['responseMessage'])->send();
                
            }
        } catch (\Exception $e) {
                session()->flash('error', 'Error: ' . $e->getMessage());
                return;
        }
    }
    
}; 

?>

<div>
    <x-ts-toast />
        <main class="p-4">
            <form wire:submit.prevent="createReservedAccount" class="w-full">
                <div class="mb-4">
                    <x-ts-input label="Email *" type="email" wire:model="customerEmail" class="border bg-gray-100"/>
                </div>

                <div class="mb-4">
                    <x-ts-input label="Fullname *" type="text" wire:model="customerName" class="border p-2"/>
                </div>

                <div class="mb-4">
                    <x-ts-input label="BVN *" wire:model="bvn" class=" p-2"/>
                </div>

                <x-ts-button type="submit" md loading>Create Wallet</x-ts-button>
            </form>
        </main>
</div>
