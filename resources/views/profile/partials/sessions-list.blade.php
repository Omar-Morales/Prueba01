@if (!empty($sessions) && count($sessions))
  <p>Si es necesario, puede cerrar sesión en todas las demás sesiones de su navegador en todos sus dispositivos. Algunas de sus sesiones recientes se enumeran a continuación...</p>
<ul class="list-group" id="sessionList">
  @foreach ($sessions as $session)
    <li class="list-group-item d-flex justify-content-between align-items-center" data-id="{{ $session['id'] }}">
      <div>
        <strong>{{ $session['agent'] }}</strong><br>
        <small>{{ $session['ip_address'] }} - {{ $session['last_active'] }}</small>
      </div>
      <div>
        @if ($session['current'])
          <span class="badge bg-success">Actual</span>
        @else
          <button class="btn btn-sm btn-danger btn-delete-session" data-id="{{ $session['id'] }}">X</button>
        @endif
      </div>
    </li>
  @endforeach
</ul>
  {{-- Mostrar botón si hay más de una sesión --}}
@if (count($sessions) > 1)
  <!-- Botón para abrir el modal -->
  <button id="btn-show-modal" type="button" class="btn btn-outline-danger btn-sm mt-3" data-bs-toggle="modal" data-bs-target="#confirmLogoutModal">
    Cerrar todas las demás sesiones
  </button>
@endif
@else
  <p class="text-muted">No hay sesiones disponibles.</p>
@endif

<!-- Modal de confirmación -->
<div class="modal fade" id="confirmLogoutModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Cerrar otras sesiones</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <div class="modal-body">
        <p>Confirma tu contraseña para cerrar todas las demás sesiones.</p>
        <input type="password" id="password-confirmations" class="form-control" placeholder="Contraseña actual">
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button id="btn-destroy-all-sessions" class="btn btn-danger">Cerrar sesiones</button>
      </div>
    </div>
  </div>
</div>


