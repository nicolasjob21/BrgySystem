document.addEventListener('DOMContentLoaded', () => {
    const residentForm = document.getElementById('residentForm');
    const reportTableBody = document.getElementById('reportTableBody');
    
    residentForm.addEventListener('submit', (event) => {
        event.preventDefault();
        
        const name = event.target.name.value;
        const medication = event.target.medication.value;
        const stock = event.target.stock.value;
        
        addEntryToReport(name, medication, stock);
        
        residentForm.reset();
    });
    
    function addEntryToReport(name, medication, stock) {
        const row = document.createElement('tr');
        
        const nameCell = document.createElement('td');
        nameCell.textContent = name;
        row.appendChild(nameCell);
        
        const medicationCell = document.createElement('td');
        medicationCell.textContent = medication;
        row.appendChild(medicationCell);
        
        const stockCell = document.createElement('td');
        stockCell.textContent = stock;
        row.appendChild(stockCell);
        
        reportTableBody.appendChild(row);
    }
});
