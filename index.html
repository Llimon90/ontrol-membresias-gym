<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gym Membership Control</title>
    <style>
        /* Estilos generales */
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Arial', sans-serif;
        }
        
        body {
            background-color: #f5f5f5;
            color: #333;
            line-height: 1.6;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        
        /* Header */
        header {
            background-color: #2c3e50;
            color: white;
            padding: 15px 0;
            margin-bottom: 30px;
        }
        
        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .logo {
            font-size: 24px;
            font-weight: bold;
        }
        
        /* Navigation */
        nav ul {
            display: flex;
            list-style: none;
        }
        
        nav ul li {
            margin-left: 20px;
        }
        
        nav ul li a {
            color: white;
            text-decoration: none;
            padding: 5px 10px;
            border-radius: 4px;
            transition: background-color 0.3s;
        }
        
        nav ul li a:hover, nav ul li a.active {
            background-color: #3498db;
        }
        
        /* Main Content */
        .main-content {
            display: flex;
            min-height: 600px;
        }
        
        .sidebar {
            width: 250px;
            background-color: white;
            border-radius: 5px;
            padding: 20px;
            margin-right: 20px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        
        .content-area {
            flex: 1;
            background-color: white;
            border-radius: 5px;
            padding: 20px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        
        /* Formularios */
        .form-group {
            margin-bottom: 15px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        
        .form-group input, 
        .form-group select, 
        .form-group textarea {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        
        .btn {
            padding: 8px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            transition: background-color 0.3s;
        }
        
        .btn-primary {
            background-color: #3498db;
            color: white;
        }
        
        .btn-primary:hover {
            background-color: #2980b9;
        }
        
        .btn-danger {
            background-color: #e74c3c;
            color: white;
        }
        
        .btn-danger:hover {
            background-color: #c0392b;
        }
        
        .btn-success {
            background-color: #27ae60;
            color: white;
        }
        
        .btn-success:hover {
            background-color: #219955;
        }
        
        /* Tablas */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        
        th, td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        
        th {
            background-color: #f4f4f4;
            font-weight: bold;
        }
        
        tr:hover {
            background-color: #f9f9f9;
        }
        
        .status {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
            display: inline-block;
        }
        
        .status-active {
            background-color: #d4edda;
            color: #155724;
        }
        
        .status-inactive {
            background-color: #f8d7da;
            color: #721c24;
        }
        
        .status-pending {
            background-color: #fff3cd;
            color: #856404;
        }
        
        /* Modal */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
        }
        
        .modal-content {
            background-color: white;
            margin: 10% auto;
            padding: 20px;
            border-radius: 5px;
            width: 80%;
            max-width: 600px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.3);
        }
        
        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 1px solid #eee;
        }
        
        .close-btn {
            font-size: 24px;
            cursor: pointer;
        }
        
        /* Alertas */
        .alert {
            padding: 10px 15px;
            margin-bottom: 20px;
            border-radius: 4px;
        }
        
        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .alert-error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .main-content {
                flex-direction: column;
            }
            
            .sidebar {
                width: 100%;
                margin-right: 0;
                margin-bottom: 20px;
            }
        }
    </style>
</head>
<body>
    <header>
        <div class="container header-content">
            <div class="logo">GymControl</div>
            <nav>
                <ul>
                    <li><a href="#" class="active" data-section="dashboard">Inicio</a></li>
                    <li><a href="#" data-section="members">Miembros</a></li>
                    <li><a href="#" data-section="payments">Pagos</a></li>
                    <li><a href="#" data-section="reports">Reportes</a></li>
                </ul>
            </nav>
        </div>
    </header>
    
    <div class="container">
        <div class="main-content">
            <div class="sidebar">
                <h3>Menú</h3>
                <ul id="sidebar-menu" style="list-style: none;">
                    <li><a href="#" data-section="dashboard">Dashboard</a></li>
                    <li><a href="#" data-section="members-list">Lista de Miembros</a></li>
                    <li><a href="#" data-section="add-member">Agregar Miembro</a></li>
                    <li><a href="#" data-section="payments-list">Lista de Pagos</a></li>
                    <li><a href="#" data-section="add-payment">Registrar Pago</a></li>
                    <li><a href="#" data-section="reports">Reportes</a></li>
                </ul>
            </div>
            
            <div class="content-area" id="content-area">
                <!-- Contenido dinámico se cargará aquí -->
                <div id="dashboard-section">
                    <h2>Dashboard</h2>
                    <p>Bienvenido al sistema de control de membresías del gimnasio.</p>
                    
                    <div class="stats-container" style="display: flex; justify-content: space-between; margin-top: 30px;">
                        <div class="stat-card" style="background: white; padding: 20px; border-radius: 5px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); width: 30%; text-align: center;">
                            <h3>Miembros Activos</h3>
                            <p id="active-members-count" style="font-size: 24px; font-weight: bold; margin: 10px 0;">0</p>
                        </div>
                        
                        <div class="stat-card" style="background: white; padding: 20px; border-radius: 5px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); width: 30%; text-align: center;">
                            <h3>Pagos del Mes</h3>
                            <p id="monthly-payments-count" style="font-size: 24px; font-weight: bold; margin: 10px 0;">0</p>
                        </div>
                        
                        <div class="stat-card" style="background: white; padding: 20px; border-radius: 5px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); width: 30%; text-align: center;">
                            <h3>Ingresos del Mes</h3>
                            <p id="monthly-income" style="font-size: 24px; font-weight: bold; margin: 10px 0;">$0</p>
                        </div>
                    </div>
                    
                    <div style="margin-top: 30px;">
                        <h3>Miembros con membresía próxima a vencer</h3>
                        <div id="expiring-members" style="margin-top: 15px;">
                            <!-- Lista de miembros se cargará aquí -->
                        </div>
                    </div>
                </div>
                
                <!-- Sección de miembros (inicialmente oculta) -->
                <div id="members-list-section" style="display: none;">
                    <h2>Lista de Miembros</h2>
                    <div style="display: flex; justify-content: space-between; margin-bottom: 20px;">
                        <input type="text" id="member-search" placeholder="Buscar miembro..." style="padding: 8px; width: 70%;">
                        <button id="refresh-members" class="btn btn-primary">Actualizar</button>
                    </div>
                    <table id="members-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nombre</th>
                                <th>Teléfono</th>
                                <th>Membresía</th>
                                <th>Fin de membresía</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="members-table-body">
                            <!-- Los miembros se cargarán aquí dinámicamente -->
                        </tbody>
                    </table>
                </div>
                
                <!-- Sección para agregar miembro (inicialmente oculta) -->
                <div id="add-member-section" style="display: none;">
                    <h2>Agregar Nuevo Miembro</h2>
                    <form id="add-member-form" style="margin-top: 20px;">
                        <div class="form-group">
                            <label for="member-name">Nombre completo</label>
                            <input type="text" id="member-name" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="member-email">Email</label>
                            <input type="email" id="member-email">
                        </div>
                        
                        <div class="form-group">
                            <label for="member-phone">Teléfono</label>
                            <input type="text" id="member-phone" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="member-photo">Foto de perfil</label>
                            <input type="file" id="member-photo" accept="image/*">
                        </div>
                        
                        <div class="form-group">
                            <label for="membership-type">Tipo de membresía</label>
                            <select id="membership-type" required>
                                <option value="">Seleccione una membresía</option>
                                <option value="1">Membresía Mensual</option>
                                <option value="2">Membresía Trimestral</option>
                                <option value="3">Membresía Anual</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="start-date">Fecha de inicio</label>
                            <input type="date" id="start-date" required>
                        </div>
                        
                        <button type="submit" class="btn btn-success">Guardar Miembro</button>
                    </form>
                </div>
                
                <!-- Sección de pagos (inicialmente oculta) -->
                <div id="payments-list-section" style="display: none;">
                    <h2>Lista de Pagos</h2>
                    <div style="margin-bottom: 20px;">
                        <input type="text" id="payment-search" placeholder="Buscar pago..." style="padding: 8px; width: 70%;">
                    </div>
                    <table id="payments-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Miembro</th>
                                <th>Monto</th>
                                <th>Fecha</th>
                                <th>Método</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="payments-table-body">
                            <!-- Los pagos se cargarán aquí dinámicamente -->
                        </tbody>
                    </table>
                </div>
                
                <!-- Sección para agregar pago (inicialmente oculta) -->
                <div id="add-payment-section" style="display: none;">
                    <h2>Registrar Nuevo Pago</h2>
                    <form id="add-payment-form" style="margin-top: 20px;">
                        <div class="form-group">
                            <label for="payment-member">Miembro</label>
                            <select id="payment-member" required>
                                <option value="">Seleccione un miembro</option>
                                <!-- Los miembros se cargarán aquí dinámicamente -->
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="payment-amount">Monto</label>
                            <input type="number" id="payment-amount" step="0.01" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="payment-date">Fecha</label>
                            <input type="date" id="payment-date" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="payment-method">Método de pago</label>
                            <select id="payment-method" required>
                                <option value="cash">Efectivo</option>
                                <option value="credit_card">Tarjeta de crédito</option>
                                <option value="debit_card">Tarjeta de débito</option>
                                <option value="transfer">Transferencia</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="payment-notes">Notas</label>
                            <textarea id="payment-notes" rows="3"></textarea>
                        </div>
                        
                        <button type="submit" class="btn btn-success">Registrar Pago</button>
                    </form>
                </div>
                
                <!-- Sección de reportes (inicialmente oculta) -->
                <div id="reports-section" style="display: none;">
                    <h2>Reportes</h2>
                    <div style="margin: 20px 0;">
                        <select id="report-type" style="padding: 8px; margin-right: 10px;">
                            <option value="monthly">Pagos mensuales</option>
                            <option value="memberships">Membresías activas</option>
                            <option value="expiring">Membresías por vencer</option>
                        </select>
                        <button id="generate-report" class="btn btn-primary">Generar Reporte</button>
                    </div>
                    
                    <div id="report-results">
                        <!-- Los resultados del reporte se mostrarán aquí -->
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Modal para editar miembro -->
    <div id="edit-member-modal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Editar Miembro</h3>
                <span class="close-btn">&times;</span>
            </div>
            <form id="edit-member-form">
                <input type="hidden" id="edit-member-id">
                <div class="form-group">
                    <label for="edit-member-name">Nombre completo</label>
                    <input type="text" id="edit-member-name" required>
                </div>
                
                <div class="form-group">
                    <label for="edit-member-email">Email</label>
                    <input type="email" id="edit-member-email">
                </div>
                
                <div class="form-group">
                    <label for="edit-member-phone">Teléfono</label>
                    <input type="text" id="edit-member-phone" required>
                </div>
                
                <div class="form-group">
                    <label for="edit-membership-type">Tipo de membresía</label>
                    <select id="edit-membership-type" required>
                        <option value="1">Membresía Mensual</option>
                        <option value="2">Membresía Trimestral</option>
                        <option value="3">Membresía Anual</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="edit-start-date">Fecha de inicio</label>
                    <input type="date" id="edit-start-date" required>
                </div>
                
                <div class="form-group">
                    <label for="edit-end-date">Fecha de fin</label>
                    <input type="date" id="edit-end-date" required>
                </div>
                
                <div class="form-group">
                    <label for="edit-member-status">Estado</label>
                    <select id="edit-member-status" required>
                        <option value="active">Activo</option>
                        <option value="inactive">Inactivo</option>
                        <option value="pending">Pendiente</option>
                    </select>
                </div>
                
                <button type="submit" class="btn btn-success">Guardar Cambios</button>
            </form>
        </div>
    </div>
    
    <!-- Modal para ver detalles de miembro -->
    <div id="view-member-modal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Detalles del Miembro</h3>
                <span class="close-btn">&times;</span>
            </div>
            <div id="member-details-content" style="padding: 20px 0;">
                <!-- Los detalles del miembro se cargarán aquí -->
            </div>
        </div>
    </div>
    
    <script>
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
        
        function updateDashboardStats() {
            // Miembros activos
            const activeMembers = members.filter(m => m.status === 'active').length;
            document.getElementById('active-members-count').textContent = activeMembers;
            
            // Pagos del mes (ejemplo: mayo 2023)
            const currentMonthPayments = payments.filter(p => {
                const paymentDate = new Date(p.payment_date);
                return paymentDate.getMonth() === 4 && paymentDate.getFullYear() === 2023;
            }).length;
            
            document.getElementById('monthly-payments-count').textContent = currentMonthPayments;
            
            // Ingresos del mes
            const monthlyIncome = payments.filter(p => {
                const paymentDate = new Date(p.payment_date);
                return paymentDate.getMonth() === 4 && paymentDate.getFullYear() === 2023;
            }).reduce((sum, payment) => sum + payment.amount, 0);
            
            document.getElementById('monthly-income').textContent = `$${monthlyIncome.toFixed(2)}`;
            
            // Miembros con membresía próxima a vencer (en los próximos 7 días)
            const today = new Date();
            const nextWeek = new Date();
            nextWeek.setDate(today.getDate() + 7);
            
            const expiringMembers = members.filter(m => {
                const endDate = new Date(m.end_date);
                return endDate >= today && endDate <= nextWeek && m.status === 'active';
            });
            
            const expiringMembersList = document.getElementById('expiring-members');
            expiringMembersList.innerHTML = '';
            
            if (expiringMembers.length === 0) {
                expiringMembersList.innerHTML = '<p>No hay membresías próximas a vencer.</p>';
            } else {
                const list = document.createElement('ul');
                expiringMembers.forEach(member => {
                    const endDate = new Date(member.end_date);
                    const formattedDate = endDate.toLocaleDateString('es-ES');
                    
                    const item = document.createElement('li');
                    item.innerHTML = `${member.name} - Vence el ${formattedDate}`;
                    list.appendChild(item);
                });
                expiringMembersList.appendChild(list);
            }
        }
        
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
        
        function deletePayment(paymentId) {
            if (confirm('¿Estás seguro de que deseas eliminar este pago?')) {
                // En un sistema real, aquí harías una llamada a la API para eliminar
                payments = payments.filter(p => p.id !== paymentId);
                
                // Actualizar la tabla
                loadPaymentsTable();
                alert('Pago eliminado correctamente');
            }
        }
        
        // Event listeners
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
            
            // Generar reporte
            document.getElementById('generate-report').addEventListener('click', function() {
                const reportType = document.getElementById('report-type').value;
                const resultsDiv = document.getElementById('report-results');
                
                if (reportType === 'monthly') {
                    // Reporte de pagos mensuales
                    const monthlyPayments = {};
                    
                    payments.forEach(payment => {
                        const paymentDate = new Date(payment.payment_date);
                        const monthYear = `${paymentDate.getMonth() + 1}/${paymentDate.getFullYear()}`;
                        
                        if (!monthlyPayments[monthYear]) {
                            monthlyPayments[monthYear] = {
                                count: 0,
                                total: 0
                            };
                        }
                        
                        monthlyPayments[monthYear].count++;
                        monthlyPayments[monthYear].total += payment.amount;
                    });
                    
                    let html = '<h3>Pagos mensuales</h3><table><thead><tr><th>Mes/Año</th><th>Cantidad de pagos</th><th>Total ingresos</th></tr></thead><tbody>';
                    
                    for (const [monthYear, data] of Object.entries(monthlyPayments)) {
                        html += `
                            <tr>
                                <td>${monthYear}</td>
                                <td>${data.count}</td>
                                <td>$${data.total.toFixed(2)}</td>
                            </tr>
                        `;
                    }
                    
                    html += '</tbody></table>';
                    resultsDiv.innerHTML = html;
                    
                } else if (reportType === 'memberships') {
                    // Reporte de membresías activas por tipo
                    const membershipCounts = {
                        'Mensual': 0,
                        'Trimestral': 0,
                        'Anual': 0
                    };
                    
                    members.forEach(member => {
                        if (member.status === 'active') {
                            membershipCounts[member.membership_name]++;
                        }
                    });
                    
                    let html = '<h3>Membresías activas por tipo</h3><table><thead><tr><th>Tipo de membresía</th><th>Cantidad</th></tr></thead><tbody>';
                    
                    for (const [type, count] of Object.entries(membershipCounts)) {
                        html += `
                            <tr>
                                <td>${type}</td>
                                <td>${count}</td>
                            </tr>
                        `;
                    }
                    
                    html += '</tbody></table>';
                    resultsDiv.innerHTML = html;
                    
                } else if (reportType === 'expiring') {
                    // Reporte de membresías por vencer (próximos 30 días)
                    const today = new Date();
                    const nextMonth = new Date();
                    nextMonth.setDate(today.getDate() + 30);
                    
                    const expiringMembers = members.filter(m => {
                        const endDate = new Date(m.end_date);
                        return endDate >= today && endDate <= nextMonth && m.status === 'active';
                    });
                    
                    let html = '<h3>Membresías por vencer (próximos 30 días)</h3>';
                    
                    if (expiringMembers.length === 0) {
                        html += '<p>No hay membresías próximas a vencer.</p>';
                    } else {
                        html += '<table><thead><tr><th>Nombre</th><th>Tipo</th><th>Fecha de fin</th></tr></thead><tbody>';
                        
                        expiringMembers.forEach(member => {
                            const endDate = new Date(member.end_date);
                            const formattedDate = endDate.toLocaleDateString('es-ES');
                            
                            html += `
                                <tr>
                                    <td>${member.name}</td>
                                    <td>${member.membership_name}</td>
                                    <td>${formattedDate}</td>
                                </tr>
                            `;
                        });
                        
                        html += '</tbody></table>';
                    }
                    
                    resultsDiv.innerHTML = html;
                }
            });
            
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
            showSection('dashboard');
            loadMembersForPayments();
        });
    </script>
    
    <?php
    // Aquí iría el código PHP para manejar las operaciones del servidor
    // Este es un ejemplo básico de cómo podrías estructurarlo
    
    // Configuración de la base de datos
    $db_host = 'localhost';
    $db_name = 'gym_db';
    $db_user = 'root';
    $db_pass = '';
    
    try {
        $pdo = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
        die("Error de conexión: " . $e->getMessage());
    }
    
    // Manejar operaciones AJAX
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
        header('Content-Type: application/json');
        
        try {
            switch ($_POST['action']) {
                case 'add_member':
                    // Validar y sanitizar datos
                    $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
                    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
                    $phone = filter_input(INPUT_POST, 'phone', FILTER_SANITIZE_STRING);
                    $membership_id = filter_input(INPUT_POST, 'membership_id', FILTER_VALIDATE_INT);
                    $start_date = filter_input(INPUT_POST, 'start_date', FILTER_SANITIZE_STRING);
                    
                    // Calcular fecha de fin según el tipo de membresía
                    $start_date_obj = new DateTime($start_date);
                    $end_date_obj = clone $start_date_obj;
                    
                    $stmt = $pdo->prepare("SELECT duration_days FROM memberships WHERE id = ?");
                    $stmt->execute([$membership_id]);
                    $membership = $stmt->fetch(PDO::FETCH_ASSOC);
                    
                    if (!$membership) {
                        throw new Exception("Tipo de membresía no válido");
                    }
                    
                    $end_date_obj->add(new DateInterval("P{$membership['duration_days']}D"));
                    $end_date = $end_date_obj->format('Y-m-d');
                    
                    // Manejar la subida de la imagen
                    $photo = 'default.jpg';
                    if (isset($_FILES['photo']) {
                        $upload_dir = 'uploads/profiles/';
                        $file_name = uniqid() . '_' . basename($_FILES['photo']['name']);
                        $target_file = $upload_dir . $file_name;
                        
                        if (move_uploaded_file($_FILES['photo']['tmp_name'], $target_file)) {
                            $photo = $file_name;
                        }
                    }
                    
                    // Insertar en la base de datos
                    $stmt = $pdo->prepare("INSERT INTO members (name, email, phone, photo, membership_id, start_date, end_date, status) 
                                          VALUES (?, ?, ?, ?, ?, ?, ?, 'active')");
                    $stmt->execute([$name, $email, $phone, $photo, $membership_id, $start_date, $end_date]);
                    
                    echo json_encode(['success' => true, 'message' => 'Miembro agregado correctamente']);
                    break;
                    
                case 'get_members':
                    $stmt = $pdo->query("
                        SELECT m.*, ms.name as membership_name 
                        FROM members m
                        JOIN memberships ms ON m.membership_id = ms.id
                        ORDER BY m.name
                    ");
                    $members = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    echo json_encode($members);
                    break;
                    
                // Más casos para otras operaciones...
                
                default:
                    echo json_encode(['success' => false, 'message' => 'Acción no válida']);
            }
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        
        exit;
    }
    ?>
</body>
</html>