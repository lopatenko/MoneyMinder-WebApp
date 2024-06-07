document.addEventListener("DOMContentLoaded", function() {
    updateTimeAndRates();
    loadExpenseData();

    var form = document.getElementById('expenseForm');
    form.addEventListener('submit', function(event) {
        event.preventDefault();

        var expenseName = document.getElementById('expenseName').value;
        var expenseAmount = document.getElementById('expenseAmount').value;
        var expenseCategory = document.getElementById('expenseCategory').value;

        var formData = new FormData();
        formData.append('action', 'addExpense');
        formData.append('expenseName', expenseName);
        formData.append('expenseAmount', expenseAmount);
        formData.append('expenseCategory', expenseCategory);

        fetch('expenses.php', {
            method: 'POST',
            body: formData
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    var expenseTableBody = document.getElementById('expenseTableBody');
                    var newRow = expenseTableBody.insertRow();
                    newRow.insertCell(0).innerText = expenseName;
                    newRow.insertCell(1).innerText = expenseAmount;
                    newRow.insertCell(2).innerText = expenseCategory;

                    document.getElementById('expenseForm').reset();
                    alert('Expense Added: ' + expenseName + ' - ' + expenseAmount + ' - ' + expenseCategory);
                    loadExpenseData();  // Reload chart data after adding a new expense
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => console.error('Error:', error));
    });

    var searchCategorySelect = document.getElementById('searchCategory');
    searchCategorySelect.addEventListener('change', function() {
        var filter = searchCategorySelect.value;
        var expenseTableBody = document.getElementById('expenseTableBody');
        var rows = expenseTableBody.getElementsByTagName('tr');
        for (var i = 0; i < rows.length; i++) {
            var categoryCell = rows[i].getElementsByTagName('td')[2];
            if (categoryCell) {
                var categoryText = categoryCell.textContent || categoryCell.innerText;
                if (filter === '' || categoryText === filter) {
                    rows[i].style.display = '';
                } else {
                    rows[i].style.display = 'none';
                }
            }
        }
    });

    AOS.init();
});

function loadExpenseData() {
    fetch('expenses.php?action=getExpenseData')
        .then(response => response.json())
        .then(data => {
            var labels = data.map(item => item.category);
            var values = data.map(item => parseFloat(item.total));

            var ctx = document.getElementById('spendingChart').getContext('2d');
            var spendingChart = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Expenses',
                        data: values,
                        backgroundColor: [
                            'rgba(255, 99, 132, 0.2)',
                            'rgba(54, 162, 235, 0.2)',
                            'rgba(255, 206, 86, 0.2)',
                            'rgba(75, 192, 192, 0.2)',
                            'rgba(153, 102, 255, 0.2)',
                            'rgba(255, 159, 64, 0.2)'
                        ],
                        borderColor: [
                            'rgba(255, 99, 132, 1)',
                            'rgba(54, 162, 235, 1)',
                            'rgba(255, 206, 86, 1)',
                            'rgba(75, 192, 192, 1)',
                            'rgba(153, 102, 255, 1)',
                            'rgba(255, 159, 64, 1)'
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'top',
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    var label = context.label || '';
                                    var value = context.raw || 0;
                                    return label + ': ' + value + ' UAH';
                                }
                            }
                        }
                    }
                }
            });
        })
        .catch(error => console.error('Error fetching expense data:', error));
}

function updateTimeAndRates() {
    var currentTimeElement = document.getElementById("currentTime");
    setInterval(() => {
        var now = new Date();
        currentTimeElement.innerText = now.toLocaleTimeString();
    }, 1000);

    fetch('https://api.exchangerate-api.com/v4/latest/USD')
        .then(response => response.json())
        .then(data => {
            document.getElementById("usdRate").innerText = (data.rates.UAH).toFixed(2);
            document.getElementById("eurRate").innerText = (data.rates.UAH / data.rates.EUR).toFixed(2);
        })
        .catch(error => console.error('Error fetching exchange rates:', error));
}

function toggleMenu() {
    var leftSidebar = document.getElementById("leftSidebar");
    var mainContentArea = document.getElementById("mainContentArea");
    leftSidebar.classList.toggle('closed');
    if (leftSidebar.classList.contains('closed')) {
        mainContentArea.classList.remove('expanded');
        mainContentArea.classList.add('collapsed');
    } else {
        mainContentArea.classList.add('expanded');
        mainContentArea.classList.remove('collapsed');
    }
}