<?php
# Bild-Organizer für Uploads – Automatische Verzeichnisstruktur mit PHP
# image_organizer.php
# V 25.01.008
# Du kannst die Funktion in anderen Skripten aufrufen, z.B.:
# include_once 'image_organizer.php';

date_default_timezone_set('Europe/Berlin');  // Zeitzone explizit setzen

$watchDir = __DIR__ . '/images';  // Ordner, der überwacht wird
$fileExtensions = ['jpg', 'jpeg'];  // Zu überwachende Dateiendungen (Array)
$createImageSubfolder = false;  // Subfolder aktivieren/deaktivieren
$imageSubfolderName = 'fotos';  // Name des Subfolders
$enableErrorLogging = false;  // Fehlerprotokoll aktivieren/deaktivieren
$maxLimit = 5000;  // Maximale Anzahl erlaubter Dateien

// Überprüfen, ob das Hauptverzeichnis existiert, andernfalls erstellen
if (!is_dir($watchDir) && !mkdir($watchDir, 0755, true) && !is_dir($watchDir)) {
    error_log("Fehler beim Erstellen des Überwachungsordners: $watchDir");
    exit("Fehler: Verzeichnis konnte nicht erstellt werden.");
}

// Zähle Dateien mit den gewünschten Endungen
$iterator = new DirectoryIterator($watchDir);
$files = [];
foreach ($iterator as $fileInfo) {
    if ($fileInfo->isFile() && in_array(strtolower($fileInfo->getExtension()), $fileExtensions)) {
        $files[] = $fileInfo->getFilename();
    }
}

$fileCount = count($files);

if ($fileCount >= $maxLimit) {
    if ($enableErrorLogging) {
        error_log("Maximale Anzahl von $maxLimit Dateien erreicht. Aktuell: $fileCount. Skript wird nicht ausgeführt.");
    }
    echo "<h2>Upload-Limit erreicht</h2>";
    echo "<p>Die maximale Anzahl von $maxLimit Dateien wurde erreicht. Es befinden sich derzeit $fileCount Dateien im Ordner. Bitte lösche oder verschiebe Dateien, bevor neue verarbeitet werden können.</p>";
    exit();
}

$lastScan = [];

// Initialer Scan des Verzeichnisses
function scanDirectory($dir, $extensions) {
    global $enableErrorLogging;
    $result = [];

    if (!is_dir($dir)) {
        if (!mkdir($dir, 0755, true)) {
            error_log("Fehler: Verzeichnis konnte nicht erstellt werden: $dir");
            return [];
        } elseif ($enableErrorLogging) {
            error_log("Verzeichnis $dir wurde erstellt.");
        }
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
        if (!isset($lastScan[$file]) || $lastScan[$file] != $timestamp) {
            $fileDate = date('Y-m-d', $timestamp);
            $targetDir = "$watchDir/$fileDate" . ($createSubfolder ? "/$imageSubfolderName" : '');

            if (!is_dir($targetDir) && !mkdir($targetDir, 0755, true)) {
                if ($enableErrorLogging) {
                    error_log("Fehler beim Erstellen des Zielverzeichnisses: $targetDir");
                }
                continue;
            }

            if (file_exists("$watchDir/$file") && !rename("$watchDir/$file", "$targetDir/" . basename($file))) {
                $errorMessage = error_get_last();
                $errorText = $errorMessage['message'] ?? 'Unbekannter Fehler';
                if ($enableErrorLogging) {
                    error_log("Fehler beim Verschieben: $file - " . $errorText);
                }
            } else {
                if ($enableErrorLogging) {
                    error_log("Datei erfolgreich verschoben: $file -> $targetDir");
                }
            }
        }
    }

    if (!empty($currentScan)) {
        $lastScan = array_merge($lastScan, $currentScan);
    }
}

// Automatischer Aufruf bei direkter Ausführung
organizeImages($watchDir, $fileExtensions, $createImageSubfolder);
?>
