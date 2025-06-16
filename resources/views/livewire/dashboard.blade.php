<?php

use Livewire\Volt\Component;
use App\Models\ReservedAccount;
use Illuminate\Support\Facades\Auth;

new class extends Component
{
    public $hasWallet;

    public function mount()
    {
        // Check if the authenticated user has an account
        $this->hasWallet = ReservedAccount::where('user_id', Auth::user()->id)->get();
        
    }
    

         
};
?>

<div>
    <div class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="text-center">
                        <x-ts-card header="WALLET BALANCE" bordered color="green" class="bg-green-300">
                            @if ($hasWallet)
                                <x-ts-button data-modal-target="fund-wallet-modal" data-modal-toggle="fund-wallet-modal" sm class="">
                                    Fund Wallet
                                </x-ts-button>
                                <h3 class="text-lg text-white mb-2 font-bold">&#8358;0.00</h3>
                            @else
                                <x-button data-modal-target="create-wallet-modal" data-modal-toggle="create-wallet-modal" class="ms-3">
                                    {{ __('Create Wallet') }}
                                </x-button>
                            @endif
                            
                        </x-ts-card>
                    </div>
                    <div class="text-center">
                        <x-ts-card header="REFERRAL BONUS" bordered color="primary" class="bg-primary-600">
                            <h3 class="text-lg text-white mb-2 font-bold">&#8358;0.00 <x-ts-button icon="arrow-down-tray" position="left" class="mx-4 font-bold" sm>Claim</x-ts-button></h3>
                            <x-ts-clipboard text="https://quickload.com" />
                            
                        </x-ts-card>
                    </div>

                    <div class="hidden sm:block">
                    <a href="#">
                    <div id="alert-border-1" class="flex items-center p-3 mb-1 text-white rounded-lg border-blue-300 bg-blue-600" role="alert">
                        <svg class="shrink-0 w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M2 12C2 6.477 6.477 2 12 2s10 4.477 10 10-4.477 10-10 10S2 17.523 2 12Zm11-4a1 1 0 1 0-2 0v4a1 1 0 0 0 .293.707l3 3a1 1 0 0 0 1.414-1.414L13 11.586V8Z" clip-rule="evenodd"/>
                        </svg>
                        <div class="ms-3 text-sm font-medium">
                        All Transactions
                        </div>
                    </div>

                    <div id="alert-border-1" class="flex items-center p-3 mb-1 text-white rounded-lg border-green-300 bg-green-600" role="alert">
                        <svg class="shrink-0 w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M2 12C2 6.477 6.477 2 12 2s10 4.477 10 10-4.477 10-10 10S2 17.523 2 12Zm13.707-1.293a1 1 0 0 0-1.414-1.414L11 12.586l-1.793-1.793a1 1 0 0 0-1.414 1.414l2.5 2.5a1 1 0 0 0 1.414 0l4-4Z" clip-rule="evenodd"/>
                        </svg>
                        <div class="ms-3 text-sm font-medium">
                        Completed Transactions
                        </div>
                    </div>

                    <div id="alert-border-1" class="flex items-center p-3 mb-1 text-white rounded-lg border-yellow-300 bg-yellow-600" role="alert">
                        <svg class="shrink-0 w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z"/>
                        </svg>
                        <div class="ms-3 text-sm font-medium">
                        Pending Transactions
                        </div>
                    </div>

                    <div id="alert-border-1" class="flex items-center p-3 mb-1 text-white rounded-lg border-red-300 bg-red-600" role="alert">
                        <svg class="shrink-0 w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24">
                            <path  d="M2 12C2 6.477 6.477 2 12 2s10 4.477 10 10-4.477 10-10 10S2 17.523 2 12Zm7.707-3.707a1 1 0 0 0-1.414 1.414L10.586 12l-2.293 2.293a1 1 0 1 0 1.414 1.414L12 13.414l2.293 2.293a1 1 0 0 0 1.414-1.414L13.414 12l2.293-2.293a1 1 0 0 0-1.414-1.414L12 10.586 9.707 8.293Z" clip-rule="evenodd"/>
                          </svg>
                        <div class="ms-3 text-sm font-medium">
                        Failed Transactions
                        </div>
                    </div>
                    </a>
                    </div>
                </div>

                <hr class="h-px mt-4 mb-4 bg-gray-200 border-0">
                <h3 class="text-2xl font-bold mb-2">Quick Services</h3>
                <div class="custom-grid text-center">
                    <div class="bg-gray-200 p-4 flex flex-col items-center justify-center text-center">
                        <img src="{{ asset('storage/images/camera.png') }}" alt="" class="w-8 h-8 mb-4">
                        <span class="text-1xl font-bold mb-4">Purchase Airtime</span>
                        <span class="text-sm">Make airtime purchases to any network and pay from your mobile wallet.</span>
                    </div>

                    <div class="bg-gray-200 p-4 flex flex-col items-center justify-center text-center">
                        <img src="{{ asset('storage/images/wi-fi.png') }}" alt="" class="w-8 h-8 mb-4">
                        <span class="text-1xl font-bold mb-4">Purchase Data</span>
                        <span class="text-sm">Make data purchases to any network and pay from your mobile wallet.</span>
                    </div>

                    <div class="bg-gray-200 p-4 flex flex-col items-center justify-center text-center">
                        <img src="{{ asset('storage/images/home.png') }}" alt="" class="w-8 h-8 mb-4">
                        <span class="text-1xl font-bold mb-4">Electricity Bill</span>
                        <span class="text-sm">Pay your electricity bill online fast, secure and hassle-free.</span>
                    </div>

                    <div class="bg-gray-200 p-4 flex flex-col items-center justify-center text-center">
                        <img src="{{ asset('storage/images/television.png') }}" alt="" class="w-8 h-8 mb-4">
                        <span class="text-1xl font-bold mb-4">Cable Bill</span>
                        <span class="text-sm">Conveniently make TV subscription bills online fast and secure.</span>
                    </div>

                    <div class="bg-gray-200 p-4 flex flex-col items-center justify-center text-center">
                        <img src="{{ asset('storage/images/book.png') }}" alt="" class="w-8 h-8 mb-4">
                        <span class="text-1xl font-bold mb-4">Exam Recharge Pin</span>
                        <span class="text-sm">Buy your exam pin at a very low rates.</span>
                    </div>

                    <div class="bg-gray-200 p-4 flex flex-col items-center justify-center text-center">
                        <img src="{{ asset('storage/images/two-arrows.png') }}" alt="" class="w-8 h-8 mb-4">
                        <span class="text-1xl font-bold mb-4">Airtime to Cash</span>
                        <span class="text-sm">Convert your airtime to cash at amazing rates.</span>
                    </div>

                    <div class="bg-gray-200 p-4 flex flex-col items-center justify-center text-center">
                        <img src="{{ asset('storage/images/chatting.png') }}" alt="" class="w-8 h-8 mb-4">
                        <span class="text-1xl font-bold mb-4">Bulk SMS</span>
                        <span class="text-sm">Send bulk SMS at amazing rates.</span>
                    </div>

                    <div class="bg-gray-200 p-4 flex flex-col items-center justify-center text-center">
                        <img src="{{ asset('storage/images/two-arrows.png') }}" alt="" class="w-8 h-8 mb-4">
                        <span class="text-1xl font-bold mb-4">Transactions</span>
                        <span class="text-sm">Check all your transactions status.</span>
                    </div>
                    
                </div>
                
                

            </div>
        </div>
    </div>


    <div id="create-wallet-modal" data-modal-backdrop="static" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
        <div class="relative p-4 w-full max-w-2xl max-h-full">
            <!-- Modal content -->
            <div class="relative bg-white rounded-lg shadow-sm dark:bg-gray-700">
                <!-- Modal header -->
                <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600 border-gray-200">
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                        Create Wallet
                    </h3>
                    <button type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white" data-modal-hide="create-wallet-modal">
                        <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                        </svg>
                        <span class="sr-only">Close modal</span>
                    </button>
                </div>
                <!-- Modal body -->
                <livewire:wallet />
            </div>
        </div>
    </div>
</div>
