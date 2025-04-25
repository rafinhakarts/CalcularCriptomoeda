<?php
error_reporting(0);
$targetPrice = null;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $btcPrice = floatval($_POST["btc_price"]);
    $taxRate = 0.0015; // 0.075% compra + 0.075% venda

    $calculationType = $_POST["calculation_type"];

    if ($calculationType === 'percent') {
        $desiredProfit = $_POST['porcentagem'] / 100;
        $totalGain = $desiredProfit - $taxRate;
        $targetPrice = $btcPrice * (1 + $totalGain);
    } elseif ($calculationType === 'usd') {
        $desiredProfitUSD = floatval($_POST['porcentagem_usd']);
        $targetPrice = ($btcPrice + $desiredProfitUSD) / (1 - $taxRate);
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Calculadora de Lucro Criptomoedas</title>
</head>
<body>
<center>
    <h2>Calculadora de Lucro Criptomoedas</h2>
    <p><strong>Obs:</strong> Já leva em consideração as taxas da Binance (0.15%)</p>

    <form method="post" onsubmit="return validarFormulario()">
        <label>Preço atual da Criptomoeda (USD):</label><br>
        <input type="number" name="btc_price" placeholder="Ex: 30000.00" step="0.0001" required><br><br>

        <label>Escolha o tipo de cálculo:</label><br>
        <select name="calculation_type" id="tipoCalculo" required>
            <option value="percent">Percentual de lucro (%)</option>
            <option value="usd">Lucro em USD</option>
        </select><br><br>

        <div id="percent-form">
            <label>% de lucro desejado:</label><br>
            <input type="number" name="porcentagem" id="campoPercentual" step="0.01"><br><br>
        </div>

        <div id="usd-form" style="display:none;">
            <label>Lucro desejado em USD:</label><br>
            <input type="number" name="porcentagem_usd" id="campoUSD" step="0.0001"><br><br>
        </div>

        <button type="submit">Calcular</button>
    </form>

    <?php if ($targetPrice): ?>
        <h3>📈 Preço alvo:</h3>
        <p><strong>$<?= number_format($targetPrice, 4, '.', ',') ?></strong></p>

        <?php if ($calculationType === 'percent'): ?>
            <p>Venda a esse valor para obter <strong><?= floatval($_POST['porcentagem']) ?>%</strong> de lucro líquido (com taxas)</p>
        <?php elseif ($calculationType === 'usd'): ?>
            <p>Venda a esse valor para obter <strong>$<?= number_format($_POST['porcentagem_usd'], 4, '.', ',') ?></strong> de lucro (com taxas)</p>
        <?php endif; ?>
    <?php endif; ?>
</center>

<script>
    const tipoCalculo = document.getElementById('tipoCalculo');
    const percentForm = document.getElementById('percent-form');
    const usdForm = document.getElementById('usd-form');
    const campoPercentual = document.getElementById('campoPercentual');
    const campoUSD = document.getElementById('campoUSD');

    tipoCalculo.addEventListener('change', function () {
        if (this.value === 'percent') {
            percentForm.style.display = 'block';
            usdForm.style.display = 'none';
        } else {
            percentForm.style.display = 'none';
            usdForm.style.display = 'block';
        }
    });

    function validarFormulario() {
        if (tipoCalculo.value === 'percent') {
            if (!campoPercentual.value) {
                alert("Informe o percentual de lucro.");
                campoPercentual.focus();
                return false;
            }
        } else {
            if (!campoUSD.value) {
                alert("Informe o lucro em USD.");
                campoUSD.focus();
                return false;
            }
        }
        return true;
    }
</script>
</body>
</html>
