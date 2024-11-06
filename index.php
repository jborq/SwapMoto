<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome at SwapMoto</title>
    <link rel="stylesheet" href="./style.css?v=<?php echo time(); ?>">
</head>
<body>

    <div class="content">
        <div class="content-left">
            <div class="logo">
                <img src=".\public\images\SwapMoto.png" alt="Logo" />
            </div>
            <div class="content-up">
                <p>Garages in 4 Cities</p>
            </div>
            <div class="content-down">
                <p>6 Types of Motorcycles</p>
            </div>
        </div>

        <div class="content-middle">
            <h1>Find nearby garage in your city</h1>
            <div class="slideshow">
                <img src=".\public\images\mt-07.png" alt="Yamaha MT-07" class="slide active">
                <img src=".\public\images\triumph.png" alt="Triumph" class="slide">
                <img src=".\public\images\harley.png" alt="Harley" class="slide">
                <img src=".\public\images\fz6.png" alt="Yamaha FZ6" class="slide">
            </div>
            <div class="button">
                <button type="button" onclick="location.href='./public/login.php'">Get Started</button>
            </div>
        </div>

        <div class="content-right">
            <div class="button">
                <button type="button" onclick="location.href='./public/register.php'">Register</button>
            </div>
            <div class="content-up">
                <p>SwapMoto is a platform that allows you to rent motorcycles from garages in your city.</p>
            </div>
            <div class="content-down">
                <p>Our mission is to provide a platform that allows you to rent motorcycles from garages in your city.</p>
            </div>
        </div>
    </div>
    <div class="footer">
        SwapMoto &copy 2024
    </div>

    <script src="./src/slideshow.js"></script>
    
</body>
</html>