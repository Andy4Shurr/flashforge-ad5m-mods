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
        }

        header img {
            height: 50px;
            margin-right: 20px;
        }

        header h1 {
            font-size: 1.8rem;
            color: #ffffff;
        }

        .container {
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 20px;
            gap: 20px;
        }

        .card {
            background-color: #2c2c2d;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.2);
            width: 680px;
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

        footer {
            text-align: center;
            color: #888;
            padding: 10px;
            font-size: 0.8rem;
        }

        @media (max-width: 700px) {
            .card {
                width: 95%;
            }
        }
    </style>
</head>
<body>

<header>
    <img src="images/FF_logo.jpg" alt="Logo">
    <h1>Web UI</h1>
</header>

<div class="container">
<div class="card">
    <h2 style="display: flex; justify-content: space-between; align-items: center;">
        <span>Live Stream</span>
        <button onclick="document.getElementById('streamFrame').src += ''" style="
            background-color: rgb(0 160 233);
            color: white;
            border: none;
            border-radius: 6px;
            padding: 6px 12px;
            font-weight: bold;
            font-family: 'Segoe UI', sans-serif;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
        ">
            <i class="fas fa-sync-alt"></i>
        </button>
    </h2>
    <iframe id="streamFrame" src="http://<?php echo $address; ?>:8080/?action=stream" height="480"></iframe>
</div>


    <div class="card">
        <h2>Status & Control</h2>
        <iframe src="ff.php?ip=<?php echo $address; ?>" height="480"></iframe>
    </div>
</div>

<footer>
</footer>

</body>
</html>
