<?php
declare(strict_types=1);
http_response_code(404);
?>

<div class="row justify-content-center py-5">
    <div class="col-md-8 col-lg-6 text-center">
        <div class="card shadow-sm border-0 rounded-4 p-5 bg-white">
            <div class="rounded-circle bg-warning bg-opacity-10 text-warning mx-auto mb-3 d-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                <i class="bi bi-compass fs-1 text-danger"></i>
            </div>
            
            <h1 class="display-6 fw-bold mb-2">Página no encontrada (Error 404)</h1>
            <p class="text-muted mb-4">
                La ruta o sección a la que intentás ingresar no existe, fue movida o cambió de nombre.
            </p>

            <div class="d-flex flex-column flex-sm-row justify-content-center gap-2 mb-4">
                <a href="index.php" class="btn btn-despegar px-4 py-2" title="Volver a la página principal de Cóndor">
                    <i class="bi bi-house-door me-1"></i>Ir a la Portada
                </a>
                <a href="index.php?pagina=mapa_sitio" class="btn btn-outline-secondary px-4 py-2" title="Ver el árbol de navegación completo">
                    <i class="bi bi-diagram-3 me-1"></i>Ver Mapa del Sitio
                </a>
            </div>

            <hr class="my-4">

            <div class="text-start small text-muted bg-light p-3 rounded-3">
                <p class="fw-bold mb-1"><i class="bi bi-info-circle me-1 text-primary"></i>¿Necesitás asistencia?</p>
                <ul class="list-unstyled mb-0 d-flex flex-column gap-1">
                    <li><i class="bi bi-telephone me-2 text-secondary"></i>Atención telefónica institucional: <strong>0800-555-CONDOR (2663)</strong></li>
                    <li><i class="bi bi-envelope me-2 text-secondary"></i>Mesa de ayuda: <a href="mailto:soporte@aeroreserva.com" class="text-decoration-none">soporte@aeroreserva.com</a></li>
                    <li><i class="bi bi-geo-alt me-2 text-secondary"></i>Oficina central: Zeballos 1341, S2000BQA Rosario, Santa Fe</li>
                </ul>
            </div>
        </div>
    </div>
</div>
