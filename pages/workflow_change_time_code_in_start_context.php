<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Workflow</title>
    <link rel="stylesheet" href="../style/style.css">
    <style>
        /* Styles locaux — utilisent !important pour surpasser le CSS global si besoin */
        body {
            font-family: Arial, Helvetica, sans-serif;
            color: #222 !important;
            margin: 18px;
        }
        ul {
            padding: 0;
            margin: 0 0 20px 0;
            list-style: none;
            background: white;
            color: black;
        }
        ul li { display: inline-block; margin: 0; }
        ul li a { display:inline-block; padding:10px 16px; color: #fff; text-decoration:none; }
        h3 { color: #005a8d; margin-top: 0; }

        /* Encadré du flux sélectionné */
        .selected-flux {
            font-size: 1.05em;
            color: #222 !important;
            background-color: #f1f1f1 !important;
            padding: 10px;
            border-radius: 6px;
            display: inline-block;
            border: 1px solid #d0d0d0;
        }

        /* Zone des checkboxes */
        .flux-list { margin-top: 10px; padding: 10px 0; }
        .flux-list label { display: block; margin: 6px 0; color: #222 !important; }

        /* Bouton */
        .btn {
            margin-top: 12px;
            padding: 8px 14px;
            font-size: 14px;
            cursor: pointer;
            border: none;
            background-color: #005a8d;
            color: white;
            border-radius: 6px;
        }
        .btn:hover { background-color: #0074c2; }

        /* Résultat export — boîte claire et contrastée */
        .result-box {
            margin-top: 20px;
            background-color: #f8f9fa !important;
            border: 1px solid #ccc !important;
            border-radius: 8px;
            padding: 15px;
            color: #222 !important;
            box-shadow: 0 1px 4px rgba(0,0,0,0.04);
        }

        /* Small note */
        .note { font-size: 0.95em; color: #444; margin-top: 6px; }
    </style>
</head>
<body>

<ul>
  <li><a class="active" href="../index.php">Tools</a></li>
  <li><a href="workflow.php">Workflow</a></li>
  <li><a href="workflow.php">Change time code in start context</a></li>
</ul>

<?php
include '../class/class_workflow.php';

$mywkf = new cl_workflow;
$allwkf = $mywkf->getWorkflowFromName('ABS');

// Helper pour vérifier si un flux (wkf_id;wkf_name) est dans le tableau POST fluxes[]
function flux_is_checked($value) {
    if (!empty($_POST['fluxes']) && is_array($_POST['fluxes'])) {
        return in_array($value, $_POST['fluxes']);
    }
    return false;
}

// Si on est en phase de sélection initiale (pas encore choisi de flux principal)
if (!isset($_POST['selected_flux'])) {

    echo '<form method="post" action="">';
    echo "<h3>Sélectionnez un flux principal :</h3>";

    foreach ($allwkf as $line) {
        $flux_value = $line['wkf_id'] . ";" . $line['wkf_name'];
        echo '<label>';
        echo '<input type="radio" name="selected_flux" value="' . htmlspecialchars($flux_value) . '"> ';
        echo htmlspecialchars($line['wkf_id'] . " - " . $line['wkf_name']);
        echo '<BR></label>';
    }

    echo '<br><button class="btn" type="submit">Suivant</button>';
    echo '</form>';

} else {
    // Flux principal choisi (phase d'export)
    $selected_flux = $_POST['selected_flux'];
    list($selected_id, $selected_name) = explode(';', $selected_flux, 2);

    // Affichage du flux sélectionné — en police lisible
    echo "<h3>Flux sélectionné :</h3>";
    echo "<div class='selected-flux'>" . htmlspecialchars($selected_id) . " - " . htmlspecialchars($selected_name) . "</div>";
    echo "<hr>";

    // Formulaire pour sélectionner les autres flux et exporter
    echo '<form method="post" action="">';
    // On remet le flux sélectionné dans un champ caché pour persistance
    echo '<input type="hidden" name="selected_flux" value="' . htmlspecialchars($selected_flux) . '">';

    echo "<h3>Sélectionnez d'autres flux à exporter :</h3>";

    // Case "Sélectionner tous les flux"
    // On pré-cochera la case "selectAll" si toutes les autres sont cochées
    $other_flux_count = 0;
    $checked_count = 0;
    foreach ($allwkf as $line) {
        $flux_value = $line['wkf_id'] . ";" . $line['wkf_name'];
        if ($flux_value === $selected_flux) continue;
        $other_flux_count++;
        if (flux_is_checked($flux_value)) $checked_count++;
    }
    $selectAll_checked = ($other_flux_count > 0 && $checked_count === $other_flux_count) ? ' checked' : '';

    echo '<label><input type="checkbox" id="selectAll"' . $selectAll_checked . '> <strong>Sélectionner tous les flux</strong></label><br>';

    echo '<div class="flux-list">';
    // Affiche la liste des autres flux, en restaurant l'état coché si présent dans POST
    foreach ($allwkf as $line) {
        $flux_value = $line['wkf_id'] . ";" . $line['wkf_name'];
        if ($flux_value === $selected_flux) continue;

        $isChecked = flux_is_checked($flux_value) ? ' checked' : '';
        echo '<label>';
        echo '<input type="checkbox" class="flux-checkbox" name="fluxes[]" value="' . htmlspecialchars($flux_value) . '"' . $isChecked . '> ';
        echo htmlspecialchars($line['wkf_id'] . " - " . $line['wkf_name']);
        echo '</label>';
    }
    echo '</div>';

    echo '<button class="btn" type="submit" name="export">Exporter</button>';
    echo '</form>';

    // Si on a cliqué sur Exporter, afficher le résultat dans une boîte visible
    if (isset($_POST['export'])) {
        echo '<div class="result-box">';
        echo "<h3>Résultat de l'export :</h3>";
        echo "<p><strong>Flux principal :</strong> " . htmlspecialchars($selected_id . " - " . $selected_name) . "</p>";

        $myWsc = new cl_workflow;
        // $myWsc = $myWsc->getWorkflowStartContextFromWkfId($selected_id);
        $myWsc = $myWsc->getWorkflowStartContextFromWkfId('000001-20250915-0000029895');
        $myWscParam = $myWsc['wsc_parameters'];
        echo $myWscParam;
        echo '<BR><BR>';

        $myWsc2 = new cl_workflow;
        $myWsc2 = $myWsc2->getWorkflowStartContextFromWkfId('000001-20251009-0000004703');
        $myWscParam2 = $myWsc2['wsc_parameters'];
        echo $myWscParam2;
        echo '<BR><BR>';

        

        if (!empty($_POST['fluxes'])) {
            echo "<p><strong>Flux supplémentaires sélectionnés :</strong></p><ul>";
            foreach ($_POST['fluxes'] as $flux) {
                list($id, $name) = explode(';', $flux, 2);
                echo "<li>" . htmlspecialchars($id) . " - " . htmlspecialchars($name) . "</li><BR>";
            }
            echo "</ul>";
        } else {
            echo "<p><em>Aucun autre flux sélectionné.</em></p>";
        }
        echo '</div>';
    }
}
?>

<!-- JS pour "Sélectionner tous les flux" et synchronisation d'état -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const selectAll = document.getElementById('selectAll');
    const checkboxes = Array.from(document.querySelectorAll('.flux-checkbox'));

    if (!selectAll) return;

    // Lorsque on change selectAll : coche/décoche toutes les cases
    selectAll.addEventListener('change', function() {
        checkboxes.forEach(cb => cb.checked = selectAll.checked);
    });

    // Met à jour la case "selectAll" si l'utilisateur coche/décoche individuellement
    checkboxes.forEach(cb => {
        cb.addEventListener('change', function() {
            const allChecked = checkboxes.length > 0 && checkboxes.every(c => c.checked);
            selectAll.checked = allChecked;
        });
    });
});
</script>

</body>
</html>
