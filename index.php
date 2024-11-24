<?php
include './database/db.php';

$query = "SELECT IDmotocykla, Motocykle.Marka, Motocykle.Model, Motocykle.Cena, Motocykle.Zdjęcie, Lokalizacje.Miasto 
          FROM Motocykle 
          JOIN Lokalizacje ON Motocykle.IDlokalizacji = Lokalizacje.IDlokalizacji 
          WHERE Motocykle.Status = 'dostępny' 
          LIMIT 15";

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
    <title>Rent a motorcycle at SwapMoto</title>
    <link rel="stylesheet" href="./style.css?v=<?php echo time(); ?>">
</head>
<body>
    <?php include './partials/navbar-logout.php'; ?>
    <div class="content-container">
        <div class="heroHeader">
            <h1>Discover nearby garages and rent your favorite motorcycle in just a few clicks!</h1>
            <p>Through SwapMoto you can rent motorcycles directly from dealers or private motorcycle owners in your area. From vintage cars to the newest BMW or Harley-Davidson models. With our motorcycle rentals you always have the same favorable rental conditions and you don't have to pay a deposit in advance. So check out our range of motorcycles for rent in and around your city!</p>
            <div class="achievement-container">
                <div class="achievement">
                    <div class="achievement-title">
                        <img src="./public/icons/bike_icon.png" alt="Bike Icon" />
                        1500+
                    </div>
                    <div class="achievement-description">
                        Motorcycles for rent in your neighbourhood
                    </div>
                </div>
                <div class="achievement">
                    <div class="achievement-title">
                        <img src="./public/icons/clock_icon.png" alt="Clock Icon" />
                        24/7
                    </div>
                    <div class="achievement-description">
                        Open and easy online renting
                    </div>
                </div>
                <div class="achievement">
                    <div class="achievement-title">
                        <img src="./public/icons/star_icon.png" alt="Bike Icon" />
                        Affordable
                    </div>
                    <div class="achievement-description">
                        Rental day of 24 hours and >100 km free per day
                    </div>
                </div>
                <div class="achievement">
                    <div class="achievement-title">
                        <img src="./public/icons/thumb_icon.png" alt="Bike Icon" />
                        8.7
                    </div>
                    <div class="achievement-description">
                        The score of other bikers
                    </div>
                </div>
            </div>
        </div>
        <div class="content">
            <div class="moto-container">
                <?php foreach ($motorcycle as $moto): ?>
                    <div class="moto-card">
                        <a href="./public/motorcycle-details.php?id=<?php echo $moto['IDmotocykla']; ?>">
                            <img src="<?php echo './uploads/bikes/' . $moto['Zdjęcie']; ?>" alt="<?php echo $moto['Marka'] . ' ' . $moto['Model']; ?>">
                            <h2><?php echo $moto['Marka'] . ' ' . $moto['Model']; ?></h2>
                            <p><?php echo $moto['Miasto']; ?></p>
                            <p><?php echo $moto['Cena']; ?> zł / day</p>
                        </a>
                    </div>
                <?php endforeach; ?>
            </div> 
            <button type="button" onclick="location.href='./public/all-motorcycles.php'">View all motorcycles</button>
        </div>
        <div class="testimonials-container">
            <h1>Experiences of our customers</h1>
            <p>Not convinced yet? Check out the reviews of SwapMoto.</p>
            <div class="testimonials-wrapper">
                <div class="testimonials">
                    <div class="testimonial">
                        <p class="stars">★★★★★</p>
                        <p>“I rented a motorcycle for a day and it was a great experience. The motorcycle was in perfect condition and the service was excellent. I will definitely rent a motorcycle again through SwapMoto.”</p>
                        <strong>Adam</strong>
                        <p>May 8, 2024</p>
                    </div>
                    <div class="testimonial">
                        <p class="stars">★★★★★</p>
                        <p>“Everything was clear, well organised and definitely worth repeating! In short, a very pleasant way to rent a motorbike.”</p>
                        <strong>Ola</strong>
                        <p>April 25, 2024</p>
                    </div>
                    <div class="testimonial">
                        <p class="stars">★★★★★</p>
                        <p>“Excellent service, even with a last-minute reservation. Definitely going to do it more often.”</p>
                        <strong>Maciek</strong>
                        <p>June 24, 2024</p>
                    </div>
                    <div class="testimonial">
                        <p class="stars">★★★★★</p>
                        <p>“Good service, well maintained motorbike, and quick process of both picking up and returning the motorbike.”</p>
                        <strong>Bob</strong>
                        <p>August 16, 2024</p>
                    </div>
                    <div class="testimonial">
                        <p class="stars">★★★★★</p>
                        <p>“The best scooter rental place in Warsaw with brand new scooters I highly recommend to everyone and thanks for the good quality scooters”</p>
                        <strong>Greg</strong>
                        <p>May 30, 2024</p>
                    </div>
                    <div class="testimonial">
                        <p class="stars">★★★★★</p>
                        <p>“Kind and professional. Flexible with my needs. The motorcycle worked perfectly. I will repeat with you in my next opportunity. Thank you.”</p>
                        <strong>Ruben</strong>
                        <p>August 7, 2024</p>
                    </div>
                    <div class="testimonial">
                        <p class="stars">★★★★★</p>
                        <p>“Great service, very friendly and helpful staff. The motorcycle was in perfect condition. I will definitely rent a motorcycle again through SwapMoto.”</p>
                        <strong>Marcin</strong>
                        <p>July 14, 2024</p>
                    </div>
                </div>
            </div>
            <button class="prev">&#10094;</button>
            <button class="next">&#10095;</button>
        </div>
    </div>
    <div class="footer-container">
        &copy SwapMoto 2024
    </div>
    <script type="module">
        import { startSlideshow } from './src/slideshow.js';
        import { scrollTestimonials } from './src/swiper.js';

        window.onload = startSlideshow;
        document.querySelector('.prev').addEventListener('click', () => scrollTestimonials(-1));
        document.querySelector('.next').addEventListener('click', () => scrollTestimonials(1));
    </script>
</body>
</html>