function loadMembersTable() {
    const tableBody = document.getElementById('members-table-body');
    tableBody.innerHTML = '';
    
    members.forEach(member => {
        const row = document.createElement('tr');
        
        // Formatear fecha
        const endDate = new Date(member.end_date);
        const formattedDate = endDate.toLocaleDateString('es-ES');
        
        // Determinar clase de estado
        let statusClass = 'status-' + member.status.toLowerCase();
        
        row.innerHTML = `
            <td>${member.id}</td>
            <td>${member.name}</td>
            <td>${member.phone}</td>
            <td>${member.membership_name}</td>
            <td>${formattedDate}</td>
            <td><span class="status ${statusClass}">${member.status}</span></td>
            <td>
                <button class="btn btn-primary view-member-btn" data-id="${member.id}">Ver</button>
                <button class="btn btn-primary edit-member-btn" data-id="${member.id}">Editar</button>
                <button class="btn btn-danger delete-member-btn" data-id="${member.id}">Eliminar</button>
            </td>
        `;
        
        tableBody.appendChild(row);
    });
    
    // Agregar eventos a los botones
    document.querySelectorAll('.view-member-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const memberId = parseInt(this.getAttribute('data-id'));
            viewMemberDetails(memberId);
        });
    });
    
    document.querySelectorAll('.edit-member-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const memberId = parseInt(this.getAttribute('data-id'));
            openEditMemberModal(memberId);
        });
    });
    
    document.querySelectorAll('.delete-member-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const memberId = parseInt(this.getAttribute('data-id'));
            deleteMember(memberId);
        });
    });
}

function deleteMember(memberId) {
    if (confirm('¿Estás seguro de que deseas eliminar este miembro?')) {
        // En un sistema real, aquí harías una llamada a la API para eliminar
        members = members.filter(m => m.id !== memberId);
        payments = payments.filter(p => p.member_id !== memberId);
        
        // Actualizar la tabla
        loadMembersTable();
        alert('Miembro eliminado correctamente');
    }
}

// Botón para actualizar miembros
document.getElementById('refresh-members').addEventListener('click', function() {
    loadMembersTable();
});

// Formulario para agregar miembro
document.getElementById('add-member-form').addEventListener('submit', function(e) {
    e.preventDefault();
    
    // Obtener valores del formulario
    const name = document.getElementById('member-name').value;
    const email = document.getElementById('member-email').value;
    const phone = document.getElementById('member-phone').value;
    const membershipId = parseInt(document.getElementById('membership-type').value);
    const startDate = document.getElementById('start-date').value;
    
    // Validar datos
    if (!name || !phone || !membershipId || !startDate) {
        alert('Por favor complete todos los campos requeridos');
        return;
    }
    
    // Determinar tipo de membresía y fecha de fin
    let membershipName = '';
    let endDate = new Date(startDate);
    
    switch(membershipId) {
        case 1: // Mensual
            membershipName = 'Mensual';
            endDate.setMonth(endDate.getMonth() + 1);
            break;
        case 2: // Trimestral
            membershipName = 'Trimestral';
            endDate.setMonth(endDate.getMonth() + 3);
            break;
        case 3: // Anual
            membershipName = 'Anual';
            endDate.setFullYear(endDate.getFullYear() + 1);
            break;
    }
    
    // Crear nuevo miembro (en un sistema real, esto iría a la base de datos)
    const newMember = {
        id: members.length > 0 ? Math.max(...members.map(m => m.id)) + 1 : 1,
        name: name,
        email: email || null,
        phone: phone,
        photo: 'default.jpg', // En un sistema real, subirías la imagen
        membership_id: membershipId,
        membership_name: membershipName,
        start_date: startDate,
        end_date: endDate.toISOString().split('T')[0],
        status: 'active'
    };
    
    members.push(newMember);
    
    // Limpiar formulario
    this.reset();
    
    // Mostrar mensaje de éxito
    alert('Miembro agregado correctamente');
    
    // Actualizar lista de miembros
    loadMembersTable();
});