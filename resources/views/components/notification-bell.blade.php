<!-- Componente de campana de notificaciones -->
<div x-data="window.notificationBell()" class="relative" @click.away="closeDropdown">
    <!-- Botón de la campana -->
    <button @click="toggleDropdown" type="button" class="relative p-2 text-gray-600 hover:text-amber-600 focus:outline-none">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
        </svg>
        
        <!-- Badge de notificaciones no leídas -->
        <template x-if="unreadCount > 0">
            <div class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center" x-text="unreadCount"></div>
        </template>
    </button>

    <!-- Dropdown de notificaciones -->
    <div x-show="isOpen" 
         x-cloak
         x-transition:enter="transition ease-out duration-100"
         x-transition:enter-start="transform opacity-0 scale-95"
         x-transition:enter-end="transform opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-75"
         x-transition:leave-start="transform opacity-100 scale-100"
         x-transition:leave-end="transform opacity-0 scale-95"
         class="absolute right-0 mt-2 w-80 bg-white rounded-md shadow-lg z-50">
        <div class="py-2">
            <!-- Encabezado -->
            <div class="px-4 py-2 border-b flex justify-between items-center">
                <h3 class="text-sm font-semibold text-gray-700">Notificacions</h3>
                <button @click="markAllAsRead" x-show="unreadCount !== null && unreadCount > 0" class="text-xs text-amber-600 hover:text-amber-800">
                    Marcar totes com llegides
                </button>
            </div>

            <!-- Lista de notificaciones -->
            <div class="max-h-64 overflow-y-auto">
                <template x-if="notifications.length === 0">
                    <div class="px-4 py-3 text-sm text-gray-500 text-center">
                        No hi ha notificacions
                    </div>
                </template>
                
                <template x-for="notification in notifications" :key="notification.id">
                    <div class="px-4 py-3 border-b border-gray-200 hover:bg-gray-50 relative group">
                        <a :href="notification.url" class="block pr-8">
                            <p class="text-sm text-gray-700" x-text="notification.mensaje"></p>
                            <p class="text-xs text-gray-500 mt-1" x-text="formatDate(notification.created_at)"></p>
                        </a>
                        <button x-show="notification.leida"
                            @click.prevent="eliminarNotificacion(notification.id)"
                            class="absolute right-2 top-3 text-gray-400 hover:text-red-500 opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                        <button x-show="!notification.leida" 
                                @click="markAsRead(notification.id)" 
                                class="ml-2 text-xs text-amber-600 hover:text-amber-800">
                            Marcar com llegida
                        </button>
                    </div>
                </template>
            </div>
        </div>
    </div>
</div>

<style>
[x-cloak] { display: none !important; }
</style>

@push('scripts')
<script>
function notificationBell() {
    return {
        isOpen: false,
        notifications: [],
        unreadCount: 0,
        
        init() {
            console.log('Inicializando componente de notificaciones');
            // Ejecutar inmediatamente
            this.fetchNotifications();
            this.fetchUnreadCount();
            
            // Actualizar cada 30 segundos
            setInterval(() => {
                console.log('Actualizando notificaciones (intervalo)');
                this.fetchNotifications();
                this.fetchUnreadCount();
            }, 30000);

            // Observar cambios en unreadCount
            this.$watch('unreadCount', (value) => {
                console.log('unreadCount cambió a:', value);
            });
        },
        
        toggleDropdown() {
            this.isOpen = !this.isOpen;
            if (this.isOpen) {
                this.fetchNotifications();
            }
            this.$nextTick(() => {
                if (!this.isOpen) {
                    this.closeDropdown();
                }
            });
        },
        
        closeDropdown() {
            this.isOpen = false;
        },
        
        async fetchNotifications() {
            try {
                console.log('Iniciando fetchNotifications');
                const response = await fetch('/notificaciones', {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                console.log('Response status:', response.status);
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                const data = await response.json();
                console.log('Notificaciones cargadas:', data);
                this.notifications = data;
            } catch (error) {
                console.error('Error al cargar notificaciones:', error);
            }
        },
        
        async fetchUnreadCount() {
            try {
                console.log('Iniciando fetchUnreadCount');
                const response = await fetch('/notificaciones/no-leidas', {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                console.log('Response status:', response.status);
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                const data = await response.json();
                console.log('Datos recibidos:', data);
                this.unreadCount = data.count;
                console.log('Contador actualizado:', this.unreadCount);
            } catch (error) {
                console.error('Error al cargar contador de notificaciones:', error);
            }
        },
        
        async markAsRead(id) {
            try {
                await fetch(`/notificaciones/${id}/marcar-leida`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });
                await this.fetchNotifications();
                await this.fetchUnreadCount();
            } catch (error) {
                console.error('Error al marcar notificación como leída:', error);
            }
        },
        
        async markAllAsRead() {
            try {
                await fetch('/notificaciones/marcar-todas-leidas', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });
                await this.fetchNotifications();
                await this.fetchUnreadCount();
            } catch (error) {
                console.error('Error al marcar todas las notificaciones como leídas:', error);
            }
        },
        
        formatDate(date) {
            return new Date(date).toLocaleString('ca-ES', {
                day: '2-digit',
                month: '2-digit',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });
        }
    }
}
</script>
@endpush
