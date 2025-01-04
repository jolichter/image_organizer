<?php
# Bild-Organizer für Uploads – Tägliche automatische Verzeichnisstruktur für Webcams und FTP-Uploads mit PHP
# image_organizer.php
# V 25.01.005
# Du kannst die Funktion in anderen Skripten aufrufen, z.B.:
# include 'image_organizer.php';

date_default_timezone_set('Europe/Berlin');  // Zeitzone explizit setzen

$watchDir = __DIR__ . '/images';  // Ordner, der überwacht wird
$fileExtension = 'jpg';  // Zu überwachende Dateiendung
$createImageSubfolder = false;  // Subfolder aktivieren/deaktivieren
$imageSubfolderName = 'fotos';  // Name des Subfolders
$enableErrorLogging = false;  // Fehlerprotokoll aktivieren/deaktivieren

$lastScan = [];

// Initialer Scan des Verzeichnisses
function scanDirectory($dir, $extension) {
    global $enableErrorLogging;
    $result = [];
    try {
        $iterator = new DirectoryIterator($dir);
    } catch (Exception $e) {
        if ($enableErrorLogging) {
            error_log("Verzeichnisfehler: " . $e->getMessage());
        }
        return [];
    }
    foreach ($iterator as $fileInfo) {
        if ($fileInfo->isFile() && $fileInfo->getExtension() === $extension) {
            $result[$fileInfo->getFilename()] = $fileInfo->getMTime();
        }
    }
    return $result;
}

function organizeImages($watchDir, $extension, $createSubfolder = false) {
    global $lastScan, $createImageSubfolder, $imageSubfolderName, $enableErrorLogging;

    $currentScan = scanDirectory($watchDir, $extension);

    foreach ($currentScan as $file => $timestamp) {
        if (!isset($lastScan[$file]) || $lastScan[$file] != $timestamp) {
            $fileDate = date('Y-m-d', $timestamp);
            $targetDir = "$watchDir/$fileDate" . ($createImageSubfolder ? "/$imageSubfolderName" : '');

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

    $lastScan = $currentScan;
}

// Automatischer Aufruf bei direkter Ausführung
organizeImages($watchDir, $fileExtension, $createImageSubfolder);
?>
