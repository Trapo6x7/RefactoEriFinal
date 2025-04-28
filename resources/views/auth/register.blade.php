<x-guest-layout>
    <div class="flex flex-col justify-center items-center bg-[#fff]">
        <div class="w-full max-w-md bg-white rounded-lg shadow-md p-8">
            <form method="POST" action="{{ route('register') }}">
                @csrf

                <!-- Name -->
                <div>
                    <x-input-label for="name" :value="__('Name')" class="text-primary-grey" />
                    <x-text-input id="name" class="block mt-1 w-full border-gray-300 text-primary-grey focus:border-blue-accent focus:ring focus:ring-blue-accent focus:ring-opacity-50" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
                    <x-input-error :messages="$errors->get('name')" class="mt-2 text-red-500" />
                </div>

                <!-- Email Address -->
                <div class="mt-4">
                    <x-input-label for="email" :value="__('Email')" class="text-primary-grey" />
                    <x-text-input id="email" class="block mt-1 w-full border-gray-300 text-primary-grey focus:border-blue-accent focus:ring focus:ring-blue-accent focus:ring-opacity-50" type="email" name="email" :value="old('email')" required autocomplete="username" />
                    <x-input-error :messages="$errors->get('email')" class="mt-2 text-red-500" />
                </div>

                <!-- Password -->
                <div class="mt-4">
                    <x-input-label for="password" :value="__('Password')" class="text-primary-grey" />
                    <x-text-input id="password" class="block mt-1 w-full border-gray-300 text-primary-grey focus:border-blue-accent focus:ring focus:ring-blue-accent focus:ring-opacity-50"
                                    type="password"
                                    name="password"
                                    required autocomplete="new-password" />
                    <x-input-error :messages="$errors->get('password')" class="mt-2 text-red-500" />
                </div>

                <!-- Confirm Password -->
                <div class="mt-4">
                    <x-input-label for="password_confirmation" :value="__('Confirm Password')" class="text-primary-grey" />
                    <x-text-input id="password_confirmation" class="block mt-1 w-full border-gray-300 text-primary-grey focus:border-blue-accent focus:ring focus:ring-blue-accent focus:ring-opacity-50"
                                    type="password"
                                    name="password_confirmation" required autocomplete="new-password" />
                    <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2 text-red-500" />
                </div>

                <div class="flex items-center justify-end mt-4">
                    <a class="underline text-sm text-primary-grey hover:text-blue-accent rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-accent" href="{{ route('login') }}">
                        {{ __('Already registered?') }}
                    </a>

                    <x-primary-button class="ms-4 bg-blue-accent text-white hover:bg-blue-700">
                        {{ __('Register') }}
                    </x-primary-button>
                </div>
            </form>
        </div>
    </div>
</x-guest-layout>