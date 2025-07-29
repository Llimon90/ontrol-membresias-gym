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