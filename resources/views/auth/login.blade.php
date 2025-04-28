<x-guest-layout>
  
        <div class="w-full max-w-md bg-[#fff] rounded-lg p-8 mt-8">
            <!-- Session Status -->
            <x-auth-session-status class="mb-4" :status="session('status')" />

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <!-- Email Address -->
                <div>
                    <x-input-label for="email" :value="__('Email')" class="text-primary-grey" />
                    <x-text-input id="email" class="block mt-1 w-full border-gray-300 text-primary-grey focus:border-blue-accent focus:ring focus:ring-blue-accent focus:ring-opacity-50" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
                    <x-input-error :messages="$errors->get('email')" class="mt-2 text-red-500" />
                </div>

                <!-- Password -->
                <div class="mt-4">
                    <x-input-label for="password" :value="__('Password')" class="text-primary-grey" />
                    <x-text-input id="password" class="block mt-1 w-full border-gray-300 text-primary-grey focus:border-blue-accent focus:ring focus:ring-blue-accent focus:ring-opacity-50"
                                    type="password"
                                    name="password"
                                    required autocomplete="current-password" />
                    <x-input-error :messages="$errors->get('password')" class="mt-2 text-red-500" />
                </div>

                <!-- Remember Me -->
                <div class="block mt-4">
                    <label for="remember_me" class="inline-flex items-center">
                        <input id="remember_me" type="checkbox" class="rounded bg-secondary-grey border-gray-300 text-blue-accent shadow-sm focus:ring-blue-accent" name="remember">
                        <span class="ms-2 text-sm text-primary-grey">{{ __('Remember me') }}</span>
                    </label>
                </div>

                <div class="flex items-center justify-end mt-4">
                    @if (Route::has('password.request'))
                        <a class="underline text-sm text-primary-grey hover:text-blue-accent rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-accent" href="{{ route('password.request') }}">
                            {{ __('Forgot your password?') }}
                        </a>
                    @endif

                    <x-primary-button class="ms-3 bg-blue-accent text-white hover:bg-blue-700">
                        {{ __('Log in') }}
                    </x-primary-button>
                </div>
            </form>
        </div>

</x-guest-layout>