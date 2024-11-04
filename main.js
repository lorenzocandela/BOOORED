// SERVICE WORKER CHIAMATA
if ('serviceWorker' in navigator) {
    navigator.serviceWorker.register('/service-worker.js')
    .then(function(registration) {
        console.log('Service Worker registered with scope:', registration.scope);
    }).catch(function(error) {
        console.log('Service Worker registration failed:', error);
    });
}

// GLOBAL
var currentStep = 1;
const totalSteps = 11;

// PER TROVARE STEP IN BASE A ID
function getAnswerForCurrentStep() {
    const visibleDiv = document.querySelector('.question-start:not(.hidden)');
    const currentStepId = visibleDiv ? visibleDiv.id : '';
    
    let answerText = '';

    if (currentStepId === 'STEP3') { // AGGIUNTO STEP 3 CON TEXTARE (DA TESTARE)
        answerText = document.getElementById('answer3').value.trim();
        console.log("DOMANDA ", currentStepId); 
    } else if (currentStepId === 'STEP8') {
        answerText = document.getElementById('answer8').value.trim();
        console.log("DOMANDA ", currentStepId);
    } else if (currentStepId === 'STEP9') {
        answerText = document.getElementById('answer9').value.trim();
        console.log("DOMANDA ", currentStepId);
    } else if (currentStepId === 'STEP10') {
        answerText = document.getElementById('answer10').value.trim();
        console.log("DOMANDA ", currentStepId);
    } else {
        const selectedOption = document.querySelector(`#${currentStepId} .opzione-attiva`);
        answerText = selectedOption ? selectedOption.textContent.trim() : '';
        console.log("DOMANDA ", currentStepId);
    }

    console.log(`Answer for ${currentStepId}: ${answerText}`); //debugga
    return answerText;
}

// ANIMAZIONE TRA I VARI STEP / CAMBIO STEP / GESTIONE USERNAME DB / SALVATAGGIO DATI DB
function checkUserNameExists(userName, callback) {
    var xhr = new XMLHttpRequest();
    xhr.open('POST', './check_user_name.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.onload = function() {
        if (xhr.status === 200) {
            callback(xhr.responseText === 'exists');
        } else {
            console.error('Request failed. Returned status of ' + xhr.status);
            callback(false); // O gestisci l'errore in un altro modo
        }
    };
    xhr.onerror = function() {
        console.error('Request error');
        callback(false); // O gestisci l'errore in un altro modo
    };
    xhr.send('userName=' + encodeURIComponent(userName));
}


// funz GLOBALE PER REDIRECT
function checkRedirectOnNextStep(nextStep, userName) {
    if (nextStep === 1) {
        window.location.href = 'result.php?userName=' + encodeURIComponent(userName);
    }
}


// funz MAIN le richiama tutte e gestiscce flusso
function toggleDivs() {
    var currentDiv = document.getElementById(`STEP${currentStep}`);
    var nextStep = currentStep < totalSteps ? currentStep + 1 : 1;
    var nextDiv = document.getElementById(`STEP${nextStep}`);

    var userName = document.getElementById('userName').value;
    var disclaimer = document.getElementById('DISCLAIMER');
    var disclaimerOptions = document.getElementById('DISCLAIMER_OPTIONS');

    checkRedirectOnNextStep(nextStep, userName);

    if (currentStep === 1) {
        checkUserNameExists(userName, function(exists) {
            if (exists) {
                disclaimer.classList.remove('hidden');
                return;
            } else {
                disclaimer.classList.add('hidden');
                saveUserName(userName);
                animateButton();
                animateTransition(currentDiv, nextDiv);
                currentStep = nextStep;
                updateProgressBar();
            }
        });
        return;
    }

    var answerText = getAnswerForCurrentStep();

    console.log(`Saving step ${currentStep}: ${answerText}`); 

    if (answerText === '' && currentStep !== 11) {
        disclaimerOptions.classList.remove('hidden');
        return;
    } else {
        disclaimerOptions.classList.add('hidden');
        if (currentStep !== '') {
            saveAnswer(answerText);
        }
    }

    animateButton();
    animateTransition(currentDiv, nextDiv);


    currentStep = nextStep;
    updateProgressBar();

    // Aggiunta della classe 'hidden' alla classe 'progress-point' allo STEP11
    if (currentStep === 11) {
        var progressPointDiv = document.querySelector('.progress-point');
        if (progressPointDiv) {
            progressPointDiv.classList.add('hidden');
        }
    }
}

// funz SALVA RISPOSTA PER CONTROLLARLA
function saveAnswer(answerText) {
    var xhr = new XMLHttpRequest();
    xhr.open('POST', 'save_answer.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.onload = function() {
        if (xhr.status === 200) {
            console.log('Server response:', xhr.responseText);
        } else {
            console.error('Error saving answer:', xhr.statusText);
        }
    };

    var step = currentStep;
    var answer = encodeURIComponent(answerText);
    var userName = encodeURIComponent(document.getElementById('userName').value);

    console.log('Sending data:', { step, answer, userName });

    xhr.send('step=' + step + '&answer=' + answer + '&userName=' + userName);
}

// funz SALVA USERNAME PER CONTROLLARLO
function saveUserName(userName) {
    var xhr = new XMLHttpRequest();
    xhr.open('POST', 'save_user_name.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    /* BLOCCO PER USERNAME CON SPAZI 
    
    xhr.onload = function() {
        if (xhr.status === 200) {
            console.log(xhr.responseText);
        }
    };*/
    xhr.send('userName=' + encodeURIComponent(userName));
}

// funz ANIMAZIONE cta
function animateButton() {
    const btn = document.getElementById('continueBtn');
    // [PER ORA TOLTO PARTE CAMBIO ICONA SUL CLICK] const img = btn.querySelector('.submit-arrow');
    btn.classList.add('grow');
    //img.src = './img/ico/sent.png';

    setTimeout(() => {
        //img.src = './img/ico/arrow.png';

        btn.classList.remove('grow');
    }, 150);
}

// funz ANIMAZIONE ENTRATA E USCITA (cambio classi)
function animateTransition(currentDiv, nextDiv) {
    currentDiv.classList.add('slide-out');
    setTimeout(() => {
        currentDiv.classList.add('hidden');
        currentDiv.classList.remove('slide-out');
        nextDiv.classList.remove('hidden');
        nextDiv.classList.add('slide-out');
        void nextDiv.offsetWidth;
        nextDiv.classList.remove('slide-out');
        nextDiv.classList.add('slide-in');
    }, 500);
}

// funz PROGRESS BAR
function updateProgressBar() {
    const progressPercentage = (currentStep - 1) / (totalSteps - 1) * 100;
    const elem = document.getElementById("barra");
    elem.style.width = progressPercentage + "%";
}

// funz CAMBIO CLASSE TRA STEP (animazione slide)
function resetQuiz() {
    document.getElementById(`STEP${currentStep}`).classList.add('hidden');
    currentStep = 1;
    document.getElementById('STEP1').classList.remove('hidden');
    document.getElementById('STEP1').classList.add('slide-in');
    updateProgressBar();
}

// funz CAMBIO CLASSE TRA STEP (colore opzione)
document.addEventListener('DOMContentLoaded', function() {
    var opzioni = document.querySelectorAll('.opzione');
    opzioni.forEach(function(opzione) {
        opzione.addEventListener('click', function() {
            opzioni.forEach(function(op) {
                op.classList.remove('opzione-attiva');
            });
            this.classList.add('opzione-attiva');
        });
    });
    document.getElementById('continueBtn').addEventListener('click', toggleDivs);
});
window.onload = function() {
    for (let i = 1; i <= totalSteps; i++) {
        let div = document.getElementById(`STEP${i}`);
        if (div) {
            div.classList.add(i === 1 ? 'slide-in' : 'hidden');
        }
    }
    updateProgressBar();
}