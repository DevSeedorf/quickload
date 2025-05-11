<?php

use Livewire\Volt\Component;
use Illuminate\Support\Facades\Http;
use TallStackUi\Traits\Interactions; 


new class extends Component {
	use Interactions;
	
    public $dataType;
    public $dataPlan;
    public $phoneNumber;
    public $apiData = [];
    public $token;

    protected $rules = [
        'dataType' => 'required',
        'dataPlan' => 'required',
        'phoneNumber' => 'required|numeric|digits_between:11,15',
    ];

    public function mount()
    {
        try {
            $this->token = session('api_token');

            if (!$this->token) {
                $response = Http::post('https://pluginng.com/api/login', [
                    'email' => env('PLUGGING_EMAIL'),
                    'password' => env('PLUGGING_PASSWORD'),
                ]);

                if ($response->successful() && $response->json('success')) {
                    $this->token = $response->json('data.token');
                    session(['api_token' => $this->token]);
                } else {
                    //session()->flash('error', 'Failed to retrieve token.');
                    return;
                }
            }

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->token,
            ])->get('https://pluginng.com/api/get/plans');

            if ($response->successful()) {
                $this->apiData = $response->json('data') ?? [];
            } else {
                //session()->flash('error', 'Failed to fetch plans.');
            }
        } catch (\Exception $e) {
            //session()->flash('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    public function buyData()
    {
        $this->validate();

        $buyDataUrl = 'https://pluginng.com/api/purchase/data';
		
        $dataPayload = [
            'plan_id' => $this->dataPlan,
            'phonenumber' => $this->phoneNumber,
            'subcategory_id' => $this->dataType,
        ];
		
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->token,
                'Content-Type' => 'application/json',
            ])->post($buyDataUrl, $dataPayload);

            $responseData = $response->json();

            if (isset($responseData['success']) && $responseData['success']) {
				$this->toast()->success('Success', 'This is a success message.')->send();
            } else {
                $this->dialog()->error('Error', ''. ($responseData['message'] ?? ''))->send();
            }

        } catch (\Exception $e) {
            session()->flash('error', 'An error occurred: ' . $e->getMessage());
        }
    }
};
?>


<div>
    <x-ts-toast />
    <x-ts-dialog />
    <div class="bg-gray-50 text-black/50">
        <div class="relative min-h-screen flex flex-col items-center justify-center selection:bg-[#FF2D20] selection:text-white">
            <div class="relative w-full max-w-2xl px-6 lg:max-w-7xl">

                <main>
                    <div class="grid gap-6 lg:grid-cols-2 lg:gap-8">
                        <div id="docs-card" class="flex flex-col items-start gap-6 p-6 overflow-hidden rounded-lg bg-white shadow-[0px_14px_34px_0px_rgba(0,0,0,0.08)] ring-1 ring-white/[0.05] transition duration-300 hover:text-black/70 focus:outline-none focus-visible:ring-[#FF2D20] md:row-span-3 lg:p-10 lg:pb-10">
                            <h2 class="text-xl font-semibold text-black">Purchase Data</h2>

                            <form wire:submit.prevent="buyData" class="w-full" id="data-purchase-form">
                                <!-- Network Field -->
                                <label class="block my-2 text-sm font-medium text-gray-900">Network</label>
                                <select id="network" wire:model="network" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                                    <option value="">-- Select Network --</option>
                                    <option value="MTN">MTN</option>
                                    <option value="GLO">GLO</option>
                                    <option value="AIRTEL">AIRTEL</option>
                                    <option value="9MOBILE">9MOBILE</option>
                                </select>
            
                                <!-- Data Type Field -->
                                <label class="block my-2 text-sm font-medium text-gray-900">Data Type</label>
                                <select id="title" wire:model="dataType" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" disabled>
                                    <option value="">-- Select Data Type --</option>
                                </select>
                                @error('dataType') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
            
                                <!-- Data Plan Field -->
                                <label class="block my-2 text-sm font-medium text-gray-900">Data Plan</label>
                                <select id="plan" wire:model="dataPlan" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" disabled>
                                    <option value="">-- Select Data Plan --</option>
                                </select>
                                @error('dataPlan') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
            
                                <!-- Phone Number Field -->
                                <label class="block my-2 text-sm font-medium text-gray-900">Phone Number</label>
                                <input type="number" wire:model="phoneNumber" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                                @error('phoneNumber') <p class="text-sm text-red-600">{{ $message }}</p> @enderror
            
                                <!-- Submit Button -->
                                <x-ts-button type="submit" class="mt-4" color="green" loading>Purchase</x-ts-button>
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


<script>
    // Fetch API data from Livewire
    const apiData = @json($this->apiData ?? []);

    // Get DOM elements
    const networkSelect = document.getElementById('network');
    const titleSelect = document.getElementById('title');
    const planSelect = document.getElementById('plan');

    // Function to update Titles based on selected Network
    function updateTitles(network) {
        // Reset titles and plans
        titleSelect.innerHTML = '<option value="">-- Select Data Type --</option>';
        planSelect.innerHTML = '<option value="">-- Select Data Plan --</option>';
        planSelect.disabled = true;

        if (!network) {
            titleSelect.disabled = true;
            return;
        }

        // Filter titles based on selected network
        const filteredTitles = apiData.filter(item => 
            item.category === "Data" && item.title.toLowerCase().includes(network.toLowerCase())
        );

        if (filteredTitles.length > 0) {
            titleSelect.disabled = false;
            filteredTitles.forEach(item => {
                const option = document.createElement('option');
                option.value = item.subcategory_id;
                option.text = item.title;
                titleSelect.appendChild(option);
            });
        } else {
            titleSelect.disabled = true;
        }
    }

    // Function to update Plans based on selected Title
    function updatePlans(subcategoryId) {
        // Reset plans
        planSelect.innerHTML = '<option value="">-- Select Data Plan --</option>';
        planSelect.disabled = true;

        if (!subcategoryId) return;

        // Find the selected title
        const selectedTitle = apiData.find(item => item.subcategory_id == subcategoryId);

        if (selectedTitle && selectedTitle.plan && selectedTitle.plan.length > 0) {
            planSelect.disabled = false;
            selectedTitle.plan.forEach(plan => {
                const option = document.createElement('option');
                option.value = plan.plan;
                option.text = `${plan.plan} - ₦${plan.amount}`;
                planSelect.appendChild(option);
            });
        }
    }

    // Event listener for Network selection
    networkSelect.addEventListener('change', function() {
        updateTitles(this.value);
    });

    // Event listener for Title selection
    titleSelect.addEventListener('change', function() {
        updatePlans(this.value);
    });

    // Initialize titles if network is already selected
    if (networkSelect.value) {
        updateTitles(networkSelect.value);
    }

    // Initialize plans if title is already selected
    if (titleSelect.value) {
        updatePlans(titleSelect.value);
    }
</script>
