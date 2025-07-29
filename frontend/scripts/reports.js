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