<x-filament::section>
    <x-slot name="heading">
        Atributos LDAP del Usuario
    </x-slot>

    <div class="space-y-2">
        @if($this->hasLdapAttributes())
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead>
                        <tr>
                            <th class="px-6 py-3 bg-gray-50 dark:bg-gray-800 text-left text-xs leading-4 font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Atributo
                            </th>
                            <th class="px-6 py-3 bg-gray-50 dark:bg-gray-800 text-left text-xs leading-4 font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Valor
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach ($this->getLdapAttributes() as $key => $value)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-300">
                                    {{ $key }}
                                </td>
                                <td class="px-6 py-4 whitespace-pre-wrap text-sm text-gray-900 dark:text-gray-300">
                                    @if (is_array($value))
                                        {{ implode(', ', $value) }}
                                    @else
                                        {{ $value }}
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="p-4 bg-white dark:bg-gray-800 rounded-lg shadow">
                <p class="text-center text-gray-500 dark:text-gray-400">
                    No hay atributos LDAP disponibles en este momento.
                    <br>
                    <span class="text-sm">
                        (Widget está visible - ID: {{ $this->getId() }})
                    </span>
                </p>
            </div>
        @endif
    </div>
</x-filament::section>
