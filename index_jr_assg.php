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
        function calculatePower ($voltage, $current){
            return ($voltage * $current) / 1000;  //kw
        }

        function calculateDailyEnergy ($power, $hours = 24){
            return $power * $hours; //kwh
        }

        function calculateDailyCost ($daily_energy, $rate){
            return $daily_energy * ($rate / 100); //rm
        }

        function calculateHourlyBreakdown($power, $rate, $hours = 24) {
            $results = [];
            for ($i = 1; $i <= $hours; $i++) {
                $hourly_energy = $power * $i;
                $hourly_total = $hourly_energy * ($rate / 100);
                $results[] = [
                    'hour' => $i,
                    'energy' => $hourly_energy,
                    'cost' => $hourly_total
                ];
            }
            return $results;
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['voltage'], $_POST['current'], $_POST['rate'])) {
            $voltage = $_POST['voltage'] ?? 0;
            $current = $_POST['current'] ?? 0;
            $rate = $_POST['rate'] ?? 0;
            $hours = 24; // default to 24 hours

            // input validation&calculation
            if ($voltage <= 0 || $current <= 0 || $rate <= 0) {
                echo "<div class='alert alert-danger mt-4'>Please enter valid positive values for all fields.</div>";
            } else {     
                $daily_power = calculatePower($voltage, $current);//call func calc power kwh
                $daily_energy = calculateDailyEnergy($daily_power, $hours); //call func calc daily energy kwh
                $daily_cost = calculateDailyCost($daily_energy, $rate); //call func calc cost/day RM

                echo "<div class='card mt-4'>";
                echo "<div class='card-header text-center bg-dark text-white'>";
                echo "<h4 class='card-title mb-0'>Results</h4>";
                echo "</div>";
                echo "<div class='card-body'>";
                echo "<p><strong>Power:</strong> " . number_format($daily_power, 5) . " kW</p>";
                echo "<p><strong>Daily Energy Consumption:</strong> " . number_format($daily_energy, 5) . " kWh</p>";
                echo "<p><strong>Daily Cost:</strong> RM " . number_format($daily_cost, 2) . "</p>";
                echo "</div>";
                echo "</div>";

                echo "<div class='table-responsive mt-4'>";
                echo "<h5 class='text-center'>Hourly Breakdown</h5>";
                echo "<table class='table table-striped table-bordered'>";
                echo "<thead class='thead-dark'><tr><th>Hour</th><th>Energy (kWh)</th><th>Total Cost (RM)</th></tr></thead>";
                echo "<tbody>";

                $hourly_results = calculateHourlyBreakdown($daily_power, $rate, $hours);  //call func calc hourly
                foreach ($hourly_results as $result) {
                    echo "<tr>";
                    echo "<td>{$result['hour']}</td>";
                    echo "<td>" . number_format($result['energy'], 5) . "</td>";
                    echo "<td>" . number_format($result['cost'], 2) . "</td>";
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
