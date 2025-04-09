// Funciones para el modal
window.openModal = function() {
    const modal = document.getElementById('modal-evaluacion');
    modal.classList.remove('hidden');
    // Añadir las clases de transición después de un pequeño delay para que se vean
    setTimeout(() => {
        modal.querySelector('.bg-opacity-75').classList.add('opacity-100');
        modal.querySelector('.transform').classList.add('opacity-100', 'translate-y-0', 'sm:scale-100');
    }, 10);
}

window.closeModal = function() {
    const modal = document.getElementById('modal-evaluacion');
    // Primero quitamos las clases de transición
    modal.querySelector('.bg-opacity-75').classList.remove('opacity-100');
    modal.querySelector('.transform').classList.remove('opacity-100', 'translate-y-0', 'sm:scale-100');
    // Después de la transición, ocultamos el modal
    setTimeout(() => {
        modal.classList.add('hidden');
    }, 300);
}

document.addEventListener('DOMContentLoaded', function() {
    const empleadoSelect = document.getElementById('empleado_id');
    const indicadorSelect = document.getElementById('indicador_id');
    const indicadorWrapper = document.getElementById('indicador-wrapper');
    const indicadorDisplay = document.getElementById('indicador-display');

    if (empleadoSelect && indicadorSelect && indicadorWrapper && indicadorDisplay) {
        // Ocultar el select original
        indicadorSelect.style.display = 'none';
        
        // Mostrar el div personalizado cuando se hace clic
        indicadorDisplay.addEventListener('click', function() {
            indicadorWrapper.classList.toggle('active');
        });

        // Cerrar al hacer clic fuera
        document.addEventListener('click', function(e) {
            if (!indicadorWrapper.contains(e.target)) {
                indicadorWrapper.classList.remove('active');
            }
        });

        empleadoSelect.addEventListener('change', function() {
            const empleadoId = this.value;
            
            // Resetear el select y el display
            indicadorSelect.innerHTML = '<option value="">Cargando indicadores...</option>';
            indicadorDisplay.textContent = 'Cargando indicadores...';
            indicadorSelect.disabled = true;
            indicadorDisplay.classList.add('disabled');

            if (empleadoId) {
                // Cargar los indicadores
                fetch(`/evaluaciones/indicadores?empleado_id=${empleadoId}`)
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Error cargando indicadores');
                        }
                        return response.json();
                    })
                    .then(indicadores => {
                        // Limpiar el select y el contenedor de opciones
                        indicadorSelect.innerHTML = '<option value="">Selecciona un indicador</option>';
                        const optionsContainer = document.getElementById('indicador-options');
                        optionsContainer.innerHTML = '<div class="custom-option" data-value="">Selecciona un indicador</div>';
                        
                        if (Array.isArray(indicadores)) {
                            indicadores.forEach(indicador => {
                                // Añadir al select original (oculto)
                                const option = document.createElement('option');
                                option.value = indicador.id;
                                option.textContent = indicador.nombre;
                                indicadorSelect.appendChild(option);

                                // Añadir opción personalizada
                                const customOption = document.createElement('div');
                                customOption.className = 'custom-option';
                                customOption.setAttribute('data-value', indicador.id);
                                customOption.textContent = indicador.nombre;
                                customOption.title = indicador.nombre;
                                
                                customOption.addEventListener('click', function() {
                                    const value = this.getAttribute('data-value');
                                    indicadorSelect.value = value;
                                    indicadorDisplay.textContent = this.textContent;
                                    indicadorWrapper.classList.remove('active');
                                    // Disparar evento change en el select original
                                    indicadorSelect.dispatchEvent(new Event('change'));
                                });
                                
                                optionsContainer.appendChild(customOption);
                            });
                        }
                        
                        indicadorSelect.disabled = false;
                        indicadorDisplay.classList.remove('disabled');
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        indicadorSelect.innerHTML = '<option value="">Error cargando indicadores</option>';
                        indicadorDisplay.textContent = 'Error cargando indicadores';
                        indicadorSelect.disabled = true;
                        indicadorDisplay.classList.add('disabled');
                    });
            } else {
                indicadorSelect.innerHTML = '<option value="">Primer selecciona un empleat</option>';
                indicadorDisplay.textContent = 'Primer selecciona un empleat';
                indicadorSelect.disabled = true;
                indicadorDisplay.classList.add('disabled');
            }
        });
    }
});
