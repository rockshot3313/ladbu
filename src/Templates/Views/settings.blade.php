<x-layouts::app :title="__('Settings')">
    <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
        <div class="relative h-full flex-1 overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700 p-6">
            <div class="h-full overflow-y-auto">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Settings</h3>
                
                <div class="space-y-6">
                    <!-- Profile Settings -->
                    <div class="bg-white dark:bg-gray-800 border border-neutral-200 dark:border-neutral-700 rounded-lg p-6">
                        <h4 class="text-md font-semibold text-gray-900 dark:text-white mb-4">Profile Settings</h4>
                        
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Name</label>
                                <input type="text" value="John Doe" class="w-full px-3 py-2 border border-neutral-300 dark:border-neutral-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500" />
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Email</label>
                                <input type="email" value="john@example.com" class="w-full px-3 py-2 border border-neutral-300 dark:border-neutral-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500" />
                            </div>
                        </div>
                    </div>

                    <!-- Notification Settings -->
                    <div class="bg-white dark:bg-gray-800 border border-neutral-200 dark:border-neutral-700 rounded-lg p-6">
                        <h4 class="text-md font-semibold text-gray-900 dark:text-white mb-4">Notification Settings</h4>
                        
                        <div class="space-y-4">
                            <div class="flex items-center justify-between">
                                <div>
                                    <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Email Notifications</label>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">Receive email updates about your account activity</p>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" checked class="sr-only peer" />
                                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white peer-checked:after:bg-blue-600 peer-checked:after:content-[''] peer-checked:after:absolute peer-checked:after:top-[2px] peer-checked:after:left-[2px] peer-checked:after:h-5 peer-checked:after:w-5 peer-checked:after:rounded-full peer-checked:after:transition-all peer-checked:after:content-['']"></div>
                                </label>
                            </div>
                            
                            <div class="flex items-center justify-between">
                                <div>
                                    <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Push Notifications</label>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">Receive push notifications on your device</p>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" class="sr-only peer" />
                                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white peer-checked:after:bg-blue-600 peer-checked:after:content-[''] peer-checked:after:absolute peer-checked:after:top-[2px] peer-checked:after:left-[2px] peer-checked:after:h-5 peer-checked:after:w-5 peer-checked:after:rounded-full peer-checked:after:transition-all peer-checked:after:content-['']"></div>
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Security Settings -->
                    <div class="bg-white dark:bg-gray-800 border border-neutral-200 dark:border-neutral-700 rounded-lg p-6">
                        <h4 class="text-md font-semibold text-gray-900 dark:text-white mb-4">Security Settings</h4>
                        
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Current Password</label>
                                <input type="password" value="password123" class="w-full px-3 py-2 border border-neutral-300 dark:border-neutral-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500" />
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">New Password</label>
                                <input type="password" placeholder="Enter new password" class="w-full px-3 py-2 border border-neutral-300 dark:border-neutral-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500" />
                            </div>
                            
                            <button class="w-full px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                                Update Password
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts::app>
