<?php
error_reporting(0);
$targetPrice = null;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $btcPrice = floatval($_POST["btc_price"]);
    $taxRate = 0.0015;

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
<html>
<head>
    <title>Calculadora de Lucro Criptomoedas</title>
</head>
<body>
<center>
    <h2>Calculadora de Lucro Criptomoedas</h2>
    <p>Obs: Já leva em consideração as taxas da Binance: 0.075%+0.075% = 0.15%</p>

    <form method="post" onsubmit="return validarFormulario()">
        <label>Preço atual da Criptomoeda (USD):</label>
        <p>Use valor inteiro (ex: $95000 para USD $95.000)</p>
        <input type="number" name="btc_price" placeholder="Valor da moeda" step="0.01" required>

        <p></p>
        <label>Escolha o tipo de cálculo:</label>
        <select name="calculation_type" id="tipoCalculo" required>
            <option value="percent">Percentual de lucro</option>
            <option value="usd">Valor em USD</option>
        </select>

        <p></p>
        <div id="percent-form">
            <label>% de lucro desejado:</label>
            <input type="number" name="porcentagem" id="campoPercentual" step="1">
        </div>

        <div id="usd-form" style="display:none;">
            <label>Valor de lucro desejado (USD):</label>
            <input type="number" name="porcentagem_usd" id="campoUSD" step="0.01">
        </div>

        <button type="submit">Calcular</button>
    </form>

    <?php if ($targetPrice): ?>
        <p>📈 Preço alvo: <strong>$<?= number_format($targetPrice, 2, '.', ',') ?></strong></p>
        <?php if ($calculationType === 'percent'): ?>
            <p>Venda a esse valor para obter <?= $_POST['porcentagem'] ?>% de lucro líquido (com taxas)</p>
        <?php elseif ($calculationType === 'usd'): ?>
            <p>Venda a esse valor para obter $<?= number_format($_POST['porcentagem_usd'], 2, '.', ',') ?> de lucro (com taxas)</p>
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
                alert("Informe o % de lucro.");
                campoPercentual.focus();
                return false;
            }
        } else {
            if (!campoUSD.value) {
                alert("Informe o valor de lucro em USD.");
                campoUSD.focus();
                return false;
            }
        }
        return true;
    }
</script>
</body>
</html>
