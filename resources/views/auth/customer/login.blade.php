<x-layouts::auth :title="__('Log in')">
    <div class="min-h-screen flex items-center justify-center py-12 lg:px-8">
        <div class="max-w-md w-full">
            {{-- Header --}}
            <div class="text-center mb-8">
                <div class="flex items-center justify-center">
                    <x-phosphor-yarn class="h-10 w-10 text-orange-600" />
                    <a href="{{ route('home') }}" class="text-4xl font-bold text-orange-600">Yarniverse</a>
                </div>
                <h2 class="mt-6 text-2xl font-bold text-gray-900">{{ __('Welcome back') }}</h2>
            </div>

            {{-- Card --}}
            <div class="bg-white py-8 px-6 shadow-lg rounded-lg">
                {{-- Session status --}}
                <x-auth-session-status class="mb-4 text-center" :status="session('status')" />

                <form method="POST" action="{{ route('login.store') }}" class="flex flex-col gap-6" novalidate>
                    @csrf

                    {{-- Email --}}
                    <flux:input name="email" :label="__('Email Address')" :value="old('email')" type="email" required
                        autofocus autocomplete="email" maxlength="40" placeholder="Email Address" data-flux-field />

                    {{-- Password --}}
                    <div class="relative" x-data="{ show: false }">
                        <flux:input name="password" :label="__('Password')" type="password" required
                            autocomplete="current-password" maxlength="50" :placeholder="__('Password')" viewable
                            x-bind:type="show ? 'text' : 'password'" data-flux-field />

                        @if (Route::has('password.request'))
                            <flux:link class="absolute top-0 end-0 text-sm text-gray-700" :href="route('password.request')"
                                wire:navigate>
                                {{ __('Forgot password?') }}
                            </flux:link>
                        @endif
                    </div>

                    {{-- Remember --}}
                    <flux:checkbox name="remember" :label="__('Remember me')" :checked="old('remember')"
                        class="text-sm text-gray-600" data-flux-field />

                    {{-- Submit --}}
                    <div class="flex items-center justify-end">
                        <flux:button variant="primary" type="submit"
                            class="w-full bg-orange-500 text-white py-3 px-4 rounded-lg hover:bg-orange-600 transition font-semibold"
                            data-test="login-button">
                            {{ __('Log in') }}
                        </flux:button>
                    </div>

                    <div class="flex items-center justify-center">
                        <p class="text-sm text-gray-600">
                            {{ __("Don't have an account?") }}
                            <flux:link :href="route('register')" class="font-medium text-blue-600 hover:text-indigo-500"
                                wire:navigate>
                                {{ __('Sign up') }}
                            </flux:link>
                        </p>
                    </div>

                </form>

                {{-- Socials --}}
                <div class="mt-6">
                    <div class="relative">
                        <div class="absolute inset-0 flex items-center">
                            <div class="w-full border-t border-gray-300"></div>
                        </div>
                        <div class="relative flex justify-center text-sm">
                            <span class="px-2 bg-white text-gray-500">{{ __('Or continue with') }}</span>
                        </div>
                    </div>

                    <div class="mt-6 grid grid-cols-2 gap-3">
                        <a href="{{ route('home') }}"
                            class="w-full inline-flex justify-center py-2 px-4 border border-gray-300 rounded-lg shadow-sm bg-white text-sm font-medium text-gray-500 hover:bg-orange-100">
                            Google
                        </a>
                        <a href="{{ route('home') }}"
                            class="w-full inline-flex justify-center py-2 px-4 border border-gray-300 rounded-lg shadow-sm bg-white text-sm font-medium text-gray-500 hover:bg-orange-100">
                            Facebook
                        </a>
                    </div>
                </div>
            </div>

            {{-- Back link --}}
            <p class="mt-6 text-center text-sm text-gray-600">
                <flux:link :href="route('home')" class="font-medium text-blue-600 hover:text-indigo-500" wire:navigate>←
                    {{ __('Back to Home') }}
                </flux:link>
            </p>
        </div>
    </div>
</x-layouts::auth>