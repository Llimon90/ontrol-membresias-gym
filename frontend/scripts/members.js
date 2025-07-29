async function loadMembersTable() {
    try {
        const response = await fetch('backend/api/members.php');
        const members = await response.json();
        
        const tableBody = document.getElementById('members-table-body');
        tableBody.innerHTML = '';
        
        members.forEach(member => {
            // Resto del código para crear filas...
        });
    } catch (error) {
        console.error('Error al cargar miembros:', error);
    }
}

async function deleteMember(memberId) {
    if (confirm('¿Estás seguro de que deseas eliminar este miembro?')) {
        try {
            const response = await fetch(`backend/api/members.php?id=${memberId}`, {
                method: 'DELETE'
            });
            const result = await response.json();
            
            if (result.success) {
                loadMembersTable();
                alert('Miembro eliminado correctamente');
            }
        } catch (error) {
            console.error('Error al eliminar miembro:', error);
        }
    }
}