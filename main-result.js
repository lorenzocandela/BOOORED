// SERVICE WORKER CHIAMATA
if ('serviceWorker' in navigator) {
    navigator.serviceWorker.register('/service-worker.js')
    .then(function(registration) {
        console.log('Service Worker registered with scope:', registration.scope);
    }).catch(function(error) {
        console.log('Service Worker registration failed:', error);
    });
}

// CTA ONBOARDING
document.getElementById("mainBtn").addEventListener("click", function() {
    window.location.href = "./onboarding.html";
});