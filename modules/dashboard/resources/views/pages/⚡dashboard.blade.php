<?php

use Livewire\Component;

new class extends Component {
    public $stats = [];
    public $recentActivity = [];

    public function mount(): void
    {
        $this->stats = [
            'total_users' => 150,
            'active_users' => 45,
            'total_posts' => 320,
            'new_posts_today' => 12,
        ];

        $this->recentActivity = [
            ['user' => 'John Doe', 'action' => 'Created new post', 'time' => '2 minutes ago'],
            ['user' => 'Jane Smith', 'action' => 'Updated profile', 'time' => '5 minutes ago'],
            ['user' => 'Bob Johnson', 'action' => 'Deleted comment', 'time' => '10 minutes ago'],
        ];
    }
};
?>

<flux:heading>Dashboard</flux:heading>

<!-- Stats Grid -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <div class="bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-lg p-6">
        <div class="flex items-center">
            <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900 rounded-lg flex items-center justify-center">
                <flux:icon name="layout-grid" class="w-6 h-6 text-blue-600 dark:text-blue-400" />
            </div>
            <div class="ml-4">
                <div class="text-sm text-zinc-600 dark:text-zinc-400">Total Users</div>
                <div class="text-2xl font-semibold text-zinc-900 dark:text-zinc-100">{{ $stats['total_users'] }}</div>
            </div>
        </div>
    </div>

    <div class="bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-lg p-6">
        <div class="flex items-center">
            <div class="w-12 h-12 bg-green-100 dark:bg-green-900 rounded-lg flex items-center justify-center">
                <flux:icon name="chevrons-up-down" class="w-6 h-6 text-green-600 dark:text-green-400" />
            </div>
            <div class="ml-4">
                <div class="text-sm text-zinc-600 dark:text-zinc-400">Active Users</div>
                <div class="text-2xl font-semibold text-zinc-900 dark:text-zinc-100">{{ $stats['active_users'] }}</div>
            </div>
        </div>
    </div>

    <div class="bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-lg p-6">
        <div class="flex items-center">
            <div class="w-12 h-12 bg-purple-100 dark:bg-purple-900 rounded-lg flex items-center justify-center">
                <flux:icon name="folder-git-2" class="w-6 h-6 text-purple-600 dark:text-purple-400" />
            </div>
            <div class="ml-4">
                <div class="text-sm text-zinc-600 dark:text-zinc-400">Total Posts</div>
                <div class="text-2xl font-semibold text-zinc-900 dark:text-zinc-100">{{ $stats['total_posts'] }}</div>
            </div>
        </div>
    </div>

    <div class="bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-lg p-6">
        <div class="flex items-center">
            <div class="w-12 h-12 bg-orange-100 dark:bg-orange-900 rounded-lg flex items-center justify-center">
                <flux:icon name="book-open-text" class="w-6 h-6 text-orange-600 dark:text-orange-400" />
            </div>
            <div class="ml-4">
                <div class="text-sm text-zinc-600 dark:text-zinc-400">New Posts Today</div>
                <div class="text-2xl font-semibold text-zinc-900 dark:text-zinc-100">{{ $stats['new_posts_today'] }}</div>
            </div>
        </div>
    </div>
</div>

<!-- Recent Activity -->
<div class="bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-lg">
    <div class="px-6 py-4 border-b border-zinc-200 dark:border-zinc-700">
        <flux:heading>Recent Activity</flux:heading>
    </div>
    
    <div class="p-6">
        <div class="space-y-4">
            @foreach($recentActivity as $activity)
                <div class="flex items-center justify-between py-3 {{ !$loop->last ? 'border-b border-zinc-200 dark:border-zinc-700' : '' }}">
                    <div class="flex items-center">
                        <div class="w-10 h-10 bg-zinc-100 dark:bg-zinc-700 rounded-full flex items-center justify-center">
                            <span class="text-sm font-medium text-zinc-600 dark:text-zinc-300">{{ substr($activity['user'], 0, 1) }}{{ substr($activity['user'], strpos($activity['user'], ' ') + 1, 1) }}</span>
                        </div>
                        <div class="ml-4">
                            <div class="font-medium text-zinc-900 dark:text-zinc-100">{{ $activity['user'] }}</div>
                            <div class="text-sm text-zinc-600 dark:text-zinc-400">{{ $activity['action'] }}</div>
                        </div>
                    </div>
                    <div class="text-sm text-zinc-500 dark:text-zinc-400">{{ $activity['time'] }}</div>
                </div>
            @endforeach
        </div>
    </div>
</div>
