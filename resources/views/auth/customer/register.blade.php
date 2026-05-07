<x-layouts::auth :title="__('Register')">
    <div class="min-h-screen flex items-center justify-center py-12 lg:px-8">
        <div class="max-w-md w-full">
            {{-- Header --}}
            <div class="text-center mb-8">
                <a href="{{ route('home') }}" class="text-4xl font-bold text-orange-600">{{ config('app.name') }}</a>
                <h2 class="mt-6 text-2xl font-bold text-gray-900">{{ __('Create your account') }}</h2>
            </div>

            {{-- Card --}}
            <div class="bg-white py-8 px-6 shadow-lg rounded-lg">
                <form method="POST" action="{{ route('register.store') }}" class="flex flex-col gap-6" novalidate>
                    @csrf

                    {{-- Session status --}}
                    <x-auth-session-status :status="session('status')" class="text-center" />

                    {{-- Name --}}
                    <flux:input name="name" :label="('Full name')" :value="old('name')" type="text" required autofocus
                        autocomplete="name" maxlength="30" placeholder="Your name" />

                    {{-- Email --}}
                    <flux:input name="email" :label="('Email address')" :value="old('email')" type="email" required
                        autocomplete="email" maxlength="40" placeholder="Email address" />

                    {{-- Phone (optional) --}}
                    <flux:input name="phone" :label="('Phone number (optional)')" :value="old('phone')" type="tel"
                        autocomplete="tel" maxlength="20" placeholder="Phone number"
                        @input="$el.value = $el.value.replace(/[^0-9+\s\-/]/g, '')" />

                    {{-- Password --}}
                    <div class="relative" x-data="{ show: false }">
                        <flux:input name="password" :label="('Password')" type="password" required
                            autocomplete="new-password" maxlength="40" placeholder="{{ ('Password') }}" viewable
                            x-bind:type="show ? 'text' : 'password'" />
                    </div>

                    {{-- Password confirmation --}}
                    <flux:input name="password_confirmation" :label="('Confirm password')" type="password" required
                        autocomplete="new-password" maxlength="40" placeholder="{{ ('Confirm password') }}" />

                    {{-- Terms --}}
                    <flux:checkbox name="terms" :label="__('I agree to the terms and privacy')" required />
                    <div class="flex items-center justify-center gap-5 mt-1 text-xs">
                        <flux:link href="#">Terms and Conditions</flux:link>
                        <flux:link href="#">Privacy Policy</flux:link>
                    </div>

                    {{-- Submit --}}
                    <flux:button variant="primary" type="submit"
                        class="w-full bg-blue-600 text-white py-3 px-4 rounded-lg hover:bg-indigo-700 transition font-semibold"
                        data-test="register-button">
                        {{ __('Create Account') }}
                    </flux:button>

                    <div class="flex items-center justify-center">
                        <p class="mt-2 text-sm text-gray-600">
                            {{ __('Already have an account?') }}
                            <flux:link :href="route('login')" class="font-medium text-blue-600 hover:text-indigo-500"
                                wire:navigate>
                                {{ __('Sign in') }}
                            </flux:link>
                        </p>
                    </div>
                </form>
            </div>

            {{-- Back to home --}}
            <p class="mt-6 text-center text-sm text-gray-600">
                <flux:link :href="route('home')" class="font-medium text-blue-600 hover:text-indigo-500" wire:navigate>←
                    {{ __('Back to Home') }}
                </flux:link>
            </p>
        </div>
    </div>
</x-layouts::auth>