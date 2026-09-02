document.querySelectorAll('[data-validar]').forEach((formulario) => {
  formulario.addEventListener('submit', (evento) => {
    if (!formulario.checkValidity()) {
      evento.preventDefault();
      formulario.classList.add('was-validated');
    }
  });
});
