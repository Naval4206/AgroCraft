<?php
// Enable error reporting
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "agrocraft";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch total products
$productQuery = $conn->query("SELECT COUNT(*) AS total FROM products");
if (!$productQuery) {
    die("Error in product query: " . $conn->error);
}
$productCount = $productQuery->fetch_assoc()['total'];

// Fetch total categories
$categoryQuery = $conn->query("SELECT COUNT(*) AS total FROM categories");
if (!$categoryQuery) {
    die("Error in category query: " . $conn->error);
}
$categoryCount = $categoryQuery->fetch_assoc()['total'];

// Fetch total stock
$stockQuery = $conn->query("SELECT SUM(stock) AS total FROM products");
if (!$stockQuery) {
    die("Error in stock query: " . $conn->error);
}
$stockCount = $stockQuery->fetch_assoc()['total'];

// Fetch top selling products
$topProducts = $conn->query("SELECT name, sales FROM products ORDER BY sales DESC LIMIT 5");
if (!$topProducts) {
    die("Error in top products query: " . $conn->error);
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Analytics Dashboard</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body { font-family: Arial, sans-serif; text-align: center; }
        .dashboard { display: flex; justify-content: space-around; padding: 20px; }
        .card { padding: 20px; border-radius: 10px; box-shadow: 0 0 10px #ccc; }
        canvas { max-width: 600px; }
    </style>
</head>
<body>
    <h1>Data Analytics Dashboard</h1>
    <div class="dashboard">
        <div class="card">
            <h2>Total Products</h2>
            <p><?php echo $productCount; ?></p>
        </div>
        <div class="card">
            <h2>Total Categories</h2>
            <p><?php echo $categoryCount; ?></p>
        </div>
        <div class="card">
            <h2>Total Stock</h2>
            <p><?php echo $stockCount; ?></p>
        </div>
    </div>
    
    <canvas id="salesChart"></canvas>
    
    <script>
        var ctx = document.getElementById('salesChart').getContext('2d');
        var salesChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: [<?php $topProducts->data_seek(0); while ($row = $topProducts->fetch_assoc()) { echo "'".$row['name']."',"; } ?>],
                datasets: [{
                    label: 'Top Selling Products',
                    data: [<?php $topProducts->data_seek(0); while ($row = $topProducts->fetch_assoc()) { echo $row['sales'].","; } ?>],
                    backgroundColor: 'rgba(54, 162, 235, 0.5)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                }]
            }
        });
    </script>
</body>
</html>
