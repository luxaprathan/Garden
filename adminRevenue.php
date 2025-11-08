<?php
include "db.php"; // Include the database connection

function getRevenue($period) {
    global $conn;

    // Call the stored procedure
    $sql = "CALL GetRevenue(?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $period);
    $stmt->execute();
    
    $result = $stmt->get_result();
    $revenue_data = [];

    while ($row = $result->fetch_assoc()) {
        $revenue_data[] = $row;
    }

    $stmt->close();
    return $revenue_data;
}

// Fetch revenue data
$yearly_revenue = getRevenue('year');
$monthly_revenue = getRevenue('month');
$weekly_revenue = getRevenue('week');

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Revenue Report</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            background-color: #f8f9fa;
            margin: 0;
        }
        h1 {
            color: #333;
        }
        .dashboard-card {
            display: inline-block;
            padding: 15px;
            margin: 10px;
            background: white;
            border-radius: 8px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
            width: 250px;
            font-size: 18px;
            font-weight: bold;
            cursor: pointer;
        }
        .dashboard-card:hover {
            background: #007bff;
            color: white;
        }
        .dashboard-card p{
            margin:0;
            margin-bottom:4px;
        }
        .dashboard-card span{
            font-size:15px;
        }
        .chart-container {
            width: 80%;
            margin: 20px auto;
            background: white;
            padding: 15px;
            border-radius: 10px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
            display: none;
        }
        .active {
            display: block;
        }
    </style>
</head>
<body>

    <h1>Revenue Dashboard</h1>

    <div class="dashboard-card" onclick="showChart('yearlyChart')">
    <p> 📅 Year Revenue </p><span id="totalYearly"> Current Year: $<?php echo number_format($yearly_revenue[0]['total_revenue'], 2); ?></span>
    </div>
    <div class="dashboard-card" onclick="showChart('monthlyChart')">
    <p>📊 Month Revenue </p><span id="totalMonthly">Current Month: $<?php echo number_format($monthly_revenue[0]['total_revenue'], 2); ?></span>
    </div>
    <div class="dashboard-card" onclick="showChart('weeklyChart')">
    <p> 📈 Week Revenue </p><span id="totalWeekly">Current Week: $<?php echo number_format($weekly_revenue[0]['total_revenue'], 2); ?></span>
    </div>

    <div id="yearlyChartContainer" class="chart-container active">
        <h2>Yearly Revenue</h2>
        <canvas id="yearlyChart"></canvas>
    </div>

    <div id="monthlyChartContainer" class="chart-container">
        <h2>Monthly Revenue</h2>
        <canvas id="monthlyChart"></canvas>
    </div>

    <div id="weeklyChartContainer" class="chart-container">
        <h2>Weekly Revenue</h2>
        <canvas id="weeklyChart"></canvas>
    </div>

    <script>
        // Data from PHP to JavaScript
        const yearlyData = <?php echo json_encode($yearly_revenue); ?>;
        const monthlyData = <?php echo json_encode($monthly_revenue); ?>;
        const weeklyData = <?php echo json_encode($weekly_revenue); ?>;

        // Extract Yearly Data for Chart
        const yearlyLabels = yearlyData.map(item => item.year);
        const yearlyRevenue = yearlyData.map(item => item.total_revenue);

        // Organize Monthly Revenue Correctly
        const monthlyGrouped = {};
        monthlyData.forEach(item => {
            const year = item.year;
            const monthIndex = parseInt(item.month) - 1;
            if (!monthlyGrouped[year]) {
                monthlyGrouped[year] = new Array(12).fill(0);
            }
            monthlyGrouped[year][monthIndex] = item.total_revenue;
        });

        const monthlyLabels = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 
                               'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];


        // Generate dataset with different colors
        const monthlyDatasets = Object.keys(monthlyGrouped).map(year => ({
            label: `Year ${year}`,
            data: monthlyGrouped[year],
            borderColor: getRandomColor(),
            backgroundColor: 'transparent',
            borderWidth: 2,
            tension: 0.3
        }));

        // Organize Weekly Revenue Correctly (Fixing Mapping Issue)
        const weeklyGrouped = {};
        weeklyData.forEach(item => {
            const year = item.year;
            const week = parseInt(item.week);
            if (!weeklyGrouped[year]) {
                weeklyGrouped[year] = new Array(52).fill(0);
            }
            weeklyGrouped[year][week - 1] = item.total_revenue;
        });


        function getRandomColor() {
            return `rgba(${Math.floor(Math.random() * 255)}, ${Math.floor(Math.random() * 255)}, ${Math.floor(Math.random() * 255)}, 0.8)`;
        }

        // Generate dataset with different colors
        const weeklyDatasets = Object.keys(weeklyGrouped).map(year => ({
            label: `Year ${year}`,
            data: weeklyGrouped[year],
            borderColor: getRandomColor(),
            backgroundColor: 'transparent',
            borderWidth: 2,
            tension: 0.3
        }));

        // Weekly Labels (1-52 weeks)
        const weeklyLabels = Array.from({ length: 52 }, (_, i) => i + 1);

        // Function to show only the selected chart
        function showChart(chartId) {
            document.getElementById('yearlyChartContainer').classList.remove('active');
            document.getElementById('monthlyChartContainer').classList.remove('active');
            document.getElementById('weeklyChartContainer').classList.remove('active');
            document.getElementById(chartId + 'Container').classList.add('active');
        }

        // Create Yearly Revenue Chart
        new Chart(document.getElementById('yearlyChart'), {
            type: 'bar',
            data: {
                labels: yearlyLabels,
                datasets: [{
                    label: 'Yearly Revenue ($)',
                    data: yearlyRevenue,
                    backgroundColor: 'rgba(54, 162, 235, 0.6)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });

       // Create Monthly Revenue Chart
new Chart(document.getElementById('monthlyChart'), {
    type: 'line',
    data: {
        labels: monthlyLabels,
        datasets: monthlyDatasets
    },
    options: {
        responsive: true,
        scales: {
            x: { title: { display: true, text: 'Month' } },
            y: { beginAtZero: true }
        }
    }
});
    
        // Create Weekly Revenue Chart
        new Chart(document.getElementById('weeklyChart'), {
            type: 'line',
            data: {
                labels: weeklyLabels,
                datasets: weeklyDatasets
            },
            options: {
                responsive: true,
                scales: {
                    x: { title: { display: true, text: 'Week (1-52)' } },
                    y: { beginAtZero: true }
                }
            }
        });

    </script>

</body>
</html>
