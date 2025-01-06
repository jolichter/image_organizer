# Bild-Organizer für Uploads – Automatische Verzeichnisstruktur mit PHP

Ein großer Vorteil von INSTAR-Webcams ist, dass sie ohne Cloud-Anbindung funktionieren (optional verfügbar) und MQTT-Verbindungen für SmartHome bieten.

Am 01.01.2025 um 00:00 Uhr trat der INSTAR Neujahrs-Bug auf ([LINK](https://forum.instar.com/t/in-9008-full-hd-erstellt-seit-dem-01-01-2025-keine-ordner-mehr-auf-dem-ftp-server-beim-speichern-von-bildern/30504/1)).

Der Fehler wurde schnell behoben, brachte mich aber auf die Idee, die Ordnerstruktur individuell anzupassen, da INSTAR hierfür keine Konfigurationsoption bietet.

---

## Kurzbeschreibung des Codes
Dieses PHP-Skript organisiert automatisch Bilddateien in einem überwachten Ordner (z.B. `/images`) und sortiert sie nach ihrem Erstellungsdatum in Tagesunterordner (Format `YYYY-MM-DD`). Es unterstützt mehrere Dateiformate und ist flexibel anpassbar.

## Funktionsweise
### Verzeichnisscan
Das Skript durchsucht einen definierten Ordner (`/images`) nach Dateien mit bestimmten Endungen (`jpg`, `jpeg`). Gefundene Dateien werden basierend auf ihrem Erstellungsdatum in passende Tagesunterverzeichnisse verschoben.

## Sortierung und Strukturierung
- **Automatische Tagesordner-Erstellung:**
  Bilder werden in Unterordner im Format `YYYY-MM-DD` sortiert.
- **Optionaler Subfolder:**
  Innerhalb der Tagesordner kann optional ein zusätzlicher Unterordner (z.B. `/YYYY-MM-DD/fotos`) erstellt werden.

## Fehlermanagement
- **Automatische Verzeichniserstellung:**
  Falls das Zielverzeichnis nicht existiert, wird es automatisch erstellt.
- **Logging von Fehlern und Erfolgen:**
  Fehler beim Erstellen von Verzeichnissen oder beim Verschieben von Dateien werden (optional) in das PHP-Error-Log geschrieben. Erfolgreiche Datei-Verschiebungen werden ebenfalls protokolliert, sofern das Logging aktiviert ist.
- **Log-Speicherorte:**
  Standardmäßig unter `/var/log/php_errors.log` oder im `logs`-Verzeichnis der jeweiligen Domain (bei Plesk-Umgebungen). 

## Optimierungen und zusätzliche Funktionen
- **Maximale Dateiverarbeitung pro Lauf:**
  Das Skript verarbeitet maximal **5000 Dateien pro Ausführung** (`$maxFilesPerRun`). Falls mehr Dateien im Verzeichnis vorhanden sind, wird die Verarbeitung beim nächsten Aufruf fortgesetzt. `rename()` ist die schnellste Lösung für das Verschieben großer Dateien im selben Dateisystem, da nur der Pfad geändert wird.
- **Doppelte Dateiprüfung entfernt:**
  Die Dateiendungsprüfung erfolgt nur noch in der `scanDirectory`-Funktion, wodurch doppelte Prüfungen vermieden werden.
- **Effiziente Aktualisierung:**
  Das Array `$lastScan` wird nur dann aktualisiert, wenn tatsächlich Dateien verarbeitet wurden, was die Performance verbessert.
- **Verzeichnisprüfung beim Start:**
  Der Hauptüberwachungsordner (`$watchDir`) wird zu Beginn des Skripts erstellt, falls er nicht existiert, um sicherzustellen, dass die Überwachung fehlerfrei starten kann.

## Flexibilität und Erweiterbarkeit
- **Mehrere Dateiformate unterstützt:**
  Die zu verarbeitenden Dateiformate können in einem Array (`$fileExtensions`) definiert werden (`jpg`, `jpeg` usw.). Die Prüfung ist **case-insensitive** (`jpg`, `JPG`).  
- **Einfache Integration:**
  Das Skript kann eigenständig ausgeführt, per Cronjob automatisiert oder in bestehende PHP-Projekte integriert werden (z.B. Bildergalerien oder FTP-Verwaltungssysteme).
  
