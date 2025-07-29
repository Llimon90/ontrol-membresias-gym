// Datos de ejemplo (en un sistema real, estos vendrían de una API o base de datos)
let members = [
    {
        id: 1,
        name: "Juan Pérez",
        email: "juan@example.com",
        phone: "555-1234",
        photo: "member1.jpg",
        membership_id: 1,
        membership_name: "Mensual",
        start_date: "2023-05-01",
        end_date: "2023-06-01",
        status: "active"
    },
    {
        id: 2,
        name: "María García",
        email: "maria@example.com",
        phone: "555-5678",
        photo: "member2.jpg",
        membership_id: 2,
        membership_name: "Trimestral",
        start_date: "2023-04-15",
        end_date: "2023-07-15",
        status: "active"
    },
    {
        id: 3,
        name: "Carlos López",
        email: "carlos@example.com",
        phone: "555-9012",
        photo: "member3.jpg",
        membership_id: 3,
        membership_name: "Anual",
        start_date: "2023-01-10",
        end_date: "2024-01-10",
        status: "active"
    }
];

let payments = [
    {
        id: 1,
        member_id: 1,
        member_name: "Juan Pérez",
        amount: 500.00,
        payment_date: "2023-05-01",
        payment_method: "cash",
        notes: "Pago mensual"
    },
    {
        id: 2,
        member_id: 2,
        member_name: "María García",
        amount: 1200.00,
        payment_date: "2023-04-15",
        payment_method: "credit_card",
        notes: "Pago trimestral"
    },
    {
        id: 3,
        member_id: 3,
        member_name: "Carlos López",
        amount: 5000.00,
        payment_date: "2023-01-10",
        payment_method: "transfer",
        notes: "Pago anual"
    }
];

// Funciones para manejar la interfaz
function showSection(sectionId) {
    // Ocultar todas las secciones
    document.querySelectorAll('#content-area > div').forEach(div => {
        div.style.display = 'none';
    });
    
    // Mostrar la sección seleccionada
    document.getElementById(sectionId + '-section').style.display = 'block';
    
    // Actualizar el contenido si es necesario
    if (sectionId === 'members-list') {
        loadMembersTable();
    } else if (sectionId === 'payments-list') {
        loadPaymentsTable();
    } else if (sectionId === 'dashboard') {
        updateDashboardStats();
    }
}

// Cargar miembros en el select de pagos
function loadMembersForPayments() {
    const select = document.getElementById('payment-member');
    select.innerHTML = '<option value="">Seleccione un miembro</option>';
    
    members.forEach(member => {
        const option = document.createElement('option');
        option.value = member.id;
        option.textContent = member.name;
        select.appendChild(option);
    });
}

// Inicializar la aplicación
document.addEventListener('DOMContentLoaded', function() {
    // Navegación principal
    document.querySelectorAll('nav a').forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const section = this.getAttribute('data-section');
            showSection(section);
            
            // Actualizar clase activa
            document.querySelectorAll('nav a').forEach(a => a.classList.remove('active'));
            this.classList.add('active');
        });
    });
    
    // Menú lateral
    document.querySelectorAll('#sidebar-menu a').forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const section = this.getAttribute('data-section');
            showSection(section);
        });
    });
    
    // Inicializar la aplicación
    showSection('dashboard');
    loadMembersForPayments();
});