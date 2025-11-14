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
                                <option value="presencia_avanzada_2">Presencia Avanzada (2ª)</option>
                                <option value="puesto_trabajo_seguro">Puesto de trabajo seguro</option>
                                <option value="crm_erp_factura">CRM/ERP/Factura</option>
                            </select>
                        </div>

                        <!-- Campos dinámicos que aparecen según selección -->
                        <div id="campos_dinamicos" style="display: none;">
                            <div class="col-md-12 mb-4">
                                <label class="form-label d-block">Tipo de Análisis <span class="text-danger">*</span></label>
                                <div class="btn-group w-100" role="group">
                                    <input type="radio" class="btn-check" name="tipo_analisis" id="tipo_web" value="web" checked>
                                    <label class="btn btn-outline-primary" for="tipo_web">
                                        <i class="fas fa-globe"></i> WEB Normal
                                    </label>

                                    <input type="radio" class="btn-check" name="tipo_analisis" id="tipo_ecommerce" value="ecommerce">
                                    <label class="btn btn-outline-success" for="tipo_ecommerce">
                                        <i class="fas fa-shopping-cart"></i> ECOMMERCE
                                    </label>
                                </div>
                                <small class="text-muted d-block mt-2">
                                    <strong>WEB:</strong> Análisis estándar sin competidores<br>
                                    <strong>ECOMMERCE:</strong> Incluye análisis de competencia automático
                                </small>
                            </div>

                            <div class="col-md-12 mb-3">
                                <label for="url_campo" class="form-label">URL <span class="text-danger">*</span></label>
                                <input type="url" class="form-control" id="url_campo" name="url_campo" placeholder="https://ejemplo.com" required>
                                <small class="text-muted">Los archivos serán generados automáticamente por el servidor</small>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="periodo_campo" class="form-label">Periodo (fecha de inicio) <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control" id="periodo_campo" name="periodo_campo" required>
                                    <small class="text-muted">Se enviará en formato DD/MM/YYYY y el backend calculará el rango anual.</small>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Resumen del envío a la API</label>
                                    <div class="alert alert-light border mb-0">
                                        <p class="mb-1"><small>El CRM enviará un JSON con <code>url</code>, <code>keywords</code>, <code>competidores</code>, <code>periodo</code> y <code>ecommerce</code> a <code>/sgseo</code>.</small></p>
                                        <p class="mb-0"><small>Los campos se validan en el backend; respeta el número máximo indicado.</small></p>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-12 mb-3">
                                <label for="keywords_campo" class="form-label">Keywords (máximo 5) <span class="text-danger">*</span></label>
                                <textarea class="form-control" id="keywords_campo" name="keywords_campo" rows="2" placeholder="Escribe una keyword por línea" required></textarea>
                                <small class="text-muted">Añade entre 1 y 5 keywords relevantes (una por línea o separadas por coma).</small>
                            </div>

                            <div class="col-md-12 mb-3">
                                <label for="competidores_campo" class="form-label">Competidores (máximo 2) <span class="text-danger">*</span></label>
                                <textarea class="form-control" id="competidores_campo" name="competidores_campo" rows="2" placeholder="https://competidor1.com&#10;https://competidor2.com" required></textarea>
                                <small class="text-muted">Incluye hasta 2 URLs completas de competidores (una por línea).</small>
                            </div>
                        </div>

                        <!-- Campos para Puesto de Trabajo Seguro -->
                        <div id="campos_puesto_seguro" style="display: none;">
                            <div class="col-md-12 mb-3">
                                <label for="nombre_campo" class="form-label">Nombre <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="nombre_campo" name="nombre_campo" placeholder="Nombre completo">
                            </div>

                            <div class="col-md-12 mb-3">
                                <label for="email_campo" class="form-label">Email <span class="text-danger">*</span></label>
                                <input type="email" class="form-control" id="email_campo" name="email_campo" placeholder="email@ejemplo.com">
                            </div>

                            <div class="col-md-12 mb-3">
                                <label for="empresa_campo" class="form-label">Empresa <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="empresa_campo" name="empresa_campo" placeholder="Nombre de la empresa">
                            </div>
                        </div>

                        <!-- Campos para Presencia Avanzada (2ª) -->
                        <div id="campos_presencia_avanzada" style="display: none;">
                            <div class="col-md-6 mb-3">
                                <label for="kd_campo" class="form-label">KD <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="kd_campo" name="kd_campo" placeholder="KD">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="fecha_campo" class="form-label">Fecha <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="fecha_campo" name="fecha_campo">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="nombre_presencia_campo" class="form-label">Nombre <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="nombre_presencia_campo" name="nombre_presencia_campo" placeholder="">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="url_presencia_campo" class="form-label">URL <span class="text-danger">*</span></label>
                                <input type="url" class="form-control" id="url_presencia_campo" name="url_presencia_campo" placeholder="https://ejemplo.com">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="keyword_campo" class="form-label">Keyword Principal</label>
                                <input type="text" class="form-control" id="keyword_campo" name="keyword_campo" placeholder="Ej: interiorismo (opcional)">
                                <small class="text-muted">Opcional</small>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="phone_campo" class="form-label">Teléfono <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="phone_campo" name="phone_campo" placeholder="+34 900 00 00 00">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="email_presencia_campo" class="form-label">Email <span class="text-danger">*</span></label>
                                <input type="email" class="form-control" id="email_presencia_campo" name="email_presencia_campo" placeholder="info@ejemplo.com">
                            </div>

                            <div class="col-md-12 mb-3">
                                <label for="address_campo" class="form-label">Dirección <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="address_campo" name="address_campo" placeholder="Calle Ejemplo 123, Madrid">
                            </div>

                            <div class="col-md-12 mb-3">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <label for="descripcion_campo" class="form-label mb-0">Descripción <span class="text-danger">*</span></label>
                                    <button type="button" class="btn btn-sm btn-primary" id="btnGenerarDescripcion" onclick="generarDescripcionIA()">
                                        <i class="fas fa-magic"></i> Generar con IA
                                    </button>
                                </div>
                                <textarea class="form-control" id="descripcion_campo" name="descripcion_campo" rows="3" placeholder="Descripción de la empresa o proyecto"></textarea>
                                <small class="text-muted">
                                    <i class="fas fa-info-circle"></i> Click en "Generar con IA" para crear una descripción automática basada en los datos del formulario
                                </small>
                            </div>
                        </div>

                        <!-- Campos para CRM/ERP/Factura -->
                        <div id="campos_crm_erp_factura" style="display: none;">
                            <div class="col-md-12 mb-3">
                                <label for="tipo_sistema_campo" class="form-label">Tipo de Sistema <span class="text-danger">*</span></label>
                                <select class="form-select" id="tipo_sistema_campo" name="tipo_sistema_campo">
                                    <option value="">Seleccione tipo</option>
                                    <option value="crm">CRM</option>
                                    <option value="erp">ERP</option>
                                    <option value="factura">Factura</option>
                                </select>
                            </div>

                            <div class="col-md-12 mb-3">
                                <label for="url_crm_campo" class="form-label">URL <span class="text-danger">*</span></label>
                                <input type="url" class="form-control" id="url_crm_campo" name="url_crm_campo" placeholder="https://ejemplo.com">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="username_campo" class="form-label">Username</label>
                                <input type="text" class="form-control" id="username_campo" name="username_campo" placeholder="Usuario" value="admin">
                                <small class="text-muted">Opcional</small>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="password_campo" class="form-label">Password</label>
                                <input type="text" class="form-control" id="password_campo" name="password_campo" placeholder="Contraseña" value="12345678">
                                <small class="text-muted">Opcional - Los archivos serán generados automáticamente</small>
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

