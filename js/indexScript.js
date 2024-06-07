document.addEventListener("DOMContentLoaded", function() {
    // Loading screen
    setTimeout(() => {
        document.querySelector('.loading-screen').style.display = 'none';
    }, 1000);

    updateTimeAndRates();

    AOS.init();

    var modal = document.getElementById("myModal");
    var span = document.getElementsByClassName("close")[0];

    span.onclick = function() {
        modal.style.display = "none";
    }
    window.onclick = function(event) {
        if (event.target == modal) {
            modal.style.display = "none";
        }
    }

    document.querySelector('.faq-section').addEventListener('click', function(e) {
        if (e.target.classList.contains('faq-question')) {
            var answer = e.target.nextElementSibling;
            answer.style.display = answer.style.display === 'block' ? 'none' : 'block';
        }
    });

    var animation = lottie.loadAnimation({
        container: document.getElementById('lottieIcon'),
        renderer: 'svg',
        loop: true,
        autoplay: true,
        path: 'path/to/your/lottie/animation.json' // путь к вашему JSON файлу с анимацией
    });

    var currentSlide = 0;
    function moveSlide(step) {
        var slides = document.querySelector('.slides');
        var slidesCount = slides.children.length;
        currentSlide = (currentSlide + step + slidesCount) % slidesCount;
        slides.style.transform = 'translateX(-' + (currentSlide * 100) + '%)';
    }
    document.querySelector('.prev').onclick = function() { moveSlide(-1); }
    document.querySelector('.next').onclick = function() { moveSlide(1); }
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