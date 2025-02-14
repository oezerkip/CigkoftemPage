# CigkoftemPage

***** Anmeldung als Administrator ******

## Admin-Ansicht muss über einen Query-String aufgerufen werden:

  /index.php?action=admin


  **** Nachdem sich der Admin abgemeldet hat, muss aus der URI die Endung ?action=logout entfernt werden *******


# Was schon funktional ist:

## Mainpage:

- Frontend zum großen Teil fertig
- Registrierung und dazugehörende Validierung
- Anmeldung als Benutzer (Login funktioniert, Logout noch nicht)

## Admin-Ansicht:

- Anmeldung + Abmeldung
- Styling in Bearbeitung
- Verwaltungspunkte bzw. Verwaltungsansichten angelegt aber nicht fertiggestellt


# Backend:

- MVC-Pattern umgesetzt
- Datenbankverbindung eingebunden
- Validierungsklasse erstellt und eingebunden
- Hauptklasse Application erstellt als Erweiterung des MVC-Patterns und als Grundplattform des Programms

# Frontend:

- Bootstrap eingebunden
- jQuery eingebunden
- Owl-library eingebunden für Slider
- Fonts angelegt
- SCSS wird mittels SASS-Compiler in main.css kompiliert
- Dummy-Bilder eingefügt
