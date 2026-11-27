# Tipppfehler – privates Fußball-Tippspiel

Webanwendung für ein privates Tippspiel zu Fußball-Welt- und Europameisterschaften.
Teilnehmer registrieren sich, tippen alle Spiele einer Turnierrunde, geben Gruppen- und
Sondertipps ab und sehen eine laufend aktualisierte Tabelle. Spielergebnisse werden
automatisch von einer Livescore-API abgeholt, Tipps werden daraufhin bewertet.

Das Problem, das die Anwendung löst: Ein Tippspiel im Freundeskreis per Zettel oder
Tabelle ist mühsam zu pflegen. Hier werden Tippabgabe, Fristen (Tipp nur bis Anpfiff),
Punktevergabe, Rangliste, Erinnerungen und Zahlungsstatus zentral verwaltet.

## Hintergrund

Das Projekt wurde ursprünglich privat entwickelt und über mehrere Turniere (seit 2021,
zuletzt WM 2026) im Freundeskreis eingesetzt. Es dient nun als öffentliches
Referenzprojekt für die Bewerbung beim **Prototype Fund 2026**.

Der Code wird im Wesentlichen im ursprünglichen Entwicklungsstand gezeigt. Für die
Veröffentlichung wurden ausschließlich Zugangsdaten, personenbezogene Daten und
Bilder mit ungeklärten Rechten entfernt bzw. durch Konfiguration und Platzhalter
ersetzt. Es fand kein Refactoring statt.

## Technologien

- **PHP** (prozedural, ohne Framework), getestet mit PHP 8.2
- **MySQL / MariaDB** über `mysqli`
- **HTML/CSS/JavaScript** mit jQuery 1.11.3
- **PHPMailer 5.2.23** (mitgeliefert unter `phpmailer/`) für SMTP-Versand
- **CKEditor 4.9.2** für formatierte Chatbeiträge
- **skel.js 3.0.1**, jquery.scrollex, jquery.scrolly, jquery.poptrox (alle MIT) und das
  HTML-Template „Snapshot“ von TEMPLATED (CC BY 3.0) als Layout-Basis
- **simplyScroll 2.0.5** (MIT/GPL) für den Ergebnis-Ticker
- **Font Awesome 4.6.3** und **Nunito Sans** als Icon- und Schriftdateien
- Externe Dienste: **livescore-api.com** (Spielpläne, Live-Ergebnisse) und optional die
  **OpenAI-API** für automatisch generierte Tipps

## Funktionsumfang

**Registrierung und Login**

- Registrierung mit Benutzername, Name, E-Mail, Passwort und Wahl der Tipprunde
- Aktivierung per E-Mail-Link, Passwort-vergessen-Funktion, Sperre nach Fehlversuchen

**Tippen** (`member.php`)

- Ergebnistipp pro Spiel, änderbar bis zum Anpfiff
- Gruppentipp (wer kommt weiter) und Sondertipps (Weltmeister, Torschützenkönig)
- Spielplan mit Flaggen, Live-Status und Ergebnissen
- Tabelle mit Punkten nach Kategorien (richtiges Ergebnis, Tendenz, Gruppen-, Sondertipp)
  sowie eine zweite „Lucky-Loser“-Tabelle
- Tabellenstand rückwirkend nach jedem Spiel abrufbar
- Regeln und Gewinnverteilung je Tipprunde
- Chat mit Bild-Upload, YouTube-Einbettung und Antworten
- Einstellungen (E-Mail, Passwort, Standard-Tipprunde)

**Automatische Auswertung**

- `cronjob.php` holt laufende Spiele von livescore-api.com, trägt Ergebnisse ein,
  bewertet Tipps und protokolliert in `crl_cronlog`. Zugriff per `cron_secret`,
  Schutz vor Parallelläufen über `GET_LOCK`.
- Für Teilnehmer ohne abgegebenen Tipp werden Autotipps erzeugt, optional per
  OpenAI-API („KI-Tipp“), sonst nach Durchschnitt.
- `raspbbery/` enthält eine ältere Variante, bei der ein Raspberry Pi die API abfragte
  und Ergebnisse an den Webserver meldete.

**Verwaltung** (`admin.php`)

- Tipprunden, Nutzer (inkl. Zahlungsstatus, Erinnerungsmail), Spiele und Ergebnisse
- Kommentare moderieren, Sondertipps auswerten
- Einstellungen in `set_setting` (Punkteschema, API-Zugang, Impressum, Datenschutz)
- Zuordnung der API-Spiel-IDs, Fehlerprotokoll, SQL-Hilfsfunktionen

## Installation (lokale Testinstallation)

Voraussetzungen: PHP 8.x mit `mysqli`, `curl` und `gd`, MySQL oder MariaDB, ein Webserver
(Apache/XAMPP) oder der eingebaute PHP-Webserver.

1. Repository in ein Webserver-Verzeichnis klonen.
2. Datenbank anlegen und Schema sowie Demodaten einspielen:

   ```sh
   mysql -u root -p -e "CREATE DATABASE tippspiel_demo CHARACTER SET utf8mb4;"
   mysql -u root -p tippspiel_demo < database/schema.sql
   mysql -u root -p tippspiel_demo < database/demo_data.sql
   ```

   `schema.sql` enthält bereits Gruppen, Teams und Spielplan der WM 2026 als
   Stammdaten (aus der livescore-api, ohne Ergebnisse).

3. Konfiguration anlegen:

   ```sh
   cp .env.example .env
   ```

4. Anwendung starten:

   ```sh
   php -S localhost:8000
   ```

5. `http://localhost:8000/` öffnen und mit `tipper1` / `demo1234` einloggen.
   Der Admin-Bereich (`admin.php`) ist mit `admin` / `demo1234` erreichbar.

Hinweise:

- Ohne SMTP-Daten funktionieren Aktivierungs- und Passwort-Mails nicht; die Demo-Nutzer
  sind bereits aktiviert.
- Ohne `api_key`/`api_secret` in `set_setting` liefert der Cronjob keine Ergebnisse.
  Ergebnisse lassen sich alternativ im Admin-Bereich manuell eintragen.
- Der Ordner `uplds/` muss für den Webserver beschreibbar sein.

## Konfiguration

Zugangsdaten liegen in `.env` (gelesen von `config.php`), fachliche Einstellungen in
der Tabelle `set_setting` (gepflegt über Admin > Settings).

| `.env`-Variable | Bedeutung |
|---|---|
| `DB_HOST`, `DB_NAME`, `DB_USER`, `DB_PASSWORD` | Datenbankverbindung |
| `APP_URL` | Basis-URL für Links in E-Mails, z. B. `http://localhost:8000/` |
| `SMTP_HOST`, `SMTP_PORT`, `SMTP_USER`, `SMTP_PASSWORD`, `SMTP_FROM` | Mailversand |
| `PAYMENT_PAYPAL`, `PAYMENT_IBAN`, `PAYMENT_BIC` | Zahlungshinweis in der Erinnerungsmail |
| `OPENAI_API_KEY` | optional, für KI-Autotipps |

| `set_setting`-Schlüssel | Bedeutung |
|---|---|
| `api_key`, `api_secret`, `api_competition_id` | Zugang zu livescore-api.com und Wettbewerbs-ID |
| `cron_secret` | Token, mit dem `cronjob.php?token=…` per HTTP aufgerufen werden darf |
| `punkte_*` | Punkteschema |
| `anzahl_gruppen`, `pokal`, `live_data_aktiv`, `usr_vergessen` | Turnierparameter |
| `email`, `impressum`, `datenschutz` | Kontakt und Rechtstexte der Installation |
| `openai_api_key` | Alternative zur `.env`-Variable |

## Projektstruktur

```
.
├── index.php            Login, Registrierung, Passwort vergessen, Impressum
├── member.php           Hauptanwendung für Teilnehmer (Tabelle, Tipps, Spielplan, Chat)
├── member_post.php      Formularverarbeitung der Teilnehmerseite
├── admin.php, admin_save.php   Verwaltung
├── aktivieren.php       Kontoaktivierung per Hash
├── cronjob.php          Ergebnisabholung und Bewertung (CLI oder per Token)
├── functions.php        Geschäftslogik (Bewertung, Autotipps, Mail, Upload)
├── showmats.php         Hilfsskript zum Abgleich der API-Spiel-IDs
├── raspbbery/           ältere Raspberry-Pi-Variante der Ergebnisabholung
├── phpmailer/           PHPMailer 5.2.23
├── assets/              CSS, JS (inkl. CKEditor), Fonts, Platzhalterbilder
├── database/            schema.sql, demo_data.sql
└── uplds/               Upload-Ziel für Chat-Bilder (leer)
```

## Projektstatus

Älteres Referenzprojekt (Entwicklung 2021 bis 2026), ursprünglich für den Einsatz im
Freundeskreis entwickelt. Bekannte Eigenheiten des Altbestands, die bewusst nicht
verändert wurden:

- Passwörter werden als MD5-Hash gespeichert.
- Die meisten SQL-Abfragen werden per String-Verkettung gebaut; nur der neuere
  `cronjob.php` nutzt Prepared Statements.
- Flaggen, Logo und Banner sind in dieser Veröffentlichung durch neutrale
  Platzhaltergrafiken ersetzt.

Das Projekt wird nur noch anlassbezogen zu Turnieren gepflegt.

## Lizenz

Der eigene Quellcode dieses Projekts steht unter der **GNU General Public License v3.0 oder später**
(siehe [LICENSE](LICENSE)).

Enthaltene Drittkomponenten behalten ihre jeweiligen Lizenzen: PHPMailer (LGPL 2.1),
CKEditor 4 (GPL 2+ / LGPL 2.1+ / MPL 1.1), jQuery, skel.js, scrollex, scrolly, poptrox (MIT),
simplyScroll (MIT/GPL), Font Awesome (SIL OFL 1.1 / MIT), Nunito Sans (SIL OFL 1.1),
Template „Snapshot“ von TEMPLATED (CC BY 3.0).
