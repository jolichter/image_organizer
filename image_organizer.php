<?php
# Bild-Organizer für Uploads – Tägliche automatische Verzeichnisstruktur für Webcams
# image_organizer.php
# V 25.01.006
# Du kannst die Funktion in anderen Skripten aufrufen, z.B.:
# include 'image_organizer.php';

date_default_timezone_set('Europe/Berlin');  // Zeitzone explizit setzen

$watchDir = __DIR__ . '/images';  // Ordner, der überwacht wird
$fileExtensions = ['jpg', 'jpeg'];  // Zu überwachende Dateiendungen (Array)
$createImageSubfolder = false;  // Subfolder aktivieren/deaktivieren
$imageSubfolderName = 'fotos';  // Name des Subfolders
$enableErrorLogging = false;  // Fehlerprotokoll aktivieren/deaktivieren

$lastScan = [];

// Initialer Scan des Verzeichnisses
function scanDirectory($dir, $extensions) {
    global $enableErrorLogging;
    $result = [];

    // Überprüfen, ob das Verzeichnis existiert, andernfalls erstellen
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
        if ($enableErrorLogging) {
            error_log("Verzeichnis $dir wurde erstellt.");
        }
        return [];
    }

    try {
        $iterator = new DirectoryIterator($dir);
    } catch (Exception $e) {
        if ($enableErrorLogging) {
            error_log("Verzeichnisfehler: " . $e->getMessage());
        }
        return [];
    }

    foreach ($iterator as $fileInfo) {
        if ($fileInfo->isFile() && in_array(strtolower($fileInfo->getExtension()), $extensions)) {
            $result[$fileInfo->getFilename()] = $fileInfo->getMTime();
        }
    }
    return $result;
}

function organizeImages($watchDir, $extensions, $createSubfolder = false) {
    global $lastScan, $createImageSubfolder, $imageSubfolderName, $enableErrorLogging;

    $currentScan = scanDirectory($watchDir, $extensions);

    foreach ($currentScan as $file => $timestamp) {
        // Überprüfen, ob die Dateiendung der aktuellen Datei der gewünschten entspricht
        if (in_array(strtolower(pathinfo($file, PATHINFO_EXTENSION)), $extensions)) {
            if (!isset($lastScan[$file]) || $lastScan[$file] != $timestamp) {
                $fileDate = date('Y-m-d', $timestamp);
                $targetDir = "$watchDir/$fileDate" . ($createSubfolder ? "/$imageSubfolderName" : '');

                if (!is_dir($targetDir)) {
                    mkdir($targetDir, 0755, true);
                }

                if (!rename("$watchDir/$file", "$targetDir/$file")) {
                    if ($enableErrorLogging) {
                        error_log("Fehler beim Verschieben: $file");
                    }
                }
            }
        }
    }

    $lastScan = $currentScan;
}

// Automatischer Aufruf bei direkter Ausführung
organizeImages($watchDir, $fileExtensions, $createImageSubfolder);
?>
