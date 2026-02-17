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
<flux:grid class="mb-8">
    <flux:card>
        <flux:card.content>
            <div class="flex items-center">
                <flux:icon.users class="w-6 h-6 text-blue-600" />
                <div class="ml-4">
                    <flux:text size="sm" color="gray">Total Users</flux:text>
                    <flux:text size="2xl" weight="semibold">{{ $stats['total_users'] }}</flux:text>
                </div>
            </div>
        </flux:card.content>
    </flux:card>

    <flux:card>
        <flux:card.content>
            <div class="flex items-center">
                <flux:icon.check-circle class="w-6 h-6 text-green-600" />
                <div class="ml-4">
                    <flux:text size="sm" color="gray">Active Users</flux:text>
                    <flux:text size="2xl" weight="semibold">{{ $stats['active_users'] }}</flux:text>
                </div>
            </div>
        </flux:card.content>
    </flux:card>

    <flux:card>
        <flux:card.content>
            <div class="flex items-center">
                <flux:icon.document-text class="w-6 h-6 text-purple-600" />
                <div class="ml-4">
                    <flux:text size="sm" color="gray">Total Posts</flux:text>
                    <flux:text size="2xl" weight="semibold">{{ $stats['total_posts'] }}</flux:text>
                </div>
            </div>
        </flux:card.content>
    </flux:card>

    <flux:card>
        <flux:card.content>
            <div class="flex items-center">
                <flux:icon.clock class="w-6 h-6 text-orange-600" />
                <div class="ml-4">
                    <flux:text size="sm" color="gray">New Posts Today</flux:text>
                    <flux:text size="2xl" weight="semibold">{{ $stats['new_posts_today'] }}</flux:text>
                </div>
            </div>
        </flux:card.content>
    </flux:card>
</flux:grid>

<!-- Recent Activity -->
<flux:card>
    <flux:card.header>
        <flux:card.title>Recent Activity</flux:card.title>
    </flux:card.header>
    
    <flux:card.content>
        <flux:separator />
        
        @foreach($recentActivity as $activity)
            <div class="flex items-center justify-between py-4">
                <div class="flex items-center">
                    <flux:avatar>{{ substr($activity['user'], 0, 1) }}</flux:avatar>
                    <div class="ml-4">
                        <flux:text weight="medium">{{ $activity['user'] }}</flux:text>
                        <flux:text size="sm" color="gray">{{ $activity['action'] }}</flux:text>
                    </div>
                </div>
                <flux:text size="sm" color="gray">{{ $activity['time'] }}</flux:text>
            </div>
            @if(!$loop->last)
                <flux:separator />
            @endif
        @endforeach
    </flux:card.content>
</flux:card>
