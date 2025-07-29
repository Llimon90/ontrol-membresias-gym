// members.js
document.addEventListener('DOMContentLoaded', () => {
  fetchMembers();

  document.getElementById('form-create').addEventListener('submit', async e => {
    e.preventDefault();
    const fd = new FormData(e.target);
    const resp = await fetch('backend/members.php?action=create', { method: 'POST', body: fd });
    const data = await resp.json();
    if (data.success) {
      e.target.reset();
      fetchMembers();
      alert('Miembro creado correctamente');
    } else {
      alert('Error: ' + data.message);
    }
  });
});

async function fetchMembers() {
  const resp = await fetch('backend/members.php?action=list');
  const list = await resp.json();
  const tbody = document.getElementById('members-body');
  tbody.innerHTML = '';
  list.forEach(m => {
    const tr = document.createElement('tr');
    tr.innerHTML = `
    
      <td>${m.name}</td>
      <td>${m.phone}</td>
      <td>${m.membership_name}</td>
      <td>${m.start_date}</td>
      <td>${m.end_date}</td>`;
    tbody.appendChild(tr);
  });
}
