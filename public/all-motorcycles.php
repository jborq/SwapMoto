<?php
include '../database/db.php';

$location = isset($_GET['location']) ? $_GET['location'] : '';
$type = isset($_GET['type']) ? $_GET['type'] : '';
$license = isset($_GET['license']) ? $_GET['license'] : '';
$brand = isset($_GET['brand']) ? $_GET['brand'] : '';
$minPrice = isset($_GET['min-price']) ? $_GET['min-price'] : 0;
$maxPrice = isset($_GET['max-price']) ? $_GET['max-price'] : 500;
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'most-recomended';

$query = "SELECT IDmotocykla, Motocykle.Marka, Motocykle.Model, Motocykle.Cena, Motocykle.Zdjęcie, Lokalizacje.Miasto 
          FROM Motocykle 
          JOIN Lokalizacje ON Motocykle.IDlokalizacji = Lokalizacje.IDlokalizacji 
          WHERE Motocykle.Status = 'dostępny'";

$params = [];
$types = '';

if ($location && $location !== 'all') {
    $query .= " AND Lokalizacje.Miasto = ?";
    $params[] = $location;
    $types .= 's';
}

if ($type && $type !== 'all') {
    $query .= " AND Motocykle.IDkategorii = ?";
    $params[] = $type;
    $types .= 'i';
}

if ($license && $license !== 'all') {
    $query .= " AND Motocykle.Kategoria_prawa_jazdy = ?";
    $params[] = $license;
    $types .= 's';
}

if ($brand && $brand !== 'all') {
    $query .= " AND Motocykle.Marka = ?";
    $params[] = $brand;
    $types .= 's';
}

$query .= " AND Motocykle.Cena BETWEEN ? AND ?";
$params[] = $minPrice;
$params[] = $maxPrice;
$types .= 'ii';

switch ($sort) {
    case 'priceDesc':
        $query .= " ORDER BY Motocykle.Cena DESC";
        break;
    case 'price':
        $query .= " ORDER BY Motocykle.Cena ASC";
        break;
    case 'most-recommended':
    default:
        break;
}

$stmt = $conn->prepare($query);

if ($params) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();
$motorcycle = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $motorcycle[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SwapMoto - Motorcycles for rent.</title>
    <link rel="stylesheet" href="./css/all-motorcycles-style.css?v=<?php echo time(); ?>">
</head>

<body>
    <div class="header-container">
        <div class="logo">
            <a href="../index.php">
                <img src="./images/SwapMoto.png" alt="Logo" />
            </a>
        </div>
        <div class="header-button">
            <button type="button" onclick="location.href='../public/login.php'">Login</button>
            <button type="button" onclick="location.href='../public/register.php'">Register at SwapMoto</button>
        </div>
    </div>
    <form method="GET" action="all-motorcycles.php">
        <div class="content-container">
            <div class="content-header">
                <h1>Discover motorcycles nearby you.</h1>
            </div>
            <div class="content">
                <div class="filters-section">
                    <div class="location-filter">
                        <label for="location">Pickup location:</label>
                        <select name="location" id="location">
                            <option value="all" <?php echo $location === 'all' ? 'selected' : ''; ?>>All</option>
                            <option value="warszawa" <?php echo $location === 'warszawa' ? 'selected' : ''; ?>>Warszawa</option>
                            <option value="kraków" <?php echo $location === 'kraków' ? 'selected' : ''; ?>>Kraków</option>
                            <option value="łódź" <?php echo $location === 'łódź' ? 'selected' : ''; ?>>Łódź</option>
                            <option value="gdańsk" <?php echo $location === 'gdańsk' ? 'selected' : ''; ?>>Gdańsk</option>
                        </select>
                    </div>
                    <div class="moto-type-filter">
                        <label for="type">Motorcycle type:</label>
                        <select name="type" id="type">
                            <option value="all" <?php echo $type === 'all' ? 'selected' : ''; ?>>All</option>
                            <option value="1" <?php echo $type === '1' ? 'selected' : ''; ?>>Motorcycle</option>
                            <option value="2" <?php echo $type === '2' ? 'selected' : ''; ?>>Motorcycle 125cc</option>
                            <option value="3" <?php echo $type === '3' ? 'selected' : ''; ?>>Scooter</option>
                        </select>
                    </div>
                    <div class="license-filter">
                        <label for="license">License:</label>
                        <select name="license" id="license">
                            <option value="all" <?php echo $license === 'all' ? 'selected' : ''; ?>>All</option>
                            <option value="a" <?php echo $license === 'a' ? 'selected' : ''; ?>>A</option>
                            <option value="a2" <?php echo $license === 'a2' ? 'selected' : ''; ?>>A2</option>
                            <option value="a1" <?php echo $license === 'a1' ? 'selected' : ''; ?>>A1</option>
                            <option value="am" <?php echo $license === 'am' ? 'selected' : ''; ?>>AM</option>
                        </select>
                    </div>
                    <div class="brand-filter">
                        <label for="brand">Brand:</label>
                        <select name="brand" id="brand">
                            <option value="all" <?php echo $brand === 'all' ? 'selected' : ''; ?>>All</option>
                            <option value="ducati" <?php echo $brand === 'ducati' ? 'selected' : ''; ?>>Ducati</option>
                            <option value="suzuki" <?php echo $brand === 'suzuki' ? 'selected' : ''; ?>>Suzuki</option>
                            <option value="honda" <?php echo $brand === 'honda' ? 'selected' : ''; ?>>Honda</option>
                            <option value="triumph" <?php echo $brand === 'triumph' ? 'selected' : ''; ?>>Triumph</option>
                            <option value="yamaha" <?php echo $brand === 'yamaha' ? 'selected' : ''; ?>>Yamaha</option>
                            <option value="bmw" <?php echo $brand === 'bmw' ? 'selected' : ''; ?>>BMW</option>
                            <option value="kawasaki" <?php echo $brand === 'kawasaki' ? 'selected' : ''; ?>>Kawasaki</option>
                            <option value="harley-davidson" <?php echo $brand === 'harley-davidson' ? 'selected' : ''; ?>>Harley-Davidson</option>
                            <option value="indian" <?php echo $brand === 'indian' ? 'selected' : ''; ?>>Indian</option>
                            <option value="moto-guzzi" <?php echo $brand === 'moto-guzzi' ? 'selected' : ''; ?>>Moto Guzzi</option>
                            <option value="ktm" <?php echo $brand === 'ktm' ? 'selected' : ''; ?>>KTM</option>
                            <option value="aprilia" <?php echo $brand === 'aprilia' ? 'selected' : ''; ?>>Aprilia</option>
                            <option value="piaggio" <?php echo $brand === 'piaggio' ? 'selected' : ''; ?>>Piaggio</option>
                            <option value="kymco" <?php echo $brand === 'kymco' ? 'selected' : ''; ?>>Kymco</option>
                            <option value="vespa" <?php echo $brand === 'vespa' ? 'selected' : ''; ?>>Vespa</option>
                            <option value="sym" <?php echo $brand === 'sym' ? 'selected' : ''; ?>>SYM</option>
                            <option value="peugeot" <?php echo $brand === 'peugeot' ? 'selected' : ''; ?>>Peugeot</option>
                        </select>
                    </div>
                    <div class="price-filter">
                        <div class="price-input-container">
                            <div class="price-input">
                                <div class="price-field">
                                    <span>Minimum daily price</span>
                                    <input type="number"
                                        class="min-input"
                                        name="min-price"
                                        value="<?php echo htmlspecialchars($minPrice); ?>">
                                </div>
                                <div class="price-field">
                                    <span>Maximum daily price</span>
                                    <input type="number"
                                        class="max-input"
                                        name="max-price"
                                        value="<?php echo htmlspecialchars($maxPrice); ?>">
                                </div>
                            </div>
                            <div class="slider-container">
                                <div class="price-slider"></div>
                            </div>
                        </div>

                        <!-- Slider -->
                        <div class="range-input">
                            <input type="range"
                                class="min-range"
                                min="0"
                                max="500"
                                value="115"
                                step="1">
                            <input type="range"
                                class="max-range"
                                min="0"
                                max="500"
                                value="430"
                                step="1">
                        </div>
                    </div>
                    <div class="submit-button">
                        <button type="submit">Apply filters</button>
                    </div>
                </div>
                <div class="moto-content-container">
                    <div class="moto-container-header">
                        <p><?php echo count($motorcycle); ?> motorcycles found</p>
                        <div class="sort-container">
                            <label for="sort">Sort by:</label>
                            <select name="sort" id="sort">
                                <option value="most-recommended" <?php echo $sort === 'most-recommended' ? 'selected' : ''; ?>>Most recommended</option>
                                <option value="price" <?php echo $sort === 'price' ? 'selected' : ''; ?>>Price (lowest)</option>
                                <option value="priceDesc" <?php echo $sort === 'priceDesc' ? 'selected' : ''; ?>>Price (highest)</option>
                            </select>
                        </div>
                    </div>
                    <div class="moto-container">
                        <?php foreach ($motorcycle as $moto): ?>
                            <div class="moto-card">
                                <a href="../public/motorcycle-details.php?id=<?php echo $moto['IDmotocykla']; ?>">
                                    <img src="<?php echo '../uploads/bikes/' . $moto['Zdjęcie']; ?>" alt="<?php echo $moto['Marka'] . ' ' . $moto['Model']; ?>">
                                    <h2><?php echo $moto['Marka'] . ' ' . $moto['Model']; ?></h2>
                                    <p><?php echo $moto['Miasto']; ?></p>
                                    <p><?php echo $moto['Cena']; ?> PLN / day</p>
                                </a>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </form>
    <div class="footer-container">
        &copy SwapMoto 2024
    </div>

    <script src="../src/priceSlider.js"></script>
    <script>
        document.getElementById('sort').addEventListener('change', function() {
            this.form.submit();
        });
    </script>

</body>

</html>