function loadPaymentsTable() {
    const tableBody = document.getElementById('payments-table-body');
    tableBody.innerHTML = '';
    
    payments.forEach(payment => {
        const row = document.createElement('tr');
        
        // Formatear fecha
        const paymentDate = new Date(payment.payment_date);
        const formattedDate = paymentDate.toLocaleDateString('es-ES');
        
        // Formatear método de pago
        let paymentMethod = '';
        switch(payment.payment_method) {
            case 'cash': paymentMethod = 'Efectivo'; break;
            case 'credit_card': paymentMethod = 'Tarjeta crédito'; break;
            case 'debit_card': paymentMethod = 'Tarjeta débito'; break;
            case 'transfer': paymentMethod = 'Transferencia'; break;
        }
        
        row.innerHTML = `
            <td>${payment.id}</td>
            <td>${payment.member_name}</td>
            <td>$${payment.amount.toFixed(2)}</td>
            <td>${formattedDate}</td>
            <td>${paymentMethod}</td>
            <td>
                <button class="btn btn-danger delete-payment-btn" data-id="${payment.id}">Eliminar</button>
            </td>
        `;
        
        tableBody.appendChild(row);
    });
    
    // Agregar eventos a los botones de eliminar
    document.querySelectorAll('.delete-payment-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const paymentId = parseInt(this.getAttribute('data-id'));
            deletePayment(paymentId);
        });
    });
}

function deletePayment(paymentId) {
    if (confirm('¿Estás seguro de que deseas eliminar este pago?')) {
        // En un sistema real, aquí harías una llamada a la API para eliminar
        payments = payments.filter(p => p.id !== paymentId);
        
        // Actualizar la tabla
        loadPaymentsTable();
        alert('Pago eliminado correctamente');
    }
}

// Formulario para agregar pago
document.getElementById('add-payment-form').addEventListener('submit', function(e) {
    e.preventDefault();
    
    // Obtener valores del formulario
    const memberId = parseInt(document.getElementById('payment-member').value);
    const amount = parseFloat(document.getElementById('payment-amount').value);
    const paymentDate = document.getElementById('payment-date').value;
    const paymentMethod = document.getElementById('payment-method').value;
    const notes = document.getElementById('payment-notes').value;
    
    // Validar datos
    if (!memberId || isNaN(amount) || !paymentDate || !paymentMethod) {
        alert('Por favor complete todos los campos requeridos');
        return;
    }
    
    // Obtener nombre del miembro
    const member = members.find(m => m.id === memberId);
    if (!member) {
        alert('Miembro no encontrado');
        return;
    }
    
    // Crear nuevo pago (en un sistema real, esto iría a la base de datos)
    const newPayment = {
        id: payments.length > 0 ? Math.max(...payments.map(p => p.id)) + 1 : 1,
        member_id: memberId,
        member_name: member.name,
        amount: amount,
        payment_date: paymentDate,
        payment_method: paymentMethod,
        notes: notes || ''
    };
    
    payments.push(newPayment);
    
    // Limpiar formulario
    this.reset();
    
    // Mostrar mensaje de éxito
    alert('Pago registrado correctamente');
    
    // Actualizar lista de pagos
    loadPaymentsTable();
});