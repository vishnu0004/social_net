<?php
error_reporting(E_ALL);
set_time_limit(0);
ob_implicit_flush();

$host = '127.0.0.1';
$port = 8080;

$sock = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
socket_set_option($sock, SOL_SOCKET, SO_REUSEADDR, 1);

if (socket_bind($sock, $host, $port) === false) {
    die("Could not bind to socket: " . socket_strerror(socket_last_error()) . "\n");
}

socket_listen($sock);
echo "WebSocket Server started at ws://{$host}:{$port}\n";

$clients = [];

while (true) {
    $read = $clients;
    $read[] = $sock;
    $write = $except = null;

    if (socket_select($read, $write, $except, null) < 1) {
        continue;
    }

    if (in_array($sock, $read)) {
        $newClient = socket_accept($sock);
        $clients[] = $newClient;

        $request = socket_read($newClient, 1024);
        if (preg_match('#Sec-WebSocket-Key: (.*)\r\n#', $request, $matches)) {
            $key = base64_encode(pack('H*', sha1($matches[1] . '258EAFA5-E914-47DA-95CA-C5AB0DC85B11')));
            $headers = "HTTP/1.1 101 Switching Protocols\r\n";
            $headers .= "Upgrade: websocket\r\n";
            $headers .= "Connection: Upgrade\r\n";
            $headers .= "Sec-WebSocket-Accept: $key\r\n\r\n";
            socket_write($newClient, $headers, strlen($headers));
        }

        echo "New connection established.\n";
    }

    foreach ($clients as $key => $client) {
        if (in_array($client, $read)) {
            $data = @socket_read($client, 1024, PHP_BINARY_READ);

            if ($data === false || strlen($data) === 0) {
                unset($clients[$key]);
                socket_close($client);
                echo "Client disconnected.\n";
                continue;
            }

            $decodedData = decodeMessage($data);
            echo "Received message: $decodedData\n";

            $response = encodeMessage($decodedData);
            foreach ($clients as $sendClient) {
                @socket_write($sendClient, $response, strlen($response));
            }
        }
    }
}

socket_close($sock);

function encodeMessage($text)
{
    $b1 = 0x81; // FIN + text frame
    $length = strlen($text);

    if ($length <= 125) {
        return pack('CC', $b1, $length) . $text;
    } elseif ($length > 125 && $length < 65536) {
        return pack('CCn', $b1, 126, $length) . $text;
    } else {
        return pack('CCNN', $b1, 127, 0, $length) . $text;
    }
}

function decodeMessage($data)
{
    $length = ord($data[1]) & 127;
    if ($length == 126) {
        $masks = substr($data, 4, 4);
        $message = substr($data, 8);
    } elseif ($length == 127) {
        $masks = substr($data, 10, 4);
        $message = substr($data, 14);
    } else {
        $masks = substr($data, 2, 4);
        $message = substr($data, 6);
    }

    $decoded = '';
    for ($i = 0; $i < strlen($message); $i++) {
        $decoded .= $message[$i] ^ $masks[$i % 4];
    }

    return $decoded;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat Application</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        #chat { border: 1px solid #ccc; height: 300px; overflow-y: scroll; padding: 10px; margin-bottom: 10px; }
        #message { width: 80%; padding: 10px; }
        #send { padding: 10px 20px; }
    </style>
</head>
<body>
    <h1>WebSocket Chat</h1>
    <div id="chat"></div>
    <input type="text" id="message" placeholder="Type a message">
    <button id="send">Send</button>

    <script>
        const chat = document.getElementById('chat');
        const messageInput = document.getElementById('message');
        const sendButton = document.getElementById('send');

        const ws = new WebSocket('ws://127.0.0.1:8080');

        ws.onopen = () => {
            console.log('Connected to WebSocket server');
        };

        ws.onmessage = (event) => {
            const message = document.createElement('div');
            message.textContent = event.data;
            chat.appendChild(message);
            chat.scrollTop = chat.scrollHeight;
        };

        sendButton.addEventListener('click', () => {
            const message = messageInput.value;
            if (message.trim() !== '') {
                ws.send(message);
                messageInput.value = '';
            }
        });
    </script>
</body>
</html>
