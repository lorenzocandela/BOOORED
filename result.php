<!DOCTYPE html>
<html lang="it">
<head>
    <meta name="theme-color" content="#04070b">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">

    <link rel="stylesheet" href="./font/css/cabinet-grotesk.css">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BORED</title>
    <link rel="stylesheet" href="style.css?v=0.3">
    <link rel="stylesheet" href="./ai-third/style.css?v=0.2">
    <link rel="stylesheet" href="./ai-third/swipe.css?v=0.1">
    <link rel="stylesheet" href="./font/css/cabinet-grotesk.css">
    <link rel="manifest" href="manifest.json">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
</head>
<body>

<?php
include 'dbconfig.php';

$userName = $_GET['userName'];

$rispostetotali = ""; // var per accumulare tutte le risposte

if ($userName) {
    $userName = $conn->real_escape_string($userName);
    $selectQuery = "SELECT * FROM `$userName` ORDER BY step";
    $result = $conn->query($selectQuery);

    // array di associazioni per i passi (da rendere poi dinamico)
    $stepDescriptions = [
        2 => "How are you feeling this period of your life?",
        3 => "How would you prefer not to feel throughout your day?",
        4 => "What activity are you currently engaged in?",
        5 => "What is the biggest source of motivation in your life?",
        6 => "Which of these activities help you unwind and relax the most?",
        7 => "Which best describes your ideal work environment?",
        8 => "How old are you? and what are your 'sources of inspiration'?",
        9 => "What are the activities or experiences that make you feel most alive and connected to yourself?",
        10 => "If you had an entire day to yourself, how would you spend it?"
    ];

    echo "<div class='container' style='padding: 55px 35px; display: none;'>";

    echo "<span class='title'  style='padding: 0'>$userName</span><br><br><br>";

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $step = $row['step'];
            $answer = htmlspecialchars($row['answer']);
            
            $stepDescription = isset($stepDescriptions[$step]) ? $stepDescriptions[$step] : "Step $step";
            
            echo "<span class='subtitle-result-question'>$stepDescription</span><br><span class='subtitle-result'>$answer</span><br><br>";
            
            // accumula le risposte
            $rispostetotali .= $stepDescription . " " . $answer . " ";
        }
    } else {
        echo "No responses found.";
    }

    echo "</div>";

} else {
    echo "Invalid user.";
}

$conn->close();
?>


<div class="result-navbar">
    <img src="./ai-third/navbar.png">
</div>
<div class="result-box">
    <div class="result-box-top">
        <span class="title">
            Hi, 
            <?php 
                include 'dbconfig.php';  
                $userName = $_GET['userName'];
                echo htmlspecialchars($userName);
                $conn->close();
            ?> 
            ;)
        </span>
    </div>
    <div class="result-box-body" id="result">
        <!-- generazione cardine da formattare -->
    </div>
    <button id="generateButton">Generate <img class="btn-ai-ico" src="./ai-third/sparkle.png"></button>
</div> 


<script src="./ai-third/script_swipe.js"></script>

<script>

    // gestore spaziatura btn quando ci sono le attività o meno (da capire se funza live)
    $(document).ready(function() {
        if ($('img.act').length === 0) {
            $('#generateButton').addClass('empty-list-result');
        } else {
            $('#generateButton').addClass('full-list-result');
        }
    });

    // var rispostetotali a JS
    var rispostetotali = <?php echo json_encode($rispostetotali); ?>;
    
    // genera versione RAW (manca da formattare ancora)
    document.addEventListener('DOMContentLoaded', function() {
    const generateButton = document.getElementById('generateButton');
    const resultElement = document.getElementById('result');

    generateButton.addEventListener('click', async () => {
        resultElement.textContent = 'Generating activities...';
        try {
            const response = await fetch('https://api.openai.com/v1/chat/completions', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': '//' // non mi carica il git la passo come var di ambiente
                },
                body: JSON.stringify({
                    model: 'gpt-4',
                    messages: [
                        { 
                            role: 'user', 
                            /* PROVA 1
                            content: `You have the user's profile based on this information: "${rispostetotali}". Generate a list of 5 personalized micro-activities that the user can complete within one day. Each activity should be small, realistic, and varied, providing a meaningful sense of accomplishment by the end of the day.

                            Use the user's profile as inspiration, focusing on specific, actionable recommendations that connect to their interests and environment. For example, instead of suggesting "watch a film," recommend a specific title like "watch 'Mediterraneo' directed by Gabriele Salvatores." 

                            Provide concrete details tailored to the user’s context, such as specific songs, albums, or films that align with their tastes. Avoid repeating the same suggestions multiple times and ensure diversity in recommendations. 

                            Format the output like this:

                            Title: [Max 1 word, short and impactful]
                            Description: [Clear and specific task; simply state what to do without time limits.]
                            Category: [Choose one: hobby, education, leisure, mental-health, socialization, sport]

                            Ensure the activities vary in type and difficulty, all achievable within a single day. Focus on providing engaging and enriching experiences, including specific names of songs, albums, or films without repeating suggestions.
                            ` 
                            */
                           content: `I have an array of information about an individual, which includes details such as age, profession, hobbies, musical and movie interests, daily activities, predominant emotions, and future goals. Using this information, generate a list of 5 specific and concrete micro-activities that can help them feel active and fulfilled throughout the day/week. The activities should be actionable and connected to their interests and lifestyle. Make sure to include unique and relevant suggestions, such as listening to specific songs (tell them which song by which artist, suggest similar artists based on their expressed preferences, considering that they will not give you all the artists or songs they like but will provide only a few examples, max one song per recommendation), watching particular films or series (tell them which film or series specifically, suggest similar films or series based on their tastes, also considering that they won’t give you all the movies or series they like but only a few examples), cooking new recipes (tell them the recipe name), or engaging in physical activities.

                           The array: "${rispostetotali}".
                           
                           Please format the output like this:
                           Title [maximum 1 word, impactful and clear, no compound words, no "1. "Title" just the Title] 
                           Description [maximum 25/30 words]
                           Category: [choose one: hobby, education, leisure, mental-health, socialization, sport]

                           If you suggest reading a book, listening to a song, listening to a podcast, reading an article, or watching a movie, please make sure to recommend a specific book, song, podcast, article, or movie that aligns with their tastes, based on the information they have provided. 
                           `
                        }
                    ],
                    max_tokens: 360
                })
            });

            if (!response.ok) {
                throw new Error(`HTTP error! Status: ${response.status}`);
            }

            const data = await response.json();
            if (data.choices && data.choices.length > 0) {
                const activities = data.choices[0].message.content.trim();
                const activityLines = activities.split('\n').filter(line => line.trim() !== '');
                let activityHTML = '';

                for (let i = 0; i < activityLines.length; i += 3) {
                    const title = activityLines[i].replace('Title:', '').trim();
                    const description = activityLines[i + 1].replace('Description:', '').trim();
                    const category = activityLines[i + 2].replace('Category:', '').trim().toLowerCase();
                    activityHTML += `
                        <div id="act-ai-${(i/3)+1}" class="activity">
                            <!--<img class="ai-img" src="./ai-third/${category}.png" alt="${category}">-->
                            <img class="icon-heart" src="./ai-third/like.png" alt="Like">
                            <img class="icon-trash" src="./ai-third/trash.png" alt="Trash">
                            <div class="ai-box-sez">
                            <span class="ai-title">${title}</span>
                            <span class="ai-desc">${description}</span>
                            </div>
                        </div>
                    `;
                }
                resultElement.innerHTML = activityHTML;



                //SPAZIO PER ANIMAZIONE DEI SINGOLI DIV, CON SCRIPT ESTERNO NON FUNZIONA ORDINAMENTO
                const SWIPE_DISTANCE = 100;

                document.querySelectorAll('.activity').forEach(item => {
                    let startX;
                    let isSwiping = false;
                    let isSwiped = false;
                    item.style.transition = 'transform 0.3s ease, opacity 0.3s ease';

                    item.addEventListener('touchstart', (e) => {
                        startX = e.touches[0].clientX;
                        isSwiping = true;
                    });

                    item.addEventListener('touchmove', (e) => {
                        if (!isSwiping) return;

                        const currentTouchX = e.touches[0].clientX;
                        let diffX = currentTouchX - startX;
                        if (diffX > 50) {
                            item.style.transform = `translateX(${SWIPE_DISTANCE}px)`;
                            item.querySelector('.icon-heart').style.opacity = '1';
                            item.querySelector('.icon-trash').style.opacity = '0';
                        } else if (diffX < -50) { // Swipe verso sinistra
                            item.style.transform = `translateX(${-SWIPE_DISTANCE}px)`; // Sposta a sinistra
                            item.querySelector('.icon-trash').style.opacity = '1'; // Mostra l'icona cestino
                            item.querySelector('.icon-heart').style.opacity = '0'; // Nascondi l'icona cuore
                        } else {
                            item.style.transform = `translateX(0)`; // Torna alla posizione originale
                            item.querySelector('.icon-heart').style.opacity = '0'; // Nascondi l'icona cuore
                            item.querySelector('.icon-trash').style.opacity = '0'; // Nascondi l'icona cestino
                        }
                    });

                    item.addEventListener('touchend', () => {
                        isSwiping = false;
                        const currentTransform = item.style.transform;

                        if (currentTransform === `translateX(${SWIPE_DISTANCE}px)`) {
                            isSwiped = true; 
                        } else if (currentTransform === `translateX(${-SWIPE_DISTANCE}px)`) {
                            isSwiped = true; 
                        } else {
                            item.style.transform = `translateX(0)`;
                            item.querySelector('.icon-heart').style.opacity = '0';
                            item.querySelector('.icon-trash').style.opacity = '0';
                        }
                    });


                    item.querySelector('.icon-heart').addEventListener('click', (e) => {
                    if (isSwiped) {
                        const title = item.querySelector('.ai-title').textContent;
                        const description = item.querySelector('.ai-desc').textContent;
                        const userName = new URLSearchParams(window.location.search).get('userName');

                        fetch('save_activity.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded',
                            },
                            body: `userName=${encodeURIComponent(userName)}&title=${encodeURIComponent(title)}&description=${encodeURIComponent(description)}`
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                alert('Attività salvata!');
                            } else {
                                alert('Errore nel salvataggio dell\'attività: ' + data.message);
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            alert('Si è verificato un errore durante il salvataggio dell\'attività.');
                        });

                        isSwiped = false;
                        item.style.transform = 'translateX(0)';
                        item.querySelector('.icon-heart').style.opacity = '0';
                        item.querySelector('.icon-trash').style.opacity = '0';
                    }
                });

                    item.querySelector('.icon-trash').addEventListener('click', (e) => {
                        if (isSwiped) {
                            alert('Attività eliminata!');
                            isSwiped = false; 
                            item.style.transform = 'translateX(0)';
                            item.querySelector('.icon-heart').style.opacity = '0';
                            item.querySelector('.icon-trash').style.opacity = '0';
                        }
                    });
                });

            } else {
                resultElement.textContent = 'No activities generated.';
            }
        } catch (error) {
            resultElement.textContent = 'An error occurred.';
            console.error('Error:', error);
        }
        });
    });

</script>
</body>
</html>
