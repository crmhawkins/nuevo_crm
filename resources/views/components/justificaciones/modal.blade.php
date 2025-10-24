<!-- Modal de Justificaciones -->
<div class="modal fade" id="justificacionesModal" tabindex="-1" aria-labelledby="justificacionesModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="justificacionesModalLabel">Nueva Justificación</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="justificacionesForm" action="{{ route('justificaciones.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label for="tipo_justificacion" class="form-label">Tipo de Justificación</label>
                            <select class="form-select" id="tipo_justificacion" name="tipo_justificacion" required>
                                <option value="">Seleccione una opción</option>
                                <option value="segunda_justificacion_presencia_basica">Segunda Justificacion Presencia Basica</option>
                            </select>
                        </div>

                        <!-- Campos dinámicos que aparecen según selección -->
                        <div id="campos_dinamicos" style="display: none;">
                            <div class="col-md-12 mb-3">
                                <label for="url_campo" class="form-label">URL <span class="text-danger">*</span></label>
                                <input type="url" class="form-control" id="url_campo" name="url_campo" placeholder="https://ejemplo.com" required>
                                <small class="text-muted">Los archivos serán generados automáticamente por el servidor</small>
                            </div>
                        </div>

                        <input type="hidden" id="nombre_justificacion" name="nombre_justificacion">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-success" id="enviarJustificacion">Enviar</button>
                </div>
            </form>
        </div>
    </div>
</div>

