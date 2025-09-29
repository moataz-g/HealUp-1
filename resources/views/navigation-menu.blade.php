<nav x-data="{ open: false }" class="bg-white dark:bg-gray-800 border-b border-gray-100 dark:border-gray-700 theme-transition">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}">
                        <x-application-mark class="block h-9 w-auto" />
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden sm:ml-10 sm:flex sm:items-center sm:space-x-4">
                    <!-- Dashboard Link -->
                    <x-nav-link href="{{ route('dashboard') }}" :active="request()->routeIs('dashboard') || request()->routeIs('health.dashboard')"
                                class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5a2 2 0 012-2h4a2 2 0 012 2v14l-5-3-5 3V5z"></path>
                        </svg>
                        {{ __('Dashboard') }}
                    </x-nav-link>

                    <!-- Health Tracking Dropdown -->
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open"
                                class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium leading-5 transition duration-150 ease-in-out focus:outline-none theme-transition
                                {{ request()->routeIs('habits.*') || request()->routeIs('progress.*') || request()->routeIs('health.reports.*')
                                   ? 'border-indigo-400 text-gray-900 dark:text-gray-100 focus:border-indigo-700'
                                   : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-600 focus:text-gray-700 dark:focus:text-gray-300 focus:border-gray-300 dark:focus:border-gray-600' }}">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                            </svg>
                            <span>Health Tracker</span>
                            <svg class="ml-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>

                        <div x-show="open"
                             @click.away="open = false"
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 transform scale-95"
                             x-transition:enter-end="opacity-100 transform scale-100"
                             x-transition:leave="transition ease-in duration-75"
                             x-transition:leave-start="opacity-100 transform scale-100"
                             x-transition:leave-end="opacity-0 transform scale-95"
                             class="absolute left-0 mt-2 w-56 rounded-md shadow-lg bg-white dark:bg-gray-800 ring-1 ring-black dark:ring-gray-600 ring-opacity-5 divide-y divide-gray-100 dark:divide-gray-700 z-50 theme-transition">
                            <div class="py-1">
                                <a href="{{ route('habits.index') }}"
                                   class="flex items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 theme-transition {{ request()->routeIs('habits.*') ? 'bg-gray-50 dark:bg-gray-700 text-indigo-600 dark:text-indigo-400' : '' }}">
                                    <span class="mr-3">🎯</span>
                                    My Habits
                                </a>
                                <a href="{{ route('progress.index') }}"
                                   class="flex items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 theme-transition {{ request()->routeIs('progress.*') ? 'bg-gray-50 dark:bg-gray-700 text-indigo-600 dark:text-indigo-400' : '' }}">
                                    <span class="mr-3">📊</span>
                                    Daily Progress
                                </a>
                                <a href="{{ route('health.reports.index') }}"
                                   class="flex items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 theme-transition {{ request()->routeIs('health.reports.*') ? 'bg-gray-50 dark:bg-gray-700 text-indigo-600 dark:text-indigo-400' : '' }}">
                                    <span class="mr-3">📈</span>
                                    Health Reports
                                </a>
                            </div>
                            <div class="py-1">
                                <a href="{{ route('habits.create') }}"
                                   class="flex items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 theme-transition">
                                    <span class="mr-3">➕</span>
                                    Create New Habit
                                </a>
                                <a href="{{ route('habits.available') }}"
                                   class="flex items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 theme-transition">
                                    <span class="mr-3">🌟</span>
                                    Browse Habits
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Wellness Events (Front Office) Link -->
                    <x-nav-link href="{{ route('events.frontoffice') }}" :active="request()->routeIs('events.frontoffice')"
                                class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3a1 1 0 011-1h6a1 1 0 011 1v4h3a1 1 0 011 1v10a1 1 0 01-1 1H4a1 1 0 01-1-1V8a1 1 0 011-1h4z"></path>
                        </svg>
                        {{ __('Wellness Events') }}
                    </x-nav-link>
                    <!-- Categories Link -->
                    <x-nav-link href="{{ route('categories.index') }}" :active="request()->routeIs('categories.*')"
                                class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2" fill="none" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h8" />
                        </svg>
                        {{ __('Categories') }}
                    </x-nav-link>

                    <!-- Nutrition Dropdown -->
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open"
                                class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium leading-5 transition duration-150 ease-in-out focus:outline-none theme-transition
                                {{ request()->routeIs('ingredients.*') || request()->routeIs('repas.*')
                                   ? 'border-indigo-400 text-gray-900 dark:text-gray-100 focus:border-indigo-700'
                                   : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-600 focus:text-gray-700 dark:focus:text-gray-300 focus:border-gray-300 dark:focus:border-gray-600' }}">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 15.546c-.523 0-1.046.151-1.5.454a2.704 2.704 0 01-3 0 2.704 2.704 0 00-3 0 2.704 2.704 0 01-3 0 2.704 2.704 0 00-3 0 2.704 2.704 0 01-3 0 2.699 2.699 0 00-1.5-.454v-.546c0-9.405 7.595-17 17-17s17 7.595 17 17v.546z"></path>
                            </svg>
                            <span>Nutrition</span>
                            <svg class="ml-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>

                        <div x-show="open"
                             @click.away="open = false"
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 transform scale-95"
                             x-transition:enter-end="opacity-100 transform scale-100"
                             x-transition:leave="transition ease-in duration-75"
                             x-transition:leave-start="opacity-100 transform scale-100"
                             x-transition:leave-end="opacity-0 transform scale-95"
                             class="absolute left-0 mt-2 w-56 rounded-md shadow-lg bg-white dark:bg-gray-800 ring-1 ring-black dark:ring-gray-600 ring-opacity-5 divide-y divide-gray-100 dark:divide-gray-700 z-50 theme-transition">
                            <div class="py-1">
                                <a href="{{ route('ingredients.index') }}"
                                   class="flex items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 theme-transition {{ request()->routeIs('ingredients.*') ? 'bg-gray-50 dark:bg-gray-700 text-indigo-600 dark:text-indigo-400' : '' }}">
                                    <span class="mr-3">🥕</span>
                                    Ingredients
                                </a>
                                <a href="{{ route('repas.index') }}"
                                   class="flex items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 theme-transition {{ request()->routeIs('repas.*') ? 'bg-gray-50 dark:bg-gray-700 text-indigo-600 dark:text-indigo-400' : '' }}">
                                    <span class="mr-3">🍽️</span>
                                    Meals (Repas)
                                </a>
                            </div>
                            <div class="py-1">
                                <a href="{{ route('ingredients.create') }}"
                                   class="flex items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 theme-transition">
                                    <span class="mr-3">➕</span>
                                    Add Ingredient
                                </a>
                                <a href="{{ route('repas.create') }}"
                                   class="flex items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 theme-transition">
                                    <span class="mr-3">🍳</span>
                                    Create Meal
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="hidden sm:flex sm:items-center sm:space-x-4 sm:ml-6">
                <!-- Theme Toggle -->
                <div class="theme-toggle-container">
                    <x-theme.toggle size="sm"/>
                </div>

                <!-- Teams Dropdown -->
                @if (Laravel\Jetstream\Jetstream::hasTeamFeatures())
                    <div class="relative">
                        <x-dropdown align="right" width="60">
                            <x-slot name="trigger">
                                <span class="inline-flex rounded-md">
                                    <button type="button" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none focus:bg-gray-50 dark:focus:bg-gray-700 active:bg-gray-50 dark:active:bg-gray-700 transition ease-in-out duration-150 theme-transition">
                                        {{ Auth::user()->currentTeam->name }}

                                        <svg class="ms-2 -me-0.5 size-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 15L12 18.75 15.75 15m-7.5-6L12 5.25 15.75 9" />
                                        </svg>
                                    </button>
                                </span>
                            </x-slot>

                            <x-slot name="content">
                                <div class="w-60">
                    <!-- Team Management -->
                    <div class="border-t border-gray-200 dark:border-gray-700 theme-transition"></div>

                    <div class="block px-4 py-2 text-xs text-gray-400 dark:text-gray-500 theme-transition">
                        {{ __('Manage Team') }}
                    </div>                                    <!-- Team Settings -->
                                    <x-dropdown-link href="{{ route('teams.show', Auth::user()->currentTeam->id) }}">
                                        {{ __('Team Settings') }}
                                    </x-dropdown-link>

                                    @can('create', Laravel\Jetstream\Jetstream::newTeamModel())
                                        <x-dropdown-link href="{{ route('teams.create') }}">
                                            {{ __('Create New Team') }}
                                        </x-dropdown-link>
                                    @endcan

                                    <!-- Team Switcher -->
                                    @if (Auth::user()->allTeams()->count() > 1)
                                        <div class="border-t border-gray-200"></div>

                                        <div class="block px-4 py-2 text-xs text-gray-400">
                                            {{ __('Switch Teams') }}
                                        </div>

                                        @foreach (Auth::user()->allTeams() as $team)
                                            <x-switchable-team :team="$team" />
                                        @endforeach
                                    @endif
                                </div>
                            </x-slot>
                        </x-dropdown>
                    </div>
                @endif

                <!-- Settings Dropdown -->
                <div class="relative">
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            @if (Laravel\Jetstream\Jetstream::managesProfilePhotos())
                                <button class="flex text-sm border-2 border-transparent rounded-full focus:outline-none focus:border-gray-300 dark:focus:border-gray-600 transition theme-transition">
                                    <img class="size-8 rounded-full object-cover" src="{{ Auth::user()->profile_photo_url }}" alt="{{ Auth::user()->name }}" />
                                </button>
                            @else
                                <span class="inline-flex rounded-md">
                                    <button type="button" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none focus:bg-gray-50 dark:focus:bg-gray-700 active:bg-gray-50 dark:active:bg-gray-700 transition ease-in-out duration-150 theme-transition">
                                        {{ Auth::user()->name }}

                                        <svg class="ms-2 -me-0.5 size-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                                        </svg>
                                    </button>
                                </span>
                            @endif
                        </x-slot>

                        <x-slot name="content">
                            <!-- Account Management -->
                            <div class="block px-4 py-2 text-xs text-gray-400">
                                {{ __('Manage Account') }}
                            </div>

                            <x-dropdown-link href="{{ route('profile.show') }}">
                                {{ __('Profile') }}
                            </x-dropdown-link>

                            @if (Laravel\Jetstream\Jetstream::hasApiFeatures())
                                <x-dropdown-link href="{{ route('api-tokens.index') }}">
                                    {{ __('API Tokens') }}
                                </x-dropdown-link>
                            @endif

                            <div class="border-t border-gray-200"></div>

                            <!-- Authentication -->
                            <form method="POST" action="{{ route('logout') }}" x-data>
                                @csrf

                                <x-dropdown-link href="{{ route('logout') }}"
                                         @click.prevent="$root.submit();">
                                    {{ __('Log Out') }}
                                </x-dropdown-link>
                            </form>
                        </x-slot>
                    </x-dropdown>
                </div>
            </div>

            <!-- Mobile Theme Toggle and Hamburger -->
            <div class="-me-2 flex items-center space-x-2 sm:hidden">
                <!-- Mobile Theme Toggle -->
                <div class="theme-toggle-container">
                    <x-theme.toggle size="sm"/>
                </div>

                <!-- Hamburger -->
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 dark:text-gray-500 hover:text-gray-500 dark:hover:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 focus:outline-none focus:bg-gray-100 dark:focus:bg-gray-700 focus:text-gray-500 dark:focus:text-gray-400 transition duration-150 ease-in-out theme-transition">
                    <svg class="size-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden bg-white dark:bg-gray-800 theme-transition">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link href="{{ route('dashboard') }}" :active="request()->routeIs('dashboard')">
                {{ __('Dashboard') }}
            </x-responsive-nav-link>

            <!-- Health Tracker Mobile Navigation -->
            <div class="border-t border-gray-200 dark:border-gray-700 mt-2 pt-2 theme-transition">
                <div class="block px-4 py-2 text-xs text-gray-400 dark:text-gray-500 uppercase tracking-wide theme-transition">
                    Health Tracker
                </div>
                <x-responsive-nav-link href="{{ route('habits.index') }}" :active="request()->routeIs('habits.*')">
                    🎯 My Habits
                </x-responsive-nav-link>
                <x-responsive-nav-link href="{{ route('progress.index') }}" :active="request()->routeIs('progress.*')">
                    📊 Daily Progress
                </x-responsive-nav-link>
                <x-responsive-nav-link href="{{ route('health.reports.index') }}" :active="request()->routeIs('health.reports.*')">
                    📈 Health Reports
                </x-responsive-nav-link>
                <x-responsive-nav-link href="{{ route('habits.create') }}">
                    ➕ Create New Habit
                </x-responsive-nav-link>
                <x-responsive-nav-link href="{{ route('habits.available') }}">
                    🌟 Browse Habits
                </x-responsive-nav-link>
            </div>

            <!-- Wellness Events (Front Office) Navigation -->
            <x-responsive-nav-link href="{{ route('events.frontoffice') }}" :active="request()->routeIs('events.frontoffice')">
                {{ __('Wellness Events') }}
            </x-responsive-nav-link>
            <!-- Categories Navigation -->
            <x-responsive-nav-link href="{{ route('categories.index') }}" :active="request()->routeIs('categories.*')">
                {{ __('Categories') }}
            </x-responsive-nav-link>

            <!-- Nutrition Mobile Navigation -->
                        <!-- Nutrition Mobile Navigation -->
            <div class="border-t border-gray-200 dark:border-gray-700 mt-2 pt-2 theme-transition">
                <div class="block px-4 py-2 text-xs text-gray-400 dark:text-gray-500 uppercase tracking-wide theme-transition">
                    Nutrition
                </div>
                <x-responsive-nav-link href="{{ route('ingredients.index') }}" :active="request()->routeIs('ingredients.*')">
                    🥕 Ingredients
                </x-responsive-nav-link>
                <x-responsive-nav-link href="{{ route('repas.index') }}" :active="request()->routeIs('repas.*')">
                    🍽️ Meals (Repas)
                </x-responsive-nav-link>
                <x-responsive-nav-link href="{{ route('ingredients.create') }}">
                    ➕ Add Ingredient
                </x-responsive-nav-link>
                <x-responsive-nav-link href="{{ route('repas.create') }}">
                    🍳 Create Meal
                </x-responsive-nav-link>
            </div>
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200 dark:border-gray-700 theme-transition">
            <div class="flex items-center px-4">
                @if (Laravel\Jetstream\Jetstream::managesProfilePhotos())
                    <div class="shrink-0 me-3">
                        <img class="size-10 rounded-full object-cover" src="{{ Auth::user()->profile_photo_url }}" alt="{{ Auth::user()->name }}" />
                    </div>
                @endif

                <div>
                    <div class="font-medium text-base text-gray-800 dark:text-gray-200 theme-transition">{{ Auth::user()->name }}</div>
                    <div class="font-medium text-sm text-gray-500 dark:text-gray-400 theme-transition">{{ Auth::user()->email }}</div>
                </div>
            </div>

            <div class="mt-3 space-y-1">
                <!-- Account Management -->
                <x-responsive-nav-link href="{{ route('profile.show') }}" :active="request()->routeIs('profile.show')">
                    {{ __('Profile') }}
                </x-responsive-nav-link>

                @if (Laravel\Jetstream\Jetstream::hasApiFeatures())
                    <x-responsive-nav-link href="{{ route('api-tokens.index') }}" :active="request()->routeIs('api-tokens.index')">
                        {{ __('API Tokens') }}
                    </x-responsive-nav-link>
                @endif

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}" x-data>
                    @csrf

                    <x-responsive-nav-link href="{{ route('logout') }}"
                                   @click.prevent="$root.submit();">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>

                <!-- Team Management -->
                @if (Laravel\Jetstream\Jetstream::hasTeamFeatures())
                    <div class="border-t border-gray-200"></div>

                    <div class="block px-4 py-2 text-xs text-gray-400">
                        {{ __('Manage Team') }}
                    </div>

                    <!-- Team Settings -->
                    <x-responsive-nav-link href="{{ route('teams.show', Auth::user()->currentTeam->id) }}" :active="request()->routeIs('teams.show')">
                        {{ __('Team Settings') }}
                    </x-responsive-nav-link>

                    @can('create', Laravel\Jetstream\Jetstream::newTeamModel())
                        <x-responsive-nav-link href="{{ route('teams.create') }}" :active="request()->routeIs('teams.create')">
                            {{ __('Create New Team') }}
                        </x-responsive-nav-link>
                    @endcan

                    <!-- Team Switcher -->
                    @if (Auth::user()->allTeams()->count() > 1)
                        <div class="border-t border-gray-200 dark:border-gray-700 theme-transition"></div>

                        <div class="block px-4 py-2 text-xs text-gray-400 dark:text-gray-500 theme-transition">
                            {{ __('Switch Teams') }}
                        </div>

                        @foreach (Auth::user()->allTeams() as $team)
                            <x-switchable-team :team="$team" component="responsive-nav-link" />
                        @endforeach
                    @endif
                @endif
            </div>
        </div>
    </div>
</nav>
