<x-layouts::auth :title="__('Log in')">
    <div class="flex flex-col gap-6">
        <x-auth-header :title="__('Holla,')" :title2="__('Welcome Back')" :description="__('Enter your username and password below to log in')" />
        <!-- Session Status -->
        <x-auth-session-status class="text-center" :status="session('status')" />

        <div class="w-full max-w-md mx-auto">
            <form method="POST" action="{{ route('login.store') }}" class="flex flex-col gap-4">
                @csrf

                <flux:input name="username" label="Username" placeholder="Masukkan username" />

                <flux:input type="password" name="password" label="Password" placeholder="Masukkan password" />

                <flux:checkbox name="remember" label="Remember me" />

                <flux:button variant="primary" color="dark" type="submit" class="w-full">
                    Login
                </flux:button>
            </form>
        </div>

        @if (Route::has('register'))
            <div class="space-x-1 text-sm text-center rtl:space-x-reverse text-zinc-600 dark:text-zinc-400">
                <span>{{ __('Don\'t have an account?') }}</span>
                <flux:link :href="route('register')" wire:navigate>{{ __('Sign up') }}</flux:link>
            </div>
        @endif
    </div>
</x-layouts::auth>
