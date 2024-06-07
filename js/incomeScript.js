document.addEventListener("DOMContentLoaded", function() {
    // Loading screen
    setTimeout(() => {
        document.querySelector('.loading-screen').style.display = 'none';
    }, 1000);

    updateTimeAndRates();

    var form = document.getElementById('incomeForm');
    form.addEventListener('submit', function(event) {
        event.preventDefault();
        var incomeAmount = document.getElementById('incomeAmount').value;

        var formData = new FormData();
        formData.append('action', 'addIncome');
        formData.append('incomeAmount', incomeAmount);

        fetch('', {
            method: 'POST',
            body: formData
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    var incomeTableBody = document.getElementById('incomeTableBody');
                    var newRow = incomeTableBody.insertRow();
                    newRow.insertCell(0).innerText = data.id;
                    newRow.insertCell(1).innerText = incomeAmount;

                    document.getElementById('incomeForm').reset();
                    alert('Income Added: ' + data.id + ' - ' + incomeAmount);

                    // Update balance
                    var userBalanceElement = document.getElementById('userBalance');
                    userBalanceElement.innerText = data.balance + ' UAH';
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => console.error('Error:', error));
    });

    AOS.init();
});

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