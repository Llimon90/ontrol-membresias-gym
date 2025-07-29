document.getElementById('add-member-form').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    try {
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
        
        // Crear FormData
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
        const response = await fetch('../backend/add_member.php', {
            method: 'POST',
            body: formData
        });
        
        if (!response.ok) {
            throw new Error(`Error HTTP! estado: ${response.status}`);
        }
        
        const data = await response.json();
        
        if (data.success) {
            alert('Miembro agregado exitosamente');
            document.getElementById('add-member-form').reset();
            document.getElementById('add-member-section').style.display = 'none';
        } else {
            alert('Error: ' + data.message);
        }
    } catch (error) {
        console.error('Error en la solicitud:', error);
        alert('Error al enviar los datos: ' + error.message);
    }
});