<?php
// Inizializziamo 
$prodotto = "";
$prezzo = "";
$sconto = "";

// Se form inviato esegui calcoli e log
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    // flag controllo validazione dati
    $dati_validi = true;

    // Validazione Input

    if (isset($_POST["prodotto"]) && trim($_POST["prodotto"]) != "" && strlen(trim($_POST["prodotto"])) <= 30) {
        $prodotto = trim($_POST["prodotto"]);
    } else {
        $prodotto = "NOT_VALID";
        $dati_validi = false;
    }

    if (isset($_POST["prezzo"]) && is_numeric($_POST["prezzo"]) && $_POST["prezzo"] > 0) {
        $prezzo = $_POST["prezzo"];
    } else {
        $prezzo = "NOT_VALID";
        $dati_validi = false;
    }
    
    if (isset($_POST["sconto"]) && is_numeric($_POST["sconto"]) && $_POST["sconto"] > 0 && $_POST["sconto"] <= 90) {
        $sconto = $_POST["sconto"];
    } else {
        $sconto = "NOT_VALID";
        $dati_validi = false;
    }

    // controllo validazione input e calcolo se validi
    if ($dati_validi) {
        $prezzo_scontato = $prezzo - ($prezzo * $sconto / 100);
        // Arrotondamento 2 decimali
        $prezzo_scontato_formattato = round($prezzo_scontato, 2);

        // calcolo fascia sconto
        if ($sconto <= 20) {
            $fascia_sconto = "PICCOLO";
        } else if ($sconto <= 49) {
            $fascia_sconto = "BUONO";
        } else if ($sconto >= 50) {
            $fascia_sconto = "SUPER";
        }
    } else {
        $prezzo_scontato_formattato = "NOT_VALID";
    }

    // scrittura log
    $fileLog = fopen("log.csv", "a");

    $record = "";
    $record .= date("d/m/Y");
    $record .= ",";
    $record .= date("H:i:s");
    $record .= ",";
    $record .= $_SERVER['REMOTE_ADDR'];
    $record .= ",";
    $record .= $prodotto;
    $record .= ",";
    $record .= $prezzo;
    $record .= ",";
    $record .= $prezzo_scontato_formattato;
    $record .= "\n";

    fwrite($fileLog, $record);
    fclose($fileLog);
}
?>



<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Calcolatore di sconto</title>
    <link rel="stylesheet" href="stile.css">
</head>
<body>



<?php
    // Mostriamo la card SOLO se siamo in POST e tutti i dati sono validi
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && $dati_validi):
?>

    <h1>Il tuo sconto</h1>

    <div class="card <?php echo $fascia_sconto; ?>">
        <h2>Prodotto: <?php echo $prodotto; ?></h2>
        <p class="dati-utente">Prezzo: <?php echo $prezzo; ?> € - Sconto: <?php echo $sconto; ?> %</p>
        <p class="prezzo-scontato"> Prezzo Scontato Arrotondato: <?php echo $prezzo_scontato_formattato; ?> €</p>
        <p class="fascia-sconto"><?php echo $fascia_sconto; ?></p>
    </div>

    <a href="index.php" class="link-ritorno">Calcola di nuovo</a>

<?php
    else:
?>

    <!-- Se siamo in GET o se i dati non erano validi, mostriamo il form -->
    <h1>Calcolatore di sconto</h1>
    <form method="post" action="index.php">
        <label for="prodotto">Prodotto</label>
        <input type="text" id="prodotto" name="prodotto" class=<?php echo ($prodotto == "NOT_VALID") ? "errore" : ""; ?> required>
        <?php if ($prodotto == "NOT_VALID"): ?>
            <span class="msg-errore">Il prodotto deve essere massimo 30 caratteri</span>
        <?php endif; ?>
        <label for="prezzo">Prezzo (€)</label>
        <input type="text" id="prezzo" name="prezzo" class=<?php echo ($prezzo == "NOT_VALID") ? "errore" : ""; ?> required>
        <?php if ($prezzo == "NOT_VALID"): ?>
           <p class="msg-errore">Il prezzo deve essere maggiore di 0</p>
        <?php endif; ?>
        <label for="sconto">Sconto (%)</label>
        <input type="text" id="sconto" name="sconto" class=<?php echo ($sconto == "NOT_VALID") ? "errore" : ""; ?> required>
        <?php if ($sconto == "NOT_VALID"): ?>
           <p class="msg-errore">Il sconto deve essere un numero tra 1 e 90</p>
        <?php endif; ?>
        <button type="submit">Calcola</button>
    </form>

<?php 
    endif;
?>
</body>
</html>