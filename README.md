# Bild-Organizer für Uploads – Tägliche automatische Verzeichnisstruktur für Webcams

Ein großer Vorteil von INSTAR-Webcams ist, dass sie ohne Cloud-Anbindung funktionieren (optional verfügbar) und MQTT-Verbindungen für SmartHome bieten.

Am 01.01.2025 um 00:00 Uhr trat der INSTAR Neujahrs-Bug auf ([LINK](https://forum.instar.com/t/in-9008-full-hd-erstellt-seit-dem-01-01-2025-keine-ordner-mehr-auf-dem-ftp-server-beim-speichern-von-bildern/30504/1)).

Der Fehler wurde schnell behoben, brachte mich aber auf die Idee, die Ordnerstruktur individuell anzupassen, da INSTAR hierfür keine Konfigurationsoption bietet.

Das PHP-Skript organisiert automatisch Bilder in einem überwachten Ordner. Beim Aufruf prüft es einen Pfad (z.B. `/images`) auf neue `.jpg`-Dateien. Gefundene Bilder werden in Tagesordner (`YYYY-MM-DD`) verschoben, optional mit zusätzlichem Unterordner (z.B. `/fotos`).

Nicht vorhandene Verzeichnisse werden automatisch erstellt. Fehlerprotokolle bei Problemen werden ins Error-Log geschrieben (standardmäßig unter `/var/log/php_errors.log` oder im logs-Verzeichnis der jeweiligen Domain unter Plesk).

Das Skript kann eigenständig ausgeführt, per Cronjob automatisiert oder – meine bevorzugte Lösung – in andere PHP-Skripte (z.B. eine Bildergalerie) eingebunden werden.
