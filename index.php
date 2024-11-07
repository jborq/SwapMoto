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
                <p>Rent a motorcycle by the hour, day, or week - choose the rental period that fits your needs.</p>
            </div>
            <div class="content-down">
                <p>From nimble scooters to powerful touring bikes - find the perfect motorcycle for your riding style.</p>
            </div>
        </div>

        <div class="content-middle">
            <h1>Discover nearby garages and rent your favorite motorcycle in just a few clicks!</h1>
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
                <p>SwapMoto is a user-friendly platform that connects you with garages in your city, making it easy to rent a motorcycle whenever you need one.</p>
            </div>
            <div class="content-down">
                <p>We aim to provide an effortless motorcycle rental experience, giving you quick access to a wide range of motorcycles in conveniently located garages across multiple cities.</p>
            </div>
        </div>
    </div>
    <div class="footer">
        SwapMoto &copy 2024
    </div>

    <script src="./src/slideshow.js"></script>
    
</body>
</html>