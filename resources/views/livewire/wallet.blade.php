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
                return redirect()->route('create_wallet');
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

    <div class="bg-gray-50 text-black/50">
        <div class="relative min-h-screen flex flex-col items-center justify-center selection:bg-[#FF2D20] selection:text-white">
            <div class="relative w-full max-w-2xl px-6 lg:max-w-7xl">

                <main>
                    <div class="grid gap-6 lg:grid-cols-2 lg:gap-8">
                        <div id="docs-card" class="flex flex-col items-start gap-6 p-6 overflow-hidden rounded-lg bg-white shadow-[0px_14px_34px_0px_rgba(0,0,0,0.08)] ring-1 ring-white/[0.05] transition duration-300 hover:text-black/70 focus:outline-none focus-visible:ring-[#FF2D20] md:row-span-3 lg:p-10 lg:pb-10">
                            <h2 class="text-xl font-semibold text-black">Create Wallet</h2>

                            <form wire:submit.prevent="createReservedAccount" class="w-full">
                                <div class="mb-4">
                                    <x-ts-input label="Email *" type="email" wire:model="customerEmail" class="border bg-gray-100"/>
                                </div>

                                <div class="mb-4">
                                    <x-ts-input label="Fullname *" type="text" wire:model="customerName" class="border p-2"/>
                                </div>

                                <div class="mb-4">
                                    <x-ts-number label="BVN *" type="number" wire:model="bvn" class=" p-2"/>
                                </div>

                                <x-ts-button type="submit" md loading>Create Wallet</x-ts-button>
                            </form>
                        </div>

                        <div class="flex items-start gap-4 rounded-lg bg-white p-6 shadow-[0px_14px_34px_0px_rgba(0,0,0,0.08)] ring-1 ring-white/[0.05] transition duration-300 hover:text-black/70 focus:outline-none focus-visible:ring-[#FF2D20] lg:pb-10">
                            <div class="flex size-12 shrink-0 items-center justify-center rounded-full bg-[#FF2D20]/10 sm:size-16">
                                <svg class="size-5 sm:size-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><g fill="#FF2D20"><path d="M8.75 4.5H5.5c-.69 0-1.25.56-1.25 1.25v4.75c0 .69.56 1.25 1.25 1.25h3.25c.69 0 1.25-.56 1.25-1.25V5.75c0-.69-.56-1.25-1.25-1.25Z"/><path d="M24 10a3 3 0 0 0-3-3h-2V2.5a2 2 0 0 0-2-2H2a2 2 0 0 0-2 2V20a3.5 3.5 0 0 0 3.5 3.5h17A3.5 3.5 0 0 0 24 20V10ZM3.5 21.5A1.5 1.5 0 0 1 2 20V3a.5.5 0 0 1 .5-.5h14a.5.5 0 0 1 .5.5v17c0 .295.037.588.11.874a.5.5 0 0 1-.484.625L3.5 21.5ZM22 20a1.5 1.5 0 1 1-3 0V9.5a.5.5 0 0 1 .5-.5H21a1 1 0 0 1 1 1v10Z"/><path d="M12.751 6.047h2a.75.75 0 0 1 .75.75v.5a.75.75 0 0 1-.75.75h-2A.75.75 0 0 1 12 7.3v-.5a.75.75 0 0 1 .751-.753ZM12.751 10.047h2a.75.75 0 0 1 .75.75v.5a.75.75 0 0 1-.75.75h-2A.75.75 0 0 1 12 11.3v-.5a.75.75 0 0 1 .751-.753ZM4.751 14.047h10a.75.75 0 0 1 .75.75v.5a.75.75 0 0 1-.75.75h-10A.75.75 0 0 1 4 15.3v-.5a.75.75 0 0 1 .751-.753ZM4.75 18.047h7.5a.75.75 0 0 1 .75.75v.5a.75.75 0 0 1-.75.75h-7.5A.75.75 0 0 1 4 19.3v-.5a.75.75 0 0 1 .75-.753Z"/></g></svg>
                            </div>

                            <div class="pt-3 sm:pt-5">
                                <h2 class="text-xl font-semibold text-black">Laravel News</h2>

                                <p class="mt-4 text-sm/relaxed">
                                    Laravel News is a community driven portal and newsletter aggregating all of the latest and most important news in the Laravel ecosystem, including new package releases and tutorials.
                                </p>
                            </div>

                            <svg class="size-6 shrink-0 self-center stroke-[#FF2D20]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12h15m0 0l-6.75-6.75M19.5 12l-6.75 6.75"/></svg>
                        </div>

                       
                    </div>
                </main>
            </div>
        </div>
    </div>
</div>
