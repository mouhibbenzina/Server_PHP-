<?php
// Initialisation des variables
$errors = [];
$montantTVA = null;
$prixTTC = null;
$prixHT = '';
$tauxTVA = '';

// Vérifier si le formulaire a été soumis
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupérer et valider les données en provenance du formulaire
    $prixHT = isset($_POST['prixHT']) ? trim($_POST['prixHT']) : '';
    $tauxTVA = isset($_POST['tauxTVA']) ? trim($_POST['tauxTVA']) : '';

    // Validation basique
    if ($prixHT === '' || !is_numeric($prixHT)) {
        $errors[] = 'Veuillez entrer un prix HT valide (nombre).';
    }
    if ($tauxTVA === '' || !is_numeric($tauxTVA)) {
        $errors[] = 'Veuillez entrer un taux de TVA valide (nombre).';
    }

    // Convertir en float si pas d'erreur
    if (empty($errors)) {
        $prixHT = floatval($prixHT);
        $tauxTVA = floatval($tauxTVA);

        if ($prixHT < 0) {
            $errors[] = 'Le prix HT ne peut pas être négatif.';
        }
        if ($tauxTVA < 0) {
            $errors[] = 'Le taux de TVA ne peut pas être négatif.';
        }
    }

    // Calcul de la TVA et du prix TTC si tout est OK
    if (empty($errors)) {
        $montantTVA = $prixHT * ($tauxTVA / 100);
        $prixTTC = $prixHT + $montantTVA;

        // Formatage des résultats avec 2 décimales suivies de la monnaie
        $montantTVA = number_format($montantTVA, 2, '.', '') . ' DT';
        $prixTTC = number_format($prixTTC, 2, '.', '') . ' DT';
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calcul TVA</title>
</head>
<body>
    <div>
        <h2>Calcul de TVA</h2>
        <form id="Form" method="POST">
            <label for="prixHT">Prix HT :</label>
            <input type="number" id="prixHT" name="prixHT" step="0.001" required 
                   value="<?php echo isset($_POST['prixHT']) ? htmlspecialchars($_POST['prixHT']) : ''; ?>">
            <br><br>
            
            <label for="tauxTVA">Taux de TVA (%) :</label>
            <input type="number" id="tauxTVA" name="tauxTVA" step="0.001" required 
                   value="<?php echo isset($_POST['tauxTVA']) ? htmlspecialchars($_POST['tauxTVA']) : ''; ?>">
            <br><br>
            
            <button type="submit">Calculer</button>
        </form>
        <br>
        
        <?php if ($_SERVER['REQUEST_METHOD'] === 'POST' && $montantTVA !== ''): ?>
        <div id="res">
            <h3>Résultats :</h3>
            <div>
                <strong>Montant de la TVA :</strong>
                <input type="text" id="montantTVA" readonly 
                       value="<?php echo htmlspecialchars($montantTVA); ?>">
            </div>
            <br>
            <div>
                <strong>Prix TTC (Toutes Taxes Comprises) :</strong>
                <input type="text" id="prixTTC" readonly 
                       value="<?php echo htmlspecialchars($prixTTC); ?>">
            </div>
        </div>
        <?php endif; ?>
    </div>
</body>
</html>
