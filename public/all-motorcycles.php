<?php
include '../database/db.php';

$query = "SELECT Motocykle.Marka, Motocykle.Model, Motocykle.Cena, Motocykle.Zdjęcie, Lokalizacje.Miasto 
          FROM Motocykle 
          JOIN Lokalizacje ON Motocykle.IDlokalizacji = Lokalizacje.IDlokalizacji 
          WHERE Motocykle.Status = 'dostępny'";

$result = $conn->query($query);
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
            <button type="button" onclick="location.href='./public/login.php'">Login</button>
            <button type="button" onclick="location.href='./public/register.php'">Register at SwapMoto</button>
        </div>
    </div>
    <div class="content-container">
        <div class="content-header">
            <h1>Discover motorcycles nearby you.</h1>
        </div>
        <div class="content">
            <div class="filters-section">
                <div class="location-filter">
                    <label for="location">Pickup Location:</label>
                    <select name="location" id="location">
                        <option value="all">All</option>
                        <option value="warszawa">Warszawa</option>
                        <option value="krakow">Kraków</option>
                        <option value="lodz">Łódź</option>
                        <option value="gdansk">Gdańsk</option>
                    </select>
                </div>
                <div class="moto-type-filter">
                    <label for="type">Motorcycle Type:</label>
                    <select name="type" id="type">
                        <option value="all">All</option>
                        <option value="cruiser">Motorcycle</option>
                        <option value="sport">Motorcycle 125cc</option>
                        <option value="touring">Scooter</option>
                    </select>
                </div>
                <div class="license-filter">
                    <label for="license">License:</label>
                    <select name="license" id="license">
                        <option value="all">All</option>
                        <option value="a">A</option>
                        <option value="a2">A2</option>
                        <option value="a1">A1</option>
                        <option value="am">AM</option>
                    </select>
                </div>  
                <div class="brand-filter">
                    <label for="brand">Brand:</label>
                    <select name="brand" id="brand">
                        <option value="all">All</option>
                        <option value="ducati">Ducati</option>
                        <option value="suzuki">Suzuki</option>
                        <option value="honda">Honda</option>
                        <option value="triumph">Triumph</option>
                        <option value="yamaha">Yamaha</option>
                        <option value="bmw">BMW</option>
                        <option value="kawasaki">Kawasaki</option>
                        <option value="harley-davidson">Harley-Davidson</option>
                        <option value="indian">Indian</option>
                        <option value="moto-guzzi">Moto Guzzi</option>
                        <option value="ktm">KTM</option>
                        <option value="aprilia">Aprilia</option>
                        <option value="piaggio">Piaggio</option>
                        <option value="kymco">Kymco</option>
                        <option value="vespa">Vespa</option>
                        <option value="sym">SYM</option>
                        <option value="peugeot">Peugeot</option>
                    </select>
                </div>
                <div class="price-filter">
                    <div class="price-input-container">
                        <div class="price-input">
                            <div class="price-field">
                                <span>Minimum Price</span>
                                <input type="number" 
                                    class="min-input" 
                                    value="0">
                            </div>
                            <div class="price-field">
                                <span>Maximum Price</span>
                                <input type="number" 
                                    class="max-input" 
                                    value="500">
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
                            value="125" 
                            step="1">
                        <input type="range" 
                            class="max-range" 
                            min="0" 
                            max="500" 
                            value="425" 
                            step="1">
                    </div>
                </div>
            </div>
            <div class="moto-content-container">
                <div class="moto-container-header">
                    <p>30 motorcycles found</p>
                    <div class="sort-container">
                        <label for="sort">Sort by:</label>
                        <select name="sort" id="sort">
                            <option value="price">Price (lowest)</option>
                            <option value="priceDesc">Price (highest)</option>
                        </select>
                    </div>
                </div>
                <div class="moto-container">
                    <?php foreach ($motorcycle as $moto): ?>
                        <div class="moto-card">
                            <a href="#">
                                <img src="<?php echo '../uploads/bikes/draft/' . $moto['Zdjęcie']; ?>" alt="<?php echo $moto['Marka'] . ' ' . $moto['Model']; ?>">
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
    <div class="footer-container">
        &copy SwapMoto 2024
    </div>

    <script src="../src/priceSlider.js"></script>

</body>
</html>