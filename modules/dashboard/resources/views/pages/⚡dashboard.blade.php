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

<x-layouts::app :title="__('Dashboard')">
    <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
        <div class="grid auto-rows-min gap-4 md:grid-cols-2 lg:grid-cols-4">
            <div class="relative aspect-video overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700">
                <div class="absolute inset-0 flex items-center justify-center">
                    <div class="text-center">
                        <div class="text-2xl font-bold text-gray-900 dark:text-white mb-2">{{ $stats['total_users'] }}</div>
                        <div class="text-sm text-gray-600 dark:text-gray-400">Total Users</div>
                    </div>
                </div>
            </div>
            <div class="relative aspect-video overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700">
                <div class="absolute inset-0 flex items-center justify-center">
                    <div class="text-center">
                        <div class="text-2xl font-bold text-gray-900 dark:text-white mb-2">{{ $stats['active_users'] }}</div>
                        <div class="text-sm text-gray-600 dark:text-gray-400">Active Users</div>
                    </div>
                </div>
            </div>
            <div class="relative aspect-video overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700">
                <div class="absolute inset-0 flex items-center justify-center">
                    <div class="text-center">
                        <div class="text-2xl font-bold text-gray-900 dark:text-white mb-2">{{ $stats['total_posts'] }}</div>
                        <div class="text-sm text-gray-600 dark:text-gray-400">Total Posts</div>
                    </div>
                </div>
            </div>
            <div class="relative aspect-video overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700">
                <div class="absolute inset-0 flex items-center justify-center">
                    <div class="text-center">
                        <div class="text-2xl font-bold text-gray-900 dark:text-white mb-2">{{ $stats['new_posts_today'] }}</div>
                        <div class="text-sm text-gray-600 dark:text-gray-400">New Today</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="relative h-full flex-1 overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700 p-6">
            <div class="h-full overflow-y-auto">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Recent Activity</h3>
                <div class="space-y-3">
                    @foreach($recentActivity as $activity)
                        <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-800 rounded-lg">
                            <div class="flex items-center">
                                <div class="w-8 h-8 bg-blue-500 text-white rounded-full flex items-center justify-center text-sm font-medium mr-3">
                                    {{ substr($activity['user'], 0, 1) }}
                                </div>
                                <div>
                                    <div class="font-medium text-gray-900 dark:text-white">{{ $activity['user'] }}</div>
                                    <div class="text-sm text-gray-600 dark:text-gray-400">{{ $activity['action'] }}</div>
                                </div>
                            </div>
                            <div class="text-sm text-gray-500 dark:text-gray-400">{{ $activity['time'] }}</div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</x-layouts::app>
