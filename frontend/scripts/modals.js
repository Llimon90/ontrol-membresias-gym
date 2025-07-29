function viewMemberDetails(memberId) {
    const member = members.find(m => m.id === memberId);
    if (!member) return;
    
    const modal = document.getElementById('view-member-modal');
    const content = document.getElementById('member-details-content');
    
    const endDate = new Date(member.end_date);
    const formattedEndDate = endDate.toLocaleDateString('es-ES');
    
    const startDate = new Date(member.start_date);
    const formattedStartDate = startDate.toLocaleDateString('es-ES');
    
    content.innerHTML = `
        <div style="display: flex; margin-bottom: 20px;">
            <div style="margin-right: 20px;">
                <img src="uploads/profiles/${member.photo || 'default.jpg'}" alt="${member.name}" style="width: 150px; height: 150px; border-radius: 50%; object-fit: cover;">
            </div>
            <div>
                <h3>${member.name}</h3>
                <p><strong>Email:</strong> ${member.email || 'No especificado'}</p>
                <p><strong>Teléfono:</strong> ${member.phone}</p>
                <p><strong>Membresía:</strong> ${member.membership_name}</p>
            </div>
        </div>
        
        <div style="margin-bottom: 20px;">
            <h4>Información de la membresía</h4>
            <p><strong>Fecha de inicio:</strong> ${formattedStartDate}</p>
            <p><strong>Fecha de fin:</strong> ${formattedEndDate}</p>
            <p><strong>Estado:</strong> <span class="status status-${member.status.toLowerCase()}">${member.status}</span></p>
        </div>
        
        <div>
            <h4>Historial de pagos</h4>
            <table style="width: 100%;">
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Monto</th>
                        <th>Método</th>
                    </tr>
                </thead>
                <tbody>
                    ${payments.filter(p => p.member_id === memberId)
                        .map(p => {
                            const paymentDate = new Date(p.payment_date);
                            const formattedDate = paymentDate.toLocaleDateString('es-ES');
                            
                            let paymentMethod = '';
                            switch(p.payment_method) {
                                case 'cash': paymentMethod = 'Efectivo'; break;
                                case 'credit_card': paymentMethod = 'Tarjeta crédito'; break;
                                case 'debit_card': paymentMethod = 'Tarjeta débito'; break;
                                case 'transfer': paymentMethod = 'Transferencia'; break;
                            }
                            
                            return `
                                <tr>
                                    <td>${formattedDate}</td>
                                    <td>$${p.amount.toFixed(2)}</td>
                                    <td>${paymentMethod}</td>
                                </tr>
                            `;
                        }).join('')}
                </tbody>
            </table>
        </div>
    `;
    
    modal.style.display = 'block';
}

function openEditMemberModal(memberId) {
    const member = members.find(m => m.id === memberId);
    if (!member) return;
    
    document.getElementById('edit-member-id').value = member.id;
    document.getElementById('edit-member-name').value = member.name;
    document.getElementById('edit-member-email').value = member.email || '';
    document.getElementById('edit-member-phone').value = member.phone;
    document.getElementById('edit-membership-type').value = member.membership_id;
    document.getElementById('edit-start-date').value = member.start_date;
    document.getElementById('edit-end-date').value = member.end_date;
    document.getElementById('edit-member-status').value = member.status.toLowerCase();
    
    document.getElementById('edit-member-modal').style.display = 'block';
}

// Formulario para editar miembro
document.getElementById('edit-member-form').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const memberId = parseInt(document.getElementById('edit-member-id').value);
    const name = document.getElementById('edit-member-name').value;
    const email = document.getElementById('edit-member-email').value;
    const phone = document.getElementById('edit-member-phone').value;
    const membershipId = parseInt(document.getElementById('edit-membership-type').value);
    const startDate = document.getElementById('edit-start-date').value;
    const endDate = document.getElementById('edit-end-date').value;
    const status = document.getElementById('edit-member-status').value;
    
    // Validar datos
    if (!name || !phone || !membershipId || !startDate || !endDate || !status) {
        alert('Por favor complete todos los campos requeridos');
        return;
    }
    
    // Actualizar miembro (en un sistema real, esto iría a la base de datos)
    const memberIndex = members.findIndex(m => m.id === memberId);
    if (memberIndex !== -1) {
        // Determinar nombre de membresía
        let membershipName = '';
        switch(membershipId) {
            case 1: membershipName = 'Mensual'; break;
            case 2: membershipName = 'Trimestral'; break;
            case 3: membershipName = 'Anual'; break;
        }
        
        members[memberIndex] = {
            ...members[memberIndex],
            name: name,
            email: email || null,
            phone: phone,
            membership_id: membershipId,
            membership_name: membershipName,
            start_date: startDate,
            end_date: endDate,
            status: status
        };
        
        // Cerrar modal
        document.getElementById('edit-member-modal').style.display = 'none';
        
        // Mostrar mensaje de éxito
        alert('Miembro actualizado correctamente');
        
        // Actualizar lista de miembros
        loadMembersTable();
    }
});

// Cerrar modales
document.querySelectorAll('.close-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        this.closest('.modal').style.display = 'none';
    });
});

// Cerrar modales al hacer clic fuera
window.addEventListener('click', function(e) {
    if (e.target.className === 'modal') {
        e.target.style.display = 'none';
    }
});