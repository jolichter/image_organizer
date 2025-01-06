# Bild-Organizer für Uploads – Tägliche automatische Verzeichnisstruktur für Webcams

Ein großer Vorteil von INSTAR-Webcams ist, dass sie ohne Cloud-Anbindung funktionieren (optional verfügbar) und MQTT-Verbindungen für SmartHome bieten.

Am 01.01.2025 um 00:00 Uhr trat der INSTAR Neujahrs-Bug auf ([LINK](https://forum.instar.com/t/in-9008-full-hd-erstellt-seit-dem-01-01-2025-keine-ordner-mehr-auf-dem-ftp-server-beim-speichern-von-bildern/30504/1)).

Der Fehler wurde schnell behoben, brachte mich aber auf die Idee, die Ordnerstruktur individuell anzupassen, da INSTAR hierfür keine Konfigurationsoption bietet.

## Kurzbeschreibung des Codes
Dieses PHP-Skript organisiert automatisch Bilddateien in einem überwachten Ordner (z.B. `/images`) und sortiert sie nach dem Erstellungsdatum in Tagesunterordner (Format `YYYY-MM-DD`).

## Funktionsweise
Verzeichnisscan: Das Skript durchsucht einen definierten Ordner (`/images`) nach Dateien mit bestimmten Endungen (`jpg`, `jpeg`).

## Sortierung
Gefundene Dateien werden anhand ihres Erstellungsdatums in einen entsprechenden Tagesordner verschoben.

## Fehlermanagement
Falls das Zielverzeichnis nicht existiert, wird es automatisch erstellt. Fehler beim Verschieben oder Zugriff auf Verzeichnisse können optional in das PHP-Error-Log geschrieben werden (standardmäßig unter `/var/log/php_errors.log` oder im logs-Verzeichnis der jeweiligen Domain unter Plesk).

## Flexibilität
Unterstützt mehrere Dateiformate durch ein Array ($fileExtensions).
Die Dateiendungsprüfung ist case-insensitive (`jpg`, `JPG`, `png` usw.).
Optional kann innerhalb der Tagesordner ein zusätzlicher Unterordner (z.B. `/fotos`) erstellt werden.
Das Skript kann eigenständig ausgeführt, per Cronjob automatisiert oder – meine bevorzugte Lösung – in andere PHP-Projekte (z.B. eine Bildergalerie) eingebunden werden.
