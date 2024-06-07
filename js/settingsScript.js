document.addEventListener("DOMContentLoaded", function() {
    // Loading screen
    setTimeout(() => {
        document.querySelector('.loading-screen').style.display = 'none';
    }, 1000);

    updateTimeAndRates();

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

document.getElementById('logoutButton').addEventListener('click', function() {
    fetch('logout.php')
        .then(() => {
            window.location.href = 'login.php';
        })
        .catch(error => console.error('Logout failed:', error));
});

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