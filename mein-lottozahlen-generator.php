<?php
/**
 * Plugin Name: Mein Lottozahlen Generator
 * Description: Definiert einen Shortcode [lottozahlen] , um Lottozahlen im Inhalt auszugeben. Dieser Code ist ein WordPress Plugin. 
 * Version:     1.0.0
 * Author:      Ksynsky
 */

function lottozahlen_generator(int $anzahl_zahlen = 6, int $zahlen_bereich = 49, bool $mit_zusatzzahl = false): array
{
    /**
     * Generiert zufällige Lottozahlen.
     *
     * @param int $anzahl_zahlen Anzahl der Hauptzahlen (Standard: 6)
     * @param int $zahlen_bereich Maximaler Zahlenbereich (Standard: 49, z.B. für Lotto 6 aus 49)
     * @param bool $mit_zusatzzahl Soll eine Zusatzzahl generiert werden? (Standard: false)
     * @return array Ein Array mit den Lottozahlen (und optional der Zusatzzahl)
     */

    $lottozahlen = [];
    $verfuegbare_zahlen = range(1, $zahlen_bereich); // Array mit verfügbaren Zahlen erstellen

    if ($anzahl_zahlen > $zahlen_bereich) {
        return ['error' => 'Anzahl der Zahlen darf nicht größer sein als der Zahlenbereich.'];
    }

    if ($anzahl_zahlen <= 0 || $zahlen_bereich <= 0) {
        return ['error' => 'Anzahl der Zahlen und Zahlenbereich müssen positiv sein.'];
    }


    // Zufällige Hauptzahlen generieren (ohne Wiederholung)
    for ($i = 0; $i < $anzahl_zahlen; $i++) {
        $zufalls_index = array_rand($verfuegbare_zahlen); // Zufälligen Index aus verfügbaren Zahlen wählen
        $lottozahl = $verfuegbare_zahlen[$zufalls_index]; // Lottozahl anhand des Indexes holen
        unset($verfuegbare_zahlen[$zufalls_index]); // Gewählte Zahl aus verfügbaren Zahlen entfernen (keine Wiederholung)
        $lottozahlen[] = $lottozahl; // Lottozahl zum Ergebnis-Array hinzufügen
    }

    sort($lottozahlen); // Lottozahlen aufsteigend sortieren

    if ($mit_zusatzzahl) {
        $zusatzzahl = rand(0, 9); // Zufällige Zusatzzahl zwischen 0 und 9 (Beispielbereich)
        $lottozahlen['zusatzzahl'] = $zusatzzahl; // Zusatzzahl zum Ergebnis-Array hinzufügen
    }

    return $lottozahlen;
}

function mein_lottozahlen_shortcode_funktion( $atts ) { // Shortcode Funktion umbenannt
    $attribute = shortcode_atts( array( // Attribute definieren (optional)
        'farbe' => '#000', // Standardfarbe schwarz
        'hintergrund' => '#fff', // Standardhintergrund weiss
    ), $atts );

    $lotto_ergebnis = lottozahlen_generator(6, 49, true); // Lottozahlen HIER generieren und speichern

    if (isset($lotto_ergebnis['error'])) { // Fehlerbehandlung, falls lottozahlen_generator() einen Fehler zurückgibt
        return '<p style="color:red;">Fehler beim Generieren der Lottozahlen: ' . esc_html($lotto_ergebnis['error']) . '</p>';
    }

    $ausgabe = '<div style="padding:10px; color:' . esc_attr( $attribute['farbe'] ) . '; background-color:' . esc_attr( $attribute['hintergrund'] ) . ';">';
    $ausgabe .= "<p><strong>Lottozahlen (6 aus 49):</strong></p><p>"; // Überschrift für Hauptzahlen
    foreach ($lotto_ergebnis as $key => $zahl) { // Durchlaufe das Array
        if ($key !== 'zusatzzahl') { // Zusatzzahl überspringen (wird separat ausgegeben)
            $ausgabe .= esc_html($zahl) . " "; // Hauptzahlen ausgeben
        }
    }
    $ausgabe .= "</p>";
    if (isset($lotto_ergebnis['zusatzzahl'])) {
        $ausgabe .= "<p><strong>Zusatzzahl:</strong> " . esc_html($lotto_ergebnis['zusatzzahl']) . "</p>"; // Zusatzzahl ausgeben
    }
    $ausgabe .= '</div>';

    return $ausgabe;
}
add_shortcode( 'lottozahlen', 'mein_lottozahlen_shortcode_funktion' ); // Shortcode Name korrigiert