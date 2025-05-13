<!DOCTYPE html>
<html lang="de">
    <link rel="stylesheet" href="style.css">
<head>
    <meta charset="UTF-8">
    <title>Anime-Suche Start</title>
</head>
<body>
    
<style>
html, body {
    height: 100%; /* Der Body füllt die gesamte Seite */
    margin: 0; /* Entfernt Standardabstände */
    padding: 0; 
}

body {
    font-family: sans-serif;
    display: flex;
    flex-direction: column; /* Stellt sicher, dass alles vertikal angeordnet wird */
    justify-content: center; /* Zentriert alles vertikal */
    align-items: center; /* Zentriert alles horizontal */
    background-image: url('Pictures/Starry_sky.jpg');
    background-size: cover;
    background-position: center;
    background-attachment: fixed;
    opacity: 0.7;
    text-align: center; /* Zentriert den Text */
    height: 100%;
}

h1 {
    margin-bottom: 20px; /* Abstand zwischen Titel und Suchformular */
}
</style>

<h1>Willkommen bei der Anime-Suche</h1>

<form id="searchForm">
    <input type="text" name="query" id="queryInput" placeholder="Gib einen Anime-Titel ein" required>
    <button type="submit">Suchen</button>
</form>

<div id="info-banner" class="info-banner" style="display: none;">
    <p>
        🔧 Ich nutze folgende APIs für die Webseite: <strong>MyAnimeList</strong> & <strong>YouTube</strong>. Ladezeiten können je nach Anime variieren. (Sucherergebnisse von YouTube werden NICHT von mir ausgesucht. Für weitere Infos <a href="Infos.php">klicke hier</a>)
    </p>
    <button id="close-banner" style="padding: 10px 20px; margin-top: 10px; cursor: pointer;">Okay, verstanden</button>
</div>

<script>
    // Öffne Seite in einem neuen Tab
    document.getElementById("searchForm").addEventListener("submit", function(e) {
        e.preventDefault();
        const query = document.getElementById("queryInput").value;
        const url = "search.php?query=" + encodeURIComponent(query);
        window.open(url, "_blank"); // "_self" = gleicher Tab
    });

    // Zeig den Banner nur, wenn er noch nicht akzeptiert wurde
    if (!localStorage.getItem('bannerClosed')) {
        document.getElementById('info-banner').style.display = 'flex';
    }

    // Schließen + merken
    document.getElementById('close-banner').addEventListener('click', function () {
        document.getElementById('info-banner').style.display = 'none';
        localStorage.setItem('bannerClosed', 'true');
    });
</script>

</body>
</html>