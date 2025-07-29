document.addEventListener('DOMContentLoaded', function() {
    loadMembers();
    
    // Configurar búsqueda
    document.getElementById('search-btn').addEventListener('click', function() {
        const searchTerm = document.getElementById('search-input').value;
        searchMembers(searchTerm);
    });
});

function loadMembers() {
    fetch('../../backend/api/members.php?action=read')
        .then(response => response.json())
        .then(data => {
            const tableBody = document.getElementById('members-list');
            tableBody.innerHTML = '';
            
            data.forEach(member => {
                const row = document.createElement('tr');
                
                row.innerHTML = `
                    <td><img src="../../uploads/profiles/${member.photo || 'default.jpg'}" alt="${member.name}" class="profile-img"></td>
                    <td>${member.name}</td>
                    <td>${member.email}</td>
                    <td>${member.phone}</td>
                    <td>${member.membership_name}</td>
                    <td>${formatDate(member.end_date)}</td>
                    <td><span class="status ${member.status.toLowerCase()}">${member.status}</span></td>
                    <td>
                        <a href="edit.html?id=${member.id}" class="btn-edit">Editar</a>
                        <button class="btn-delete" data-id="${member.id}">Eliminar</button>
                    </td>
                `;
                
                tableBody.appendChild(row);
            });
            
            // Agregar eventos a los botones de eliminar
            document.querySelectorAll('.btn-delete').forEach(button => {
                button.addEventListener('click', function() {
                    const memberId = this.getAttribute('data-id');
                    deleteMember(memberId);
                });
            });
        })
        .catch(error => console.error('Error:', error));
}

function searchMembers(term) {
    // Implementar búsqueda
}

function deleteMember(id) {
    if (confirm('¿Estás seguro de que deseas eliminar este miembro?')) {
        fetch(`../../backend/api/members.php?action=delete&id=${id}`, {
            method: 'DELETE'
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Miembro eliminado correctamente');
                loadMembers();
            } else {
                alert('Error al eliminar miembro');
            }
        })
        .catch(error => console.error('Error:', error));
    }
}