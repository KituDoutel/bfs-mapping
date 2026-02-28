<?php
// index.php - 12 Interasaun Layout
session_start();
require_once 'config.php';
require_once 'functions.php';

$mapaFunc = new MapaFunctions();
$mapaFunc->createTablesIfNotExist();

// Hola dadus ba Interasaun I to'o XII
$interasaunHotu = [];
for ($i = 1; $i <= 12; $i++) {
    $kategoria = '';
    switch($i) {
        case 1: $kategoria = 'I'; break;
        case 2: $kategoria = 'II'; break;
        case 3: $kategoria = 'III'; break;
        case 4: $kategoria = 'IV'; break;
        case 5: $kategoria = 'V'; break;
        case 6: $kategoria = 'VI'; break;
        case 7: $kategoria = 'VII'; break;
        case 8: $kategoria = 'VIII'; break;
        case 9: $kategoria = 'IX'; break;
        case 10: $kategoria = 'X'; break;
        case 11: $kategoria = 'XI'; break;
        case 12: $kategoria = 'XII'; break;
    }
    $interasaunHotu[$i] = [
        'kategoria' => $kategoria,
        'lokasaun' => $mapaFunc->getLokasaunPorKategoria($kategoria)
    ];
}

// Divide lokasaun ba koluna eskerda no direita
function divideLokasaun($lokasaun) {
    $total = count($lokasaun);
    $metade = ceil($total / 2);
    return [
        'eskerda' => array_slice($lokasaun, 0, $metade),
        'direita' => array_slice($lokasaun, $metade)
    ];
}
?>
<!DOCTYPE html>
<html lang="tet">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>METODU BFS - 12 INTERASAUN</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <!-- Header -->
    <div class="header text-center">
        <div class="container-fluid">
            <button class="btn btn-secondary btn-sm float-start" onclick="history.back()">
                <i class="fas fa-arrow-left"></i> BACK
            </button>
            <h3 class="mb-0"><i class="fas fa-map-marked-alt"></i> METODU BFS</h3>
        </div>
    </div>

    <div class="container-fluid px-2">
        <div class="row g-2 mt-2">
            <?php for ($i = 1; $i <= 12; $i++): 
                $data = $interasaunHotu[$i];
                $divided = divideLokasaun($data['lokasaun']);
                $kategoria = $data['kategoria'];
            ?>
            <!-- Interasaun <?= $i ?> -->
            <div class="col-xl-3 col-lg-4 col-md-6">
                <div class="panel panel-<?= $i ?>">
                    <h5 class="text-center mb-2">Interasi <?= $i ?></h5>
                    
                    <div class="row g-1">
                        <!-- Koluna Eskerda -->
                        <div class="col-6">
                            <?php if (!empty($divided['eskerda'])): ?>
                                <?php foreach($divided['eskerda'] as $lok): ?>
                                <div class="location-item">
                                    <input type="checkbox" class="form-check-input lok-check" 
                                           data-kategoria="<?= $kategoria ?>" 
                                           data-interasaun="<?= $i ?>"
                                           data-id="<?= $lok['id'] ?>"
                                           data-distansia="<?= $lok['distansia'] ?>">
                                    <label class="form-check-label ms-1 flex-grow-1">
                                        <?= htmlspecialchars($lok['naran']) ?>
                                    </label>
                                    <input type="text" class="form-control distansia-input" 
                                           value="<?= $lok['distansia'] ?>" readonly>
                                </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Koluna Direita -->
                        <div class="col-6">
                            <?php if (!empty($divided['direita'])): ?>
                                <?php foreach($divided['direita'] as $lok): ?>
                                <div class="location-item">
                                    <input type="checkbox" class="form-check-input lok-check" 
                                           data-kategoria="<?= $kategoria ?>"
                                           data-interasaun="<?= $i ?>"
                                           data-id="<?= $lok['id'] ?>"
                                           data-distansia="<?= $lok['distansia'] ?>">
                                    <label class="form-check-label ms-1 flex-grow-1">
                                        <?= htmlspecialchars($lok['naran']) ?>
                                    </label>
                                    <input type="text" class="form-control distansia-input" 
                                           value="<?= $lok['distansia'] ?>" readonly>
                                </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <!-- Total Button -->
                    <div class="mt-2 text-center">
                        <button class="btn btn-warning btn-sm w-100" onclick="kalkulaTotal(<?= $i ?>)">
                            TOTAL
                        </button>
                        <input type="text" class="form-control form-control-sm mt-1" 
                               id="total_<?= $i ?>" readonly>
                    </div>
                </div>
            </div>
            <?php endfor; ?>
        </div>

        <!-- Control Panel -->
        <div class="row mt-3 mb-3">
            <div class="col-md-12">
                <div class="control-panel">
                    <div class="row">
                        <div class="col-md-3">
                            <button class="btn btn-success w-100 mb-2" onclick="kalkulaSolusaun()">
                                <i class="fas fa-calculator"></i> KALKULA SOLUSAUN (BFS)
                            </button>
                            <button class="btn btn-primary w-100 mb-2" onclick="raiDatabase()">
                                <i class="fas fa-save"></i> RAI BA DATABASE
                            </button>
                            <button class="btn btn-info w-100 mb-2" onclick="seleksionaHotu()">
                                <i class="fas fa-check-square"></i> SELEKSIONA HOTU
                            </button>
                            <button class="btn btn-warning w-100" onclick="deseleksionaHotu()">
                                <i class="fas fa-square"></i> DESELEKSIONA HOTU
                            </button>
                        </div>
                        <div class="col-md-9">
                            <div class="result-area" id="resultadoArea">
                                <h6><i class="fas fa-chart-bar"></i> REZULTADU</h6>
                                <div class="progress mb-2" style="height: 25px;">
                                    <div class="progress-bar progress-bar-striped progress-bar-animated" 
                                         id="progressBar" role="progressbar" style="width: 0%">0%</div>
                                </div>
                                <textarea class="form-control" id="resultaduTotal" rows="8" readonly
                                    placeholder="Rezultadu sei hatudu iha ne'e..."></textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="script.js"></script>
</body>
</html>