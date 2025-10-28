<div class="alert alert-warning">
    <strong>Recuerde {{ Auth::user()->name }}!</strong><br>
    Al cambiar su contraseña, se cerrará su sesión y deberá iniciar sesión nuevamente con su nueva contraseña.
</div>

<form id="passwordForm">
    <!-- Contraseña Actual -->
    <div class="mb-3">
        <label class="form-label">Contraseña Actual</label>
        <div class="position-relative">
            <input type="password" name="current_password" id="current_password" class="form-control" autocomplete="current-password">
            <span class="btn btn-sm btn-icon position-absolute translate-middle top-50 end-0 me-n2"
                onclick="togglePasswordVisibility('current_password')">
                <i id="eye-icon-current_password" class="ri-eye-off-line fs-4 text-body"></i>
                <i id="eye-icon-current_password-show" class="ri-eye-line fs-4 d-none text-body"></i>
            </span>
        </div>
    </div>

    <!-- Nueva Contraseña -->
    <div class="mb-3">
        <label class="form-label">Nueva Contraseña</label>
        <div class="position-relative">
            <input type="password" name="password" id="password" class="form-control" autocomplete="new-password">
            <span class="btn btn-sm btn-icon position-absolute translate-middle top-50 end-0 me-n2"
                onclick="togglePasswordVisibility('password')">
            <i id="eye-icon-password" class="ri-eye-off-line fs-4 text-body"></i>
            <i id="eye-icon-password-show" class="ri-eye-line fs-4 d-none text-body"></i>
            </span>
        </div>
        <small class="text-muted">Debe contener al menos 8 caracteres, incluyendo letras, números y símbolos.</small>
    </div>

    <!-- Confirmar Contraseña -->
    <div class="mb-3">
        <label class="form-label">Confirmar Contraseña</label>
        <div class="position-relative">
            <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" autocomplete="confirmation-password">
            <span class="btn btn-sm btn-icon position-absolute translate-middle top-50 end-0 me-n2"
                onclick="togglePasswordVisibility('password_confirmation')">
                <i id="eye-icon-password_confirmation" class="ri-eye-off-line fs-4 text-body"></i>
                <i id="eye-icon-password_confirmation-show" class="ri-eye-line fs-4 d-none text-body"></i>
            </span>
        </div>
    </div>

    <div class="text-end">
        <button type="submit" id="passwordSubmit" class="btn btn-success">Actualizar contraseña</button>
    </div>
</form>


<script>
    // Función para alternar visibilidad de la contraseña
function togglePasswordVisibility(id) {
    var passwordField = document.getElementById(id);
    var eyeIcon = document.getElementById('eye-icon-' + id);
    var eyeIconShow = document.getElementById('eye-icon-' + id + '-show');

    console.log(passwordField, eyeIcon, eyeIconShow); // Verifica si los elementos están bien seleccionados

    if (passwordField.type === "password") {
        passwordField.type = "text";
        eyeIcon.classList.add('d-none');
        eyeIconShow.classList.remove('d-none');
    } else {
        passwordField.type = "password";
        eyeIcon.classList.remove('d-none');
        eyeIconShow.classList.add('d-none');
    }
}


</script>
