<?php if (!usuarioLogueado()): header('Location: index.php?pagina=login'); exit; endif; ?>
<h1>Mis reservas</h1>
<div class="table-responsive"><table class="table align-middle"><thead><tr><th>Código</th><th>Trayecto</th><th>Fecha</th><th>Estado</th><th>Acción</th></tr></thead><tbody>
<tr><td>#501</td><td>Rosario → Buenos Aires</td><td>15/10/2026</td><td><span class="badge text-bg-warning">Pendiente de pago</span></td><td><button class="btn btn-sm btn-primary">Confirmar</button></td></tr>
</tbody></table></div>
