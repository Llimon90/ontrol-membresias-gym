document.getElementById('add-member-form').addEventListener('submit', function(e) {
    e.preventDefault();
    
    // Obtener los valores del formulario
    const name = document.getElementById('member-name').value;
    const email = document.getElementById('member-email').value;
    const phone = document.getElementById('member-phone').value;
    const membershipId = document.getElementById('membership-type').value;
    const startDate = document.getElementById('start-date').value;
    const photoFile = document.getElementById('member-photo').files[0];
    
    // Validación básica
    if (!name || !phone || !membershipId || !startDate) {
        alert('Por favor complete los campos obligatorios');
        return;
    }
    
    // Crear FormData para enviar datos incluyendo el archivo
    const formData = new FormData();
    formData.append('name', name);
    formData.append('email', email);
    formData.append('phone', phone);
    formData.append('membership_id', membershipId);
    formData.append('start_date', startDate);
    if (photoFile) {
        formData.append('photo', photoFile);
    }
    
    // Enviar datos al servidor
    fetch('add_member.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Miembro agregado exitosamente');
            // Limpiar el formulario
            document.getElementById('add-member-form').reset();
            // Opcional: ocultar la sección después de agregar
            document.getElementById('add-member-section').style.display = 'none';
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Ocurrió un error al enviar los datos');
    });
});