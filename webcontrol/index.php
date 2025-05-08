<?php
if (isset($_GET['ip'])) {
    $address = $_GET['ip'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <meta charset="UTF-8">
    <title>FLASHFORGE Web UI</title>
    <style>
        body {
            margin: 0;
            font-family: 'Segoe UI', sans-serif;
            background-color: #1e1e1e;
            color: #f0f0f0;
        }

        header {
            background-color: #2c2c2d;
            padding: 10px 20px;
            display: flex;
            align-items: center;
            justify-content: space-between; /* Space between logo and IP address */
        }

        header img {
            height: 50px;
            margin-right: 20px;
        }

        header h1 {
            font-size: 1.8rem;
            color: #ffffff;
            margin: 0;
        }

        header .ip-address {
            font-size: 0.9rem;
            color: rgb(0 160 233); /* Blue color used for buttons */
            margin-left: 10px;
            font-weight: normal;
        }

        .container {
            display: flex;
            flex-wrap: wrap;  /* Allow the items to wrap onto the next line */
            justify-content: center;  /* Center them both */
            gap: 20px;  /* Add some gap between the cards */
            padding: 20px;
        }
        
        .card {
            background-color: #2c2c2d;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.2);
            max-width: 50%;
            min-width: 640px;
            position: relative;  /* Allow the button to be positioned absolutely inside */
        }

        iframe {
            display: block;
            margin: 0 auto;
            width: 640px;
            max-width: 640px;
            height: 480px;
            border: none;
            border-radius: 10px;
        }

        .refresh-button {
            background-color: rgb(0 160 233);
            color: white;
            border: none;
            padding: 12px 0;
            border-radius: 8px;
            font-size: 1.5rem;
            cursor: pointer;
            transition: background 0.2s ease;
            width: 50px;  /* Set fixed width for the button */
            height: 50px; /* Set fixed height for the button */
            position: absolute;
            top: 10px;
            right: 10px;
        }

        .refresh-button:hover {
            background-color: rgb(1 140 203);
        }

        footer {
            text-align: center;
            color: #888;
            padding: 10px;
            font-size: 0.8rem;
        }

        @media (max-width: 700px) {
            .card {
                width: 100%;  /* Full width on smaller screens */
            }
        }
        
    </style>
</head>
<body>

<header>
    <img src="images/FF_logo.jpg" alt="Logo">
    <h1>Web UI</h1>
    <?php if (isset($address)): ?>
        <span class="ip-address"><?php echo "Connected Printer: " . $address; ?></span>
    <?php endif; ?>
</header>

<div class="container">
    <div class="card">
        <h2>Live Stream</h2>
        <iframe id="liveStreamIframe" src="http://<?php echo $address; ?>:8080/?action=stream" height="480"></iframe>
        <button class="refresh-button" onclick="refreshIframe()">
            <i class="fas fa-sync"></i>
        </button>
    </div>

    <div class="card">
        <h2>Control Panel</h2>
        <iframe src="ff.php?ip=<?php echo $address; ?>" height="480"></iframe>
    </div>
</div>

<footer>
</footer>

<script>
    // Function to refresh the iframe
    function refreshIframe() {
        var iframe = document.getElementById('liveStreamIframe');
        iframe.src = iframe.src; // This will reload the iframe by re-assigning the same source
    }
</script>

</body>
</html>
