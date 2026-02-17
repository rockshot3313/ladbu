<flux:heading>Settings</flux:heading>

<flux:card>
    <flux:card.header>
        <flux:card.title>General Settings</flux:card.title>
    </flux:card.header>
    
    <flux:card.content>
        <flux:form wire:submit="save">
            <!-- Site Name -->
            <flux:textbox 
                wire:model="siteName" 
                label="Site Name" 
                placeholder="Enter site name"
                required
            />
            @error('siteName') <flux:error>{{ $message }}</flux:error> @enderror

            <!-- Site Description -->
            <flux:textarea 
                wire:model="siteDescription" 
                label="Site Description" 
                placeholder="Enter site description"
                rows="3"
                required
            />
            @error('siteDescription') <flux:error>{{ $message }}</flux:error> @enderror

            <!-- Admin Email -->
            <flux:textbox 
                wire:model="adminEmail" 
                type="email" 
                label="Admin Email" 
                placeholder="admin@example.com"
                required
            />
            @error('adminEmail') <flux:error>{{ $message }}</flux:error> @enderror

            <!-- Max Users -->
            <flux:textbox 
                wire:model="maxUsers" 
                type="number" 
                label="Maximum Users" 
                placeholder="100"
                min="1"
                required
            />
            @error('maxUsers') <flux:error>{{ $message }}</flux:error> @enderror

            <!-- Toggle Settings -->
            <flux:separator class="my-6" />
            
            <flux:fieldset>
                <flux:fieldset.legend>Toggle Settings</flux:fieldset.legend>
                
                <flux:checkbox 
                    wire:model="enableRegistration" 
                    label="Enable User Registration"
                    description="Allow new users to register on your site"
                />
                
                <flux:checkbox 
                    wire:model="enableEmailNotifications" 
                    label="Email Notifications"
                    description="Send email notifications for important events"
                />
            </flux:fieldset>

            <!-- Action Buttons -->
            <flux:separator class="my-6" />
            
            <div class="flex justify-between">
                <flux:button variant="ghost" wire:click="resetToDefaults">
                    <flux:icon.arrow-path class="w-4 h-4 mr-2" />
                    Reset to Defaults
                </flux:button>
                
                <flux:button type="submit">
                    <flux:icon.check class="w-4 h-4 mr-2" />
                    Save Settings
                </flux:button>
            </div>
        </flux:form>
    </flux:card.content>
</flux:card>

<!-- Flash Messages -->
@if(session()->has('success'))
    <flux:toast variant="success">
        {{ session('success') }}
    </flux:toast>
@endif

<!-- Livewire Event Listeners -->
<script>
    document.addEventListener('livewire:init', () => {
        Livewire.on('settings-saved', (message) => {
            // You can use Flux UI toast here
            alert(message);
        });

        Livewire.on('settings-reset', (message) => {
            // You can use Flux UI toast here
            alert(message);
        });
    });
</script>
