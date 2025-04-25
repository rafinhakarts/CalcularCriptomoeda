<?php
error_reporting(1);
$targetPrice = null;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $investedAmount = floatval($_POST["invested_usd"]);
    $btcPrice = floatval($_POST["btc_price"]);
    $taxRate = 0.0015;

    // Verifica se o preço é maior que zero para evitar divisão por zero
    if ($btcPrice > 0 && $investedAmount > 0) {
        $coinQty = $investedAmount / $btcPrice;

        $calculationType = $_POST["calculation_type"];

        if ($calculationType === 'percent') {
            $profitPercent = floatval($_POST['porcentagem']) / 100;
            $profitUSD = $investedAmount * $profitPercent;
            $targetPrice = ($investedAmount + $profitUSD) / ($coinQty * (1 - $taxRate));
        } elseif ($calculationType === 'usd') {
            $profitUSD = floatval($_POST['porcentagem_usd']);
            $targetPrice = ($investedAmount + $profitUSD) / ($coinQty * (1 - $taxRate));
        }
    }
}

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Calculadora de Venda de Criptomoeda</title>
</head>
<body>
<center>
    <h2>Calculadora de Venda de Criptomoeda</h2>
    <p><strong>Obs:</strong> A taxa da Binance (0.15%) já está considerada.</p>

    <form method="post" onsubmit="return validarFormulario()">
        <label>Valor que será investido (USD):</label><br>
        <input type="number" name="invested_usd" placeholder="Ex: 500.00" step="0.01" required><br><br>

        <label>Preço atual da moeda (USD):</label><br>
        <input type="number" name="btc_price" placeholder="Ex: 30000.00" step="0.0001" required><br><br>

        <label>Tipo de cálculo:</label><br>
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
            <input type="number" name="porcentagem_usd" id="campoUSD" step="0.01"><br><br>
        </div>

        <button type="submit">Calcular</button>
    </form>

    <?php if ($targetPrice): ?>
        <h3>📈 Preço alvo de venda por moeda:</h3>
        <p><strong>$<?= number_format($targetPrice, 4, '.', ',') ?></strong></p>

        <?php if ($calculationType === 'percent'): ?>
            <p>Venda a esse valor para obter <strong><?= floatval($_POST['porcentagem']) ?>%</strong> de lucro líquido (com taxas)</p>
        <?php elseif ($calculationType === 'usd'): ?>
            <p>Venda a esse valor para obter <strong>$<?= number_format($_POST['porcentagem_usd'], 2, '.', ',') ?></strong> de lucro (com taxas)</p>
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
                alert("Informe o lucro desejado em USD.");
                campoUSD.focus();
                return false;
            }
        }
        return true;
    }
</script>
</body>
</html>
