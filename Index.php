<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>92 Doner Kings</title>
    <style>
        /* General Reset */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        /* Body Styling */
        body {
            font-family: Arial, sans-serif;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
        /* Header Styling */
        header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 10px 20px;
            background-color: #d10a1f;
        }
        nav ul {
            list-style: none;
            display: flex;
            gap: 20px;
        }
        nav ul li a {
            color: white;
            text-decoration: none;
            font-weight: bold;
            font-size: 1em;
        }
        /* Hero Section */
        .hero {
            position: relative;
            background-image: url('images/hero-bg.jpg'); /* Adjust path to your background image */
            background-size: cover;
            background-position: center;
            height: 80vh;
            display: flex;
            justify-content: center;
            align-items: center;
            text-align: center;
        }
        .hero-text {
            color: white;
            text-shadow: -1px -1px 0 #000, 1px -1px 0 #000, -1px 1px 0 #000, 1px 1px 0 #000;
            font-weight: bold;
            font-size: 2em;
        }
        .hero h1 {
            font-size: 3em;
            margin-bottom: 10px;
        }
        .hero p {
            font-size: 1.5em;
            margin-bottom: 20px;
        }
        .cta-button {
            background-color: #ff0000;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            font-weight: bold;
            border-radius: 5px;
            transition: background-color 0.3s;
        }
        .cta-button:hover {
            background-color: #cc0000;
            cursor: pointer;
        }
        footer {
            background-color: #d10a1f;
            color: white;
            padding: 10px 20px;
            text-align: center;
            margin-top: auto;
        }
    </style>
    <!-- Add this script tag -->
    <script src="https://translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>
    <script>
        function googleTranslateElementInit() {
            new google.translate.TranslateElement({
                pageLanguage: 'en',
                includedLanguages: 'ar,zh,cs,da,nl,en,fi,fr,de,el,hi,hu,id,it,ja,ko,no,pl,pt,ru,es,sv,th,tr,vi',
                layout: google.translate.TranslateElement.InlineLayout.SIMPLE
            }, 'google_translate_element');
        }
    </script>
</head>
<body>
    <!-- Header with Navigation -->
    <header>
        <nav>
            <ul>
                <li><a href="sign-in.php" data-translate>Sign In</a></li>
            </ul>
        </nav>
    </header>
    <!-- Hero Section -->
    <section class="hero">
        <div class="hero-text">
            <h1 data-translate>92 Doner Kings</h1>
            <p data-translate>Digital Interface</p>
            <a href="sign-in.php" class="cta-button" data-translate>Sign In</a>
        </div>
    </section>
    <footer>
        <div id="google_translate_element"></div>
        <button onclick="googleTranslateElementInit()">Translate</button>
    </footer>
</body>
</html>
