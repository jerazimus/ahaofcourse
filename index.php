<?php

require_once 'vendor/autoload.php';

session_start();


$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$client = new Google_Client();
$client->setClientId(getenv('GOOGLE_CLIENT_ID')); // Use environment variable for Client ID
$client->setClientSecret(getenv('GOOGLE_CLIENT_SECRET')); // Use environment variable for Client Secret
$client->setRedirectUri('https://ahaofcourse.com/callback.php'); // Use your actual domain or test environment
$client->addScope('email');
$client->addScope('profile');

$loginUrl = $client->createAuthUrl();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aha! Insights & Analytics</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Lily+Script+One&display=swap" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&family=Lily+Script+One&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            font-family: "Inter", sans-serif;
        }

        html {
            font-size: 16px;
        }

        body {
            background: #FFFBEE;
        }

        .section-cta {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: start;
            row-gap: 3rem;
            padding: 4rem;
            min-height: 600px;
            max-width: 50rem;
            min-width: 20rem;
        }

        .light-bulb{
            font-size: 4rem;
        }

        .logo {
            font-family: "Lily Script One", system-ui;
            font-size: 4rem;
            font-weight: 400;
            font-style: normal;
            background: linear-gradient(to right, #FF8400, #AA00FF);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .sub-text {
            font-size: 1.5rem;
            font-weight: 600;
            max-width: 75%;
        }

        .benefits {
            display: flex;
            flex-direction: column;
            row-gap: 10px;
            font-size: 1.1rem;
            line-height: 1.6;
        }

        .primary-button {
            height: 3rem;
            text-align: center;
            background: #00C20B;
            border-radius: 0.6rem;
            padding: 1rem;
            border: none;
            color: white;
            font-size: 1rem;
            font-weight: 700;
            line-height: 1.2;
            text-decoration: none;
        }

        .secondary-button {
            height: 3rem;
            text-align: center;
            background: #E0E3E1;
            border-radius: 0.6rem;
            padding: 1rem;
            border: none;
            color: black;
            font-size: 1rem;
            font-weight: 700;
            line-height: 1.2;
        }
    </style>
</head>
<body>
    <div class="section-cta">
        <div>
            <p class="light-bulb">💡</p>
            <h1 class="logo">Aha! Of Course</h1>
            <h3 class="sub-text">Key Insight & Progression Tracking for Your Courses. Understand how your students engage with your content — wherever you host your course.</h3>
        </div>
            
        <div class="benefits">
            <p><strong>🐾 Track Progression:</strong> Understand where students are thriving and where they get stuck.</p>
            <p><strong>📊 Get Customized Analytics:</strong> Tailored to your course, helping you improve your content.</p>
            <p><strong>🔌 Seamless Integration:</strong> Works anywhere JavaScript embeds are supported!</p>
        </div>
        
        <div>
            <a href="<?php echo htmlspecialchars($loginUrl); ?>" class="primary-button">Continue with Google</a>
            <button class="secondary-button">Pricing</button>
        </div>
    </div>
</body>
</html>
