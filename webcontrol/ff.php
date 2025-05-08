<?php
if (isset($_GET['ip'])) {
    $address = $_GET['ip'];
}
$port = 8899;
$page = $_SERVER['PHP_SELF'];
$sec = "5";

// G-code commands
$rcm = '~M601 S1\r\n';
$rim = '~M115\r\n';
$rtm = '~M105\r\n';
$rhm = '~G28\r\n';
$progres = '~M27\r\n';
$status = '~M119\r\n';
$l_on = '~M146 r255 g255 b255\r\n';
$cal = '~M650\r\n';
$pause = '~M25\r\n';
$resume = '~M24\r\n';
$cancel = '~M26\r\n';
$home = '~G28\r\n';

$buf = '';

if (($socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP)) and socket_connect($socket, $address, $port)) {
    $text = "Connection successful on IP $address, port $port";
    socket_send($socket, utf8_encode($rcm), strlen($rcm), 0);
    socket_recv($socket, $bufi, 1024, 0);

    if (isset($_POST['PAUSE']) && $_POST['PAUSE'] == 'ON') {
        socket_send($socket, utf8_encode($pause), strlen($pause), 0);
        socket_recv($socket, $buf, 1024, 0);
    }
    if (isset($_POST['RESUME']) && $_POST['RESUME'] == 'ON') {
        socket_send($socket, utf8_encode($resume), strlen($resume), 0);
        socket_recv($socket, $buf, 1024, 0);
    }
    if (isset($_POST['STOP']) && $_POST['STOP'] == 'ON') {
        socket_send($socket, utf8_encode($cancel), strlen($cancel), 0);
        socket_recv($socket, $buf, 1024, 0);
    }
    if (isset($_POST['HOME']) && $_POST['HOME'] == 'ON') {
        socket_send($socket, utf8_encode($home), strlen($home), 0);
        socket_recv($socket, $buf, 1024, 0);
    }
    if (isset($_POST['LED']) && $_POST['LED'] == 'SWITCH') {
        socket_send($socket, utf8_encode($l_on), strlen($l_on), 0);
        socket_recv($socket, $buf, 1024, 0);
    }

    socket_send($socket, utf8_encode($rtm), strlen($rtm), 0);
    socket_recv($socket, $buft, 1024, 0);

    socket_send($socket, utf8_encode($progres), strlen($progres), 0);
    while (!socket_recv($socket, $bufp, 1024, 0));

    socket_send($socket, utf8_encode($status), strlen($status), 0);
    while (!socket_recv($socket, $bufs, 1024, 0));

    socket_close($socket);

    // Parse temperature info
    $buft = explode('T0:', $buft);
    $buft = explode('T1:', $buft[1]);
    $temp = explode('/', $buft[0]);
    $temp_he = $temp[0];
    $temp_hes = $temp[1];
    $buft = explode('B:', $buft[1]);
    $buft = explode('ok', $buft[1]);
    $temp = explode('/', $buft[0]);
    $temp_bed = $temp[0];
    $temp_beds = $temp[1];

    // Parse progress
    $bufp = explode('byte', $bufp);
    $bufpp = explode('/', $bufp[1]);
    $hotovo = intval($bufpp[0]);
    $layer = explode('Layer:', $bufp[1]);
    $layer = explode('ok', $layer[1]);
    $layer = trim($layer[0]);

    // Parse file
    $file = explode('CurrentFile:', $bufs);
    $files = explode('ok', $file[1]);
    $file = trim($files[0]);

    // Parse status
    $stav = explode('MoveMode:', $bufs);
    $stavs = explode('Status: S:', $stav[1]);
    $stav = trim($stavs[0]);
} else {
    $text = "Unable to connect<pre>" . socket_strerror(socket_last_error()) . "</pre>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="refresh" content="<?php echo $sec; ?>">
    <title>Printer Control</title>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@700&display=swap" rel="stylesheet">
    <!-- Include Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            margin: 0;
            font-family: 'Open Sans', sans-serif;
            background-color: #2d2d2d;
            color: #ffffff;
        }

        .status-card {
            padding: 20px;
            background: #363639;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.4);
            max-width: 900px;
            margin: 20px auto;
        }

        .row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 6px 0;
            border-bottom: 1px solid #444;
        }

        .label {
            color: #aaa;
            margin-right: 10px;
        }

        form {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            gap: 10px;
            margin-top: 10px;
        }

        button {
            flex: 1 1 18%;
            background-color: rgb(0 160 233);
            color: white;
            border: none;
            padding: 12px 0;
            border-radius: 8px;
            font-size: 1.2rem;
            font-family: 'Open Sans', sans-serif;
            font-weight: bold;
            cursor: pointer;
            text-align: center;
            transition: background 0.2s ease;
        }

        button:hover {
            background-color: rgb(1 140 203);
        }

        .progress-bar-container {
            width: 100%;
            background-color: #555;
            border-radius: 8px;
            overflow: hidden;
            height: 24px;
            flex-grow: 1;
            display: flex;
            align-items: center;
        }

        .progress-bar {
            height: 100%;
            background-color: rgb(0 160 233);
            color: white;
            text-align: center;
            white-space: nowrap;
            line-height: 24px;
            font-weight: bold;
            font-size: 0.9rem;
            padding: 0 8px;
        }

        @media (max-width: 600px) {
            form {
                flex-direction: column;
            }

            button {
                flex: 1 1 100%;
            }
        }
    </style>
</head>
<body>
<div class="status-card">
    <h2>Printer Status</h2>
    <div class="row"><span class="label">File:</span><span><?php echo $file; ?></span></div>
    <div class="row"><span class="label">State:</span><span><?php echo $stav; ?></span></div>

    <div class="row"><span class="label">HOTEND:</span><span><?php echo "$temp_he °C / $temp_hes °C"; ?></span></div>
    <div class="row"><span class="label">BED:</span><span><?php echo "$temp_bed °C / $temp_beds °C"; ?></span></div>
    <div class="row"><span class="label">Layer:</span><span><?php echo $layer; ?></span></div>

    <div class="row">
        <span class="label">Progress:</span>
        <div class="progress-bar-container">
            <div class="progress-bar" style="width: <?php echo $hotovo; ?>%;">
                <?php echo $hotovo; ?>%
            </div>
        </div>
    </div>

    <form action="ff.php?ip=<?php echo $address; ?>" method="post">
        <button type="submit" name="PAUSE" value="ON"><i class="fas fa-pause"></i></button>
        <button type="submit" name="RESUME" value="ON"><i class="fas fa-play"></i></button>
        <button type="submit" name="STOP" value="ON"><i class="fas fa-stop"></i></button>
        <button type="submit" name="HOME" value="ON"><i class="fas fa-home"></i></button>
        <button type="submit" name="LED" value="SWITCH"><i class="fas fa-lightbulb"></i></button>
    </form>
</div>
</body>
</html>
