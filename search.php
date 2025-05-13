<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>Suchergebnisse</title>
    <link rel="stylesheet" href="style.css">
    <style>
.background {
    position: relative;
    background-image: url('Pictures/Starry_sky.jpg');
    background-size: cover;
    background-position: center;
    background-attachment: fixed;
    padding: 20px;
}

.background::before {
    content: "";
    position: absolute;
    top: 0; left: 0; right: 0; bottom: 0;
    background-color: rgba(0, 0, 0, 0.5);
    z-index: 0;
}

.background > * {
    position: relative;
    z-index: 1;
}


    .background * {
        opacity: 1;
    }

    body {
        margin: 0;
        padding: 0;
        font-family: sans-serif;
    }

    input[type="text"] {
        padding: 10px;
        font-size: 16px;
        width: 250px;
        margin-bottom: 10px;
    }

    button {
        padding: 10px;
        font-size: 16px;
        cursor: pointer;
    }
    </style>
</head>
<body>

<!-- Alles, was den Hintergrund haben soll -->
<div class="background">
    <h1>Anime-Suchergebnisse</h1>

    <form method="get" action="search.php">
    <input type="text" name="query" placeholder="Gib einen Anime-Titel ein" required>
    <button type="submit">Suchen</button>
</form>

</div>

<br>

<!-- Resultate OHNE Hintergrund -->
<?php
require_once 'config.php';
// Keeps API Keys private
$query = isset($_GET['query']) ? $_GET['query'] : '';

function getCache($key, $duration_seconds = 900) {
    $cache_file = __DIR__ . '/cache/' . md5($key) . '.json';
    if (file_exists($cache_file) && (time() - filemtime($cache_file)) < $duration_seconds) {
        return json_decode(file_get_contents($cache_file), true);
    }
    return null;
}

function setCache($key, $data) {
    $cache_file = __DIR__ . '/cache/' . md5($key) . '.json';
    file_put_contents($cache_file, json_encode($data));
}

if (!empty($query)) {
    $anime_cache_key = "anime_" . strtolower($query);
    $data = getCache($anime_cache_key);

    if (!$data) {
        $api_url = "https://api.myanimelist.net/v2/anime?q=" . urlencode($query) . "&limit=5&fields=id,title,main_picture,synopsis,mean,genres";
        $options = ["http" => ["header" => "X-MAL-CLIENT-ID: $client_id"]];
        $context = stream_context_create($options);
        $response = file_get_contents($api_url, false, $context);

        if ($response === FALSE) {
            die('Fehler bei der Anfrage an die API.');
        }

        $data = json_decode($response, true);
        setCache($anime_cache_key, $data);
    }

    if (isset($data['data']) && count($data['data']) > 0) {
        echo '<h2>Suchergebnisse für: ' . htmlspecialchars($query) . '</h2>';

        foreach ($data['data'] as $anime) {
            $title = $anime['node']['title'];
            echo '<div style="border: 1px solid #ddd; padding: 10px; margin-bottom: 10px;">';
            echo '<h3>' . htmlspecialchars($title) . '</h3>';
            echo '<a href="https://myanimelist.net/anime/' . $anime['node']['id'] . '" target="_blank">';
            echo '<img src="' . htmlspecialchars($anime['node']['main_picture']['medium']) . '" alt="' . htmlspecialchars($title) . '" style="width: 200px; height: auto;">';
            echo '</a>';
            echo '<p><strong>Beschreibung:</strong> ' . nl2br(htmlspecialchars($anime['node']['synopsis'])) . '</p>';
            echo '<p><strong>Bewertung:</strong> ' . $anime['node']['mean'] . ' / 10</p>';

            $genres = array_map(fn($g) => $g['name'], $anime['node']['genres']);
            echo '<p><strong>Genres:</strong> ' . implode(', ', $genres) . '</p>';

            // --- YouTube-Suche mit Caching ---
            $search_query = $title . ' anime trailer';
            $yt_cache_key = "youtube_" . strtolower($search_query);
            $yt_data = getCache($yt_cache_key);

            if (!$yt_data) {
                $youtube_api_url = "https://www.googleapis.com/youtube/v3/search?part=snippet&q=" . urlencode($search_query) . "&key={$youtube_api_key}&maxResults=1&type=video";
                $youtube_response = file_get_contents($youtube_api_url);
                $yt_data = json_decode($youtube_response, true);
                setCache($yt_cache_key, $yt_data);
            }

            if (!empty($yt_data['items'])) {
                $video_id = $yt_data['items'][0]['id']['videoId'];
                echo '<div style="margin-top: 10px;">';
                echo '<iframe width="400" height="225" src="https://www.youtube.com/embed/' . $video_id . '" frameborder="0" allowfullscreen></iframe>';
                echo '</div>';
            }

            echo '</div>'; // Ende einzelnes Anime-Div
        }
    } else {
        echo '<p>Keine Ergebnisse gefunden.</p>';
    }
}
?>
</body>
</html>
