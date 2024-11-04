//FUNZIONE COME GMAIL PER LE TASK (da riprendere più avanti)


const resultElement = document.getElementById('result');

resultElement.addEventListener('click', (event) => {
    if (event.target.closest('.icon-heart')) {
        // LIKE
        const activity = event.target.closest('.activity');
        console.log('Liked:', activity);
    } else if (event.target.closest('.icon-trash')) {
        // CANCELLA
        const activity = event.target.closest('.activity');
        activity.remove();
        console.log('Removed:', activity);
    }
});

const SWIPE_DISTANCE = 100; // distanza swipe (da capire)

document.querySelectorAll('.activity').forEach(item => {
    let startX;
    let isSwiping = false;
    let isSwiped = false;

    // css per motion
    item.style.transition = 'transform 0.3s ease, opacity 0.3s ease';

    item.addEventListener('touchstart', (e) => {
        startX = e.touches[0].clientX;
        isSwiping = true;
    });

    item.addEventListener('touchmove', (e) => {
        if (!isSwiping) return;

        const currentTouchX = e.touches[0].clientX;
        let diffX = currentTouchX - startX;

        if (diffX > 50) { // swipe verso destra
            item.style.transform = `translateX(${SWIPE_DISTANCE}px)`; // Sposta a destra
            item.querySelector('.icon-heart').style.opacity = '0.2'; // Mostra l'icona cuore
            item.querySelector('.icon-trash').style.opacity = '0'; // Nascondi l'icona cestino
        } else if (diffX < -50) { // swipe verso sinistra
            item.style.transform = `translateX(${-SWIPE_DISTANCE}px)`; // Sposta a sinistra
            item.querySelector('.icon-trash').style.opacity = '0.2'; // Mostra l'icona cestino
            item.querySelector('.icon-heart').style.opacity = '0'; // Nascondi l'icona cuore
        } else {
            item.style.transform = `translateX(0)`; // origin
            item.querySelector('.icon-heart').style.opacity = '0'; // Nascondi l'icona cuore
            item.querySelector('.icon-trash').style.opacity = '0'; // Nascondi l'icona cestino
        }
    });

    item.addEventListener('touchend', () => {
        isSwiping = false;

        const currentTransform = item.style.transform;

        if (currentTransform === `translateX(${SWIPE_DISTANCE}px)`) {
            isSwiped = true; // dx
        } else if (currentTransform === `translateX(${-SWIPE_DISTANCE}px)`) {
            isSwiped = true; // sx
        } else {
            // origin
            item.style.transform = `translateX(0)`;
            item.querySelector('.icon-heart').style.opacity = '0'; // hide l'icona cuore
            item.querySelector('.icon-trash').style.opacity = '0'; // hide l'icona cestino
        }
    });

    // click l'icon x il salvataggio
    item.querySelector('.icon-heart').addEventListener('click', (e) => {
        if (isSwiped) {
            alert('Attività salvata!');
            isSwiped = false; // reset lo stato di swipe
            item.style.transform = 'translateX(0)'; // reset la posizione originale
            item.querySelector('.icon-heart').style.opacity = '0'; 
            item.querySelector('.icon-trash').style.opacity = '0';
        }
    });

    // click l'icon x cancellare
    item.querySelector('.icon-trash').addEventListener('click', (e) => {
        if (isSwiped) {
            alert('Attività eliminata!');
            isSwiped = false; // reset lo stato di swipe
            item.style.transform = 'translateX(0)'; // reset la posizione originale
            item.querySelector('.icon-heart').style.opacity = '0'; 
            item.querySelector('.icon-trash').style.opacity = '0'; 
        }
    });
});
