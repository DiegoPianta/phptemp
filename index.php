<?php
try {
    // Connessione al database SQLite
    $conn = new PDO('sqlite:db.sqlite');
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Inizializzo le variabili
    $risultato = '';
    $numero1 = isset($_POST['numero1']) ? (float)$_POST['numero1'] : 0;
    $numero2 = isset($_POST['numero2']) ? (float)$_POST['numero2'] : 0;
    $operatore = isset($_POST['operatore']) ? $_POST['operatore'] : '';

    // Esecuzione delle operazioni
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if ($operatore == 'somma') {
            $risultato = $numero1 + $numero2;
        } elseif ($operatore == 'sottrazione') {
            $risultato = $numero1 - $numero2;
        } elseif ($operatore == 'moltiplicazione') {
            $risultato = $numero1 * $numero2;
        } elseif ($operatore == 'divisione') {
            if ($numero2 != 0) {
                $risultato = $numero1 / $numero2;
            } else {
                $risultato = 'Impossibile dividere per zero';
            }
        } else {
            $risultato = 'Operatore non valido';
        }

        // Visualizzo il risultato
        echo "<h3>Risultato: $risultato</h3>";

        // Salvataggio dei dati nel database
        if ($_POST['salva'] == '1') {
            $stmt = $conn->prepare("INSERT INTO calcoli (numero1, numero2, operatore, risultato) VALUES (:numero1, :numero2, :operatore, :risultato)");
            $stmt->bindParam(':numero1', $numero1, PDO::PARAM_STR);
            $stmt->bindParam(':numero2', $numero2, PDO::PARAM_STR);
            $stmt->bindParam(':operatore', $operatore, PDO::PARAM_STR);
            $stmt->bindParam(':risultato', $risultato, PDO::PARAM_STR);

            if ($stmt->execute()) {
                echo "<h3>Risultato salvato nel database!</h3>";
            } else {
                echo "<h3>Errore nel salvataggio dei dati:</h3>";
                print_r($stmt->errorInfo());
            }
        }
    }

} catch (PDOException $e) {
    echo "Errore nella connessione al database: " . $e->getMessage();
}
?>

<h2>Calcolatrice</h2>
<form method="POST" action="">
    <label for="numero1">Inserisci il primo numero:</label>
    <input type="number" id="numero1" name="numero1" required><br><br>

    <label for="numero2">Inserisci il secondo numero:</label>
    <input type="number" id="numero2" name="numero2" required><br><br>

    <label for="operatore">Seleziona l'operatore:</label>
    <select id="operatore" name="operatore" required>
        <option value="somma">Somma (+)</option>
        <option value="sottrazione">Sottrazione (-)</option>
        <option value="moltiplicazione">Moltiplicazione (*)</option>
        <option value="divisione">Divisione (/)</option>
    </select><br><br>

    <input type="submit" value="Calcola">
    <input type="hidden" name="salva" value="1"> <!-- Questo invia la richiesta per salvare nel database -->
</form>

