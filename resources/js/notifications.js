export function notificationBell() {
    return {
        isOpen: false,
        notifications: [],
        unreadCount: 0,
        
        init() {
            // Ejecutar inmediatamente
            this.fetchNotifications();
            this.fetchUnreadCount();
            
            // Actualizar cada 30 segundos
            setInterval(() => {
                this.fetchNotifications();
                this.fetchUnreadCount();
            }, 30000);
        },
        
        toggleDropdown() {
            this.isOpen = !this.isOpen;
            if (this.isOpen) {
                this.fetchNotifications();
            }
        },
        
        closeDropdown() {
            this.isOpen = false;
        },
        
        async fetchNotifications() {
            try {
                const response = await fetch('/notificaciones', {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                this.notifications = await response.json();
            } catch (error) {
                console.error('Error al cargar notificaciones:', error);
            }
        },
        
        async fetchUnreadCount() {
            try {
                const response = await fetch('/notificaciones/no-leidas', {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                const data = await response.json();
                this.unreadCount = data.count;
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
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
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
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });
                await this.fetchNotifications();
                await this.fetchUnreadCount();
            } catch (error) {
                console.error('Error al marcar todas las notificaciones como leídas:', error);
            }
        },

        async eliminarNotificacion(id) {
            try {
                const response = await fetch(`/notificaciones/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });

                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                await this.fetchNotifications();
                await this.fetchUnreadCount();
            } catch (error) {
                console.error('Error al eliminar la notificación:', error);
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
