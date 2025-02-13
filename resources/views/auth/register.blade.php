<x-guest-layout>
    <x-authentication-card>
        <x-slot name="logo">
            <x-authentication-card-logo />
        </x-slot>

        <x-validation-errors class="mb-4" />

        <form method="POST" action="{{ route('register') }}">
            @csrf

            <div>
                <x-ts-input label="Username *" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" icon="users" />
            </div>

            <div class="mt-4">
                <x-ts-input label="Email *" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="email" icon="envelope" />
            </div>

            <div class="mt-4">
                <x-ts-input label="Phone Number *" class="block mt-1 w-full" type="number" name="phone" :value="old('phone')" required autocomplete="phone" icon="phone" />
            </div>

            <div class="mt-4">
                <x-ts-password label="Password *" generator :rules="['min:8', 'symbols', 'numbers', 'mixed']" class="block mt-1 w-full" name="password" required autocomplete="new-password" icon="key" />
            </div>

            <div class="mt-4">
                <x-ts-password label="Confirm Password *" class="block mt-1 w-full" type="password" name="password_confirmation" required autocomplete="new-password" icon="key" />
            </div>

            @if (Laravel\Jetstream\Jetstream::hasTermsAndPrivacyPolicyFeature())
                <div class="mt-4">
                    <x-label for="terms">
                        <div class="flex items-center">
                            <x-checkbox name="terms" id="terms" required />

                            <div class="ms-2">
                                {!! __('I agree to the :terms_of_service and :privacy_policy', [
                                        'terms_of_service' => '<a target="_blank" href="'.route('terms.show').'" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">'.__('Terms of Service').'</a>',
                                        'privacy_policy' => '<a target="_blank" href="'.route('policy.show').'" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">'.__('Privacy Policy').'</a>',
                                ]) !!}
                            </div>
                        </div>
                    </x-label>
                </div>
            @endif

            <div class="flex items-center justify-end mt-4">

                <x-button class="ms-4">
                    {{ __('Register') }}
                </x-button>
            </div>

            <p class="text-sm font-light text-gray-500 dark:text-gray-400">
                Already have an account? <a wire:navigate href="{{ route('login') }}" class="font-medium text-primary-600 hover:underline dark:text-primary-500">Login here</a>
            </p>
        </form>
    </x-authentication-card>
</x-guest-layout>
