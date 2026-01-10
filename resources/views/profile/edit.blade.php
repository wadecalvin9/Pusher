<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Profile - Skylex</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-gray-50 text-gray-900 min-h-screen flex flex-col">

    <!-- Navbar / Header -->
    <div class="bg-white border-b border-gray-100 sticky top-0 z-10">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center gap-4">
                    <a href="{{ route('chat') }}" class="group flex items-center gap-2 text-gray-500 hover:text-blue-600 transition-colors">
                        <div class="p-2 rounded-xl group-hover:bg-blue-50 transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                            </svg>
                        </div>
                        <span class="font-medium text-sm">Back to Chat</span>
                    </a>
                </div>
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center text-white font-bold text-xs shadow-md">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </div>
                    <span class="font-bold text-gray-900">Profile Settings</span>
                </div>
            </div>
        </div>
    </div>

    <main class="flex-1 py-8 px-4 sm:px-6 lg:px-8">
        <div class="max-w-3xl mx-auto space-y-6">

            <!-- Profile Information -->
            <div class="bg-white p-6 sm:p-8 rounded-2xl shadow-sm border border-gray-100">
                <header class="mb-6">
                    <h2 class="text-xl font-bold text-gray-900">Profile Information</h2>
                    <p class="mt-1 text-sm text-gray-500">Update your account's profile information and email address.</p>
                </header>

                <form method="post" action="{{ route('profile.update') }}" class="space-y-6">
                    @csrf
                    @method('patch')

                    <div class="grid gap-6 sm:grid-cols-2">
                        <div class="space-y-2">
                            <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
                            <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" required autofocus autocomplete="name"
                                   class="w-full bg-gray-50 border-gray-200 rounded-xl px-4 py-2.5 focus:bg-white focus:ring-2 focus:ring-blue-100 focus:border-blue-500 transition-all text-gray-900 text-sm">
                            @error('name') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="space-y-2">
                            <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                            <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" required autocomplete="username"
                                   class="w-full bg-gray-50 border-gray-200 rounded-xl px-4 py-2.5 focus:bg-white focus:ring-2 focus:ring-blue-100 focus:border-blue-500 transition-all text-gray-900 text-sm">
                            @error('email') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                        <div class="bg-yellow-50 p-4 rounded-xl border border-yellow-100 text-yellow-800 text-sm">
                            <p>Your email address is unverified.</p>
                            <button form="send-verification" class="underline hover:text-yellow-900 mt-2 font-medium">Click here to re-send the verification email.</button>
                            @if (session('status') === 'verification-link-sent')
                                <p class="mt-2 font-medium text-green-600">A new verification link has been sent to your email address.</p>
                            @endif
                        </div>
                    @endif

                    <div class="flex items-center gap-4 pt-2">
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2.5 rounded-xl font-medium text-sm transition-all shadow-lg shadow-blue-500/30">
                            Save Changes
                        </button>

                        @if (session('status') === 'profile-updated')
                            <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2000)" class="text-sm text-green-600 font-medium flex items-center gap-1">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                                Saved
                            </p>
                        @endif
                    </div>
                </form>
                <form id="send-verification" method="post" action="{{ route('verification.send') }}">@csrf</form>
            </div>

            <!-- Update Password -->
            <div class="bg-white p-6 sm:p-8 rounded-2xl shadow-sm border border-gray-100">
                <header class="mb-6">
                    <h2 class="text-xl font-bold text-gray-900">Update Password</h2>
                    <p class="mt-1 text-sm text-gray-500">Ensure your account is using a long, random password to stay secure.</p>
                </header>

                <form method="post" action="{{ route('password.update') }}" class="space-y-6">
                    @csrf
                    @method('put')

                    <div class="space-y-4 max-w-xl">
                        <div class="space-y-2">
                            <label for="current_password" class="block text-sm font-medium text-gray-700">Current Password</label>
                            <input type="password" name="current_password" id="current_password" autocomplete="current-password"
                                   class="w-full bg-gray-50 border-gray-200 rounded-xl px-4 py-2.5 focus:bg-white focus:ring-2 focus:ring-blue-100 focus:border-blue-500 transition-all text-gray-900 text-sm">
                            @error('current_password', 'updatePassword') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="space-y-2">
                            <label for="password" class="block text-sm font-medium text-gray-700">New Password</label>
                            <input type="password" name="password" id="password" autocomplete="new-password"
                                   class="w-full bg-gray-50 border-gray-200 rounded-xl px-4 py-2.5 focus:bg-white focus:ring-2 focus:ring-blue-100 focus:border-blue-500 transition-all text-gray-900 text-sm">
                            @error('password', 'updatePassword') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="space-y-2">
                            <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirm Password</label>
                            <input type="password" name="password_confirmation" id="password_confirmation" autocomplete="new-password"
                                   class="w-full bg-gray-50 border-gray-200 rounded-xl px-4 py-2.5 focus:bg-white focus:ring-2 focus:ring-blue-100 focus:border-blue-500 transition-all text-gray-900 text-sm">
                            @error('password_confirmation', 'updatePassword') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="flex items-center gap-4 pt-2">
                        <button type="submit" class="bg-gray-900 hover:bg-gray-800 text-white px-6 py-2.5 rounded-xl font-medium text-sm transition-all shadow-md">
                            Update Password
                        </button>

                        @if (session('status') === 'password-updated')
                            <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2000)" class="text-sm text-green-600 font-medium flex items-center gap-1">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                                Saved
                            </p>
                        @endif
                    </div>
                </form>
            </div>

            <!-- Delete Account -->
            <div class="bg-white p-6 sm:p-8 rounded-2xl shadow-sm border border-gray-100">
                <header class="mb-6">
                    <h2 class="text-xl font-bold text-red-600">Delete Account</h2>
                    <p class="mt-1 text-sm text-gray-500">Once your account is deleted, all of its resources and data will be permanently deleted.</p>
                </header>

                <div x-data="{ open: {{ $errors->userDeletion->isNotEmpty() ? 'true' : 'false' }} }">
                    <button @click="open = true" type="button" class="bg-red-50 hover:bg-red-100 text-red-600 border border-red-200 px-6 py-2.5 rounded-xl font-medium text-sm transition-all">
                        Delete Account
                    </button>

                    <!-- Modal Backdrop -->
                    <div x-show="open" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4 transition-opacity" 
                         x-transition:enter="ease-out duration-300"
                         x-transition:enter-start="opacity-0"
                         x-transition:enter-end="opacity-100"
                         x-transition:leave="ease-in duration-200"
                         x-transition:leave-start="opacity-100"
                         x-transition:leave-end="opacity-0"
                         style="display: none;">
                        
                        <!-- Modal Content -->
                        <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full p-6 transform transition-all"
                             @click.away="open = false"
                             x-transition:enter="ease-out duration-300"
                             x-transition:enter-start="opacity-0 scale-95"
                             x-transition:enter-end="opacity-100 scale-100"
                             x-transition:leave="ease-in duration-200"
                             x-transition:leave-start="opacity-100 scale-100"
                             x-transition:leave-end="opacity-0 scale-95">
                            
                            <h2 class="text-lg font-bold text-gray-900 mb-2">Are you sure you want to delete your account?</h2>
                            <p class="text-sm text-gray-500 mb-6">
                                Once your account is deleted, all of its resources and data will be permanently deleted. Please enter your password to confirm.
                            </p>

                            <form method="post" action="{{ route('profile.destroy') }}">
                                @csrf
                                @method('delete')

                                <div class="space-y-2 mb-6">
                                    <label for="password_delete" class="sr-only">Password</label>
                                    <input type="password" name="password" id="password_delete" placeholder="Password"
                                           class="w-full bg-gray-50 border-gray-200 rounded-xl px-4 py-2.5 focus:bg-white focus:ring-2 focus:ring-red-100 focus:border-red-500 transition-all text-gray-900 text-sm">
                                    @error('password', 'userDeletion') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                                </div>

                                <div class="flex justify-end gap-3">
                                    <button type="button" @click="open = false" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-200 rounded-xl hover:bg-gray-50 transition-colors">
                                        Cancel
                                    </button>
                                    <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-xl hover:bg-red-700 transition-colors shadow-lg shadow-red-500/30">
                                        Delete Account
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </main>
</body>
</html>
