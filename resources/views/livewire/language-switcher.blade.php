<div class="flex items-center space-x-2 rtl:space-x-reverse">
    <button 
        wire:click="switchLocale('ca')"
        @class([
            'px-2 py-1 rounded-lg text-sm font-medium transition-colors duration-200',
            'bg-primary-600 text-white dark:bg-primary-500' => $currentLocale === 'ca',
            'text-gray-700 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-700' => $currentLocale !== 'ca',
        ])
    >
        CAT
    </button>
    <button 
        wire:click="switchLocale('es')"
        @class([
            'px-2 py-1 rounded-lg text-sm font-medium transition-colors duration-200',
            'bg-primary-600 text-white dark:bg-primary-500' => $currentLocale === 'es',
            'text-gray-700 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-700' => $currentLocale !== 'es',
        ])
    >
        ESP
    </button>
</div>
