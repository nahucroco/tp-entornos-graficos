<?php
declare(strict_types=1);

/**
 * Componente de paginación numérica reutilizable (Bootstrap 5.3 + WAI-AA).
 */

function renderizarPaginador(
    int $paginaActual,
    int $totalPaginas,
    string $paginaRuta,
    array $parametrosExtra = []
): string {
    if ($totalPaginas <= 1) {
        return '';
    }

    $construirUrl = function (int $p) use ($paginaRuta, $parametrosExtra): string {
        $params = array_merge($parametrosExtra, ['pagina' => $paginaRuta, 'p' => $p]);
        return 'index.php?' . http_build_query($params);
    };

    $html = '<nav aria-label="Paginación de resultados" class="my-4">';
    $html .= '<ul class="pagination justify-content-center flex-wrap mb-0 shadow-sm">';

    // Botón Anterior
    if ($paginaActual > 1) {
        $html .= '<li class="page-item">';
        $html .= '<a class="page-link" href="' . htmlspecialchars($construirUrl($paginaActual - 1)) . '" aria-label="Página anterior">&laquo; Anterior</a>';
        $html .= '</li>';
    } else {
        $html .= '<li class="page-item disabled"><span class="page-link" aria-disabled="true">&laquo; Anterior</span></li>';
    }

    // Ventana deslizante de números
    $rango = 2;
    $inicio = max(1, $paginaActual - $rango);
    $fin = min($totalPaginas, $paginaActual + $rango);

    if ($inicio > 1) {
        $html .= '<li class="page-item"><a class="page-link" href="' . htmlspecialchars($construirUrl(1)) . '">1</a></li>';
        if ($inicio > 2) {
            $html .= '<li class="page-item disabled"><span class="page-link">&hellip;</span></li>';
        }
    }

    for ($i = $inicio; $i <= $fin; $i++) {
        if ($i === $paginaActual) {
            $html .= '<li class="page-item active" aria-current="page"><span class="page-link fw-bold">' . $i . '</span></li>';
        } else {
            $html .= '<li class="page-item"><a class="page-link" href="' . htmlspecialchars($construirUrl($i)) . '">' . $i . '</a></li>';
        }
    }

    if ($fin < $totalPaginas) {
        if ($fin < $totalPaginas - 1) {
            $html .= '<li class="page-item disabled"><span class="page-link">&hellip;</span></li>';
        }
        $html .= '<li class="page-item"><a class="page-link" href="' . htmlspecialchars($construirUrl($totalPaginas)) . '">' . $totalPaginas . '</a></li>';
    }

    // Botón Siguiente
    if ($paginaActual < $totalPaginas) {
        $html .= '<li class="page-item">';
        $html .= '<a class="page-link" href="' . htmlspecialchars($construirUrl($paginaActual + 1)) . '" aria-label="Página siguiente">Siguiente &raquo;</a>';
        $html .= '</li>';
    } else {
        $html .= '<li class="page-item disabled"><span class="page-link" aria-disabled="true">Siguiente &raquo;</span></li>';
    }

    $html .= '</ul>';
    $html .= '</nav>';

    return $html;
}
