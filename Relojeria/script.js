function validarFormulario() {
    const nombre = document.getElementById('nombre').value.trim();
    const email = document.getElementById('email').value.trim();
    const contrasena = document.getElementById('contrasena').value.trim();

    // Validar nombre: solo letras y espacios, mínimo 3 caracteres
    const regexNombre = /^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]{3,}$/;
    if (!regexNombre.test(nombre)) {
        alert("El nombre solo debe contener letras y al menos 3 caracteres.");
        return false;
    }

    // Validar formato de correo electrónico
    const regexEmail = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!regexEmail.test(email)) {
        alert("Por favor ingresa un correo electrónico válido.");
        return false;
    }

    // Validar contraseña (mínimo 8 caracteres, al menos una mayúscula, una minúscula y un número)
    const regexPassword = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[A-Za-z\d@$!%*?&]{8,}$/;
    if (!regexPassword.test(contrasena)) {
        alert("La contraseña debe tener al menos 8 caracteres, incluyendo mayúsculas, minúsculas y números.");
        return false;
    }

    return true;
}
