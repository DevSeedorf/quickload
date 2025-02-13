<x-form-section submit="updatePassword">
  
    <x-slot name="form">
        <div class="col-span-6 sm:col-span-4">
            <h3 class="text-lg font-medium">{{ __('Update Password') }} </h3>
            <x-label value="Ensure you are using a long, random password to stay secure." />
        </div>

        <div class="col-span-6 sm:col-span-4">
            <x-ts-password label="Current Password" type="password" class="mt-1 block w-full" wire:model="state.current_password" autocomplete="current-password" icon="key" />
            <x-input-error for="current_password" class="mt-2" />
        </div>

        <div class="col-span-6 sm:col-span-4">
            <x-ts-password label="New Password" type="password" class="mt-1 block w-full" wire:model="state.password" autocomplete="new-password" icon="key" />
            <x-input-error for="password" class="mt-2" />
        </div>

        <div class="col-span-6 sm:col-span-4">
            <x-ts-password label="Password Confirmation" type="password" class="mt-1 block w-full" wire:model="state.password_confirmation" autocomplete="new-password" icon="key" />
            <x-input-error for="password_confirmation" class="mt-2" />
        </div>
    </x-slot>

    <x-slot name="actions">
        <x-action-message class="me-3" on="saved">
            {{ __('Saved.') }}
        </x-action-message>

        <x-button>
            {{ __('Save') }}
        </x-button>
    </x-slot>
</x-form-section>
