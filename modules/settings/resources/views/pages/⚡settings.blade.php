<?php

use Livewire\Component;

new class extends Component {
    public $settings = [];
    public $siteName = '';
    public $siteDescription = '';
    public $emailSettings = [];
    public $features = [];

    public function mount(): void
    {
        $this->loadSettings();
    }

    public function loadSettings(): void
    {
        // Mock data - replace with actual database query
        $this->siteName = 'My Application';
        $this->siteDescription = 'A Laravel application with Ladwire modules';
        $this->emailSettings = [
            'driver' => 'smtp',
            'host' => 'mail.example.com',
            'port' => 587,
            'from_address' => 'noreply@example.com',
            'from_name' => 'My Application',
        ];
        $this->features = [
            'user_registration' => true,
            'email_verification' => true,
            'two_factor_auth' => false,
            'maintenance_mode' => false,
        ];
    }

    public function saveSettings(): void
    {
        // Implement settings save logic
        $this->dispatch('settings-saved');
    }
};
?>

<flux:heading>Application Settings</flux:heading>

<!-- General Settings -->
<flux:card class="mb-6">
    <flux:card.header>
        <flux:card.title>General Settings</flux:card.title>
        <flux:card.description>Basic application configuration</flux:card.description>
    </flux:card.header>
    
    <flux:card.content>
        <flux:form wire:submit="saveSettings">
            <flux:field>
                <flux:label>Site Name</flux:label>
                <flux:input 
                    wire:model="siteName" 
                    placeholder="Enter site name"
                />
            </flux:field>
            
            <flux:field>
                <flux:label>Site Description</flux:label>
                <flux:textarea 
                    wire:model="siteDescription" 
                    placeholder="Enter site description"
                    rows="3"
                />
            </flux:field>
            
            <flux:button type="submit" variant="primary">
                <flux:icon.check class="w-4 h-4 mr-2" />
                Save Settings
            </flux:button>
        </flux:form>
    </flux:card.content>
</flux:card>

<!-- Email Settings -->
<flux:card class="mb-6">
    <flux:card.header>
        <flux:card.title>Email Settings</flux:card.title>
        <flux:card.description>Configure email delivery</flux:card.description>
    </flux:card.header>
    
    <flux:card.content>
        <flux:grid>
            <flux:field>
                <flux:label>Mail Driver</flux:label>
                <flux:select wire:model="emailSettings.driver">
                    <flux:select.option value="smtp">SMTP</flux:select.option>
                    <flux:select.option value="mail">Mail</flux:select.option>
                    <flux:select.option value="sendmail">Sendmail</flux:select.option>
                </flux:select>
            </flux:field>
            
            <flux:field>
                <flux:label>Host</flux:label>
                <flux:input 
                    wire:model="emailSettings.host" 
                    placeholder="mail.example.com"
                />
            </flux:field>
            
            <flux:field>
                <flux:label>Port</flux:label>
                <flux:input 
                    wire:model="emailSettings.port" 
                    type="number"
                    placeholder="587"
                />
            </flux:field>
            
            <flux:field>
                <flux:label>From Address</flux:label>
                <flux:input 
                    wire:model="emailSettings.from_address" 
                    type="email"
                    placeholder="noreply@example.com"
                />
            </flux:field>
        </flux:grid>
    </flux:card.content>
</flux:card>

<!-- Feature Toggles -->
<flux:card>
    <flux:card.header>
        <flux:card.title>Features</flux:card.title>
        <flux:card.description>Enable or disable application features</flux:card.description>
    </flux:card.header>
    
    <flux:card.content>
        <flux:grid>
            @foreach($features as $key => $enabled)
                <flux:field>
                    <flux:label>{{ ucwords(str_replace('_', ' ', $key)) }}</flux:label>
                    <flux:switch 
                        wire:model="features.{{ $key }}"
                        description="{{ $enabled ? 'Enabled' : 'Disabled' }}"
                    />
                </flux:field>
            @endforeach
        </flux:grid>
    </flux:card.content>
</flux:card>
