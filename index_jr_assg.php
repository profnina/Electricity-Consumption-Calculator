<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Electricity Consumption Calculator</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet">
    <script>
        function clearForm() {
            document.getElementById('voltage').value = '';
            document.getElementById('current').value = '';
            document.getElementById('rate').value = '';
        }
        function showAlert() {
            alert('Electricity consumption have been calculated!');
        }
    </script>
</head>
<body>
    <div class="container mt-5">
        <h2 class="text-center">Electricity Consumption Calculator</h2>
        <form method="POST" action="" onsubmit="showAlert()">
            <div class="form-group">
                <label for="voltage">Voltage (V)</label>
                <input type="number" class="form-control" id="voltage" name="voltage" step="0.01" value="<?= htmlspecialchars($_POST['voltage'] ?? '') ?>" required>
            </div>
            <div class="form-group">
                <label for="current">Current (A)</label>
                <input type="number" class="form-control" id="current" name="current" step="0.01" value="<?= htmlspecialchars($_POST['current'] ?? '') ?>" required>
            </div>
            <div class="form-group">
                <label for="rate">Current Rate (sen/kWh)</label>
                <input type="number" class="form-control" id="rate" name="rate" step="0.01" value="<?= htmlspecialchars($_POST['rate'] ?? '') ?>" required>
            </div>
            <button type="submit" class="btn btn-primary btn-block">Calculate</button>
            <button type="button" class="btn btn-secondary btn-block" onclick="clearForm()">Reset</button>
        </form>

        <?php
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['voltage'], $_POST['current'], $_POST['rate'])) {
            $voltage = $_POST['voltage'] ?? 0;
            $current = $_POST['current'] ?? 0;
            $rate = $_POST['rate'] ?? 0;
            $hours = 24; // Default to 24 hours

            // Input validation&calculation
            if ($voltage <= 0 || $current <= 0 || $rate <= 0) {
                echo "<div class='alert alert-danger mt-4'>Please enter valid positive values for all fields.</div>";
            } else {     
                $power = ($voltage * $current) / 1000; //calc power kwh
                $daily_energy = $power * $hours; //calc daily energy kwh
                $daily_cost = $daily_energy * ($rate / 100); //calc cost/day RM

                echo "<div class='card mt-4'>";
                echo "<div class='card-header text-center bg-dark text-white'>";
                echo "<h4 class='card-title mb-0'>Results</h4>";
                echo "</div>";
                echo "<div class='card-body'>";
                echo "<p><strong>Power:</strong> " . number_format($power, 5) . " kW</p>";
                echo "<p><strong>Daily Energy Consumption:</strong> " . number_format($daily_energy, 5) . " kWh</p>";
                echo "<p><strong>Daily Cost:</strong> RM " . number_format($daily_cost, 2) . "</p>";
                echo "</div>";
                echo "</div>";

                echo "<div class='table-responsive mt-4'>";
                echo "<h5 class='text-center'>Hourly Breakdown</h5>";
                echo "<table class='table table-striped table-bordered'>";
                echo "<thead class='thead-dark'><tr><th>Hour</th><th>Energy (kWh)</th><th>Total Cost (RM)</th></tr></thead>";
                echo "<tbody>";

                for ($i = 1; $i <= $hours; $i++) {
                    $hourly_energy = $power * $i;
                    $hourly_total = $hourly_energy * ($rate / 100);
                    echo "<tr>";
                    echo "<td>$i</td>";
                    echo "<td>" . number_format($hourly_energy, 5) . "</td>";
                    echo "<td>" . number_format($hourly_total, 2) . "</td>";
                    echo "</tr>";
                }

                echo "</tbody></table>";
                echo "</div>";
            }
        }
        ?>
    </div>
</body>
</html>
