<?php
require 'vendor/autoload.php'; // Include the Ratchet library

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;

/**
 * Summary of Chat
 */
class Chat implements MessageComponentInterface
{
    /**
     * Summary of clients
     * @var 
     */
    protected $clients;

    /**
     * Summary of __construct
     */
    public function __construct()
    {
        $this->clients = new \SplObjectStorage;
    }

    /**
     * Summary of onOpen
     * @param Ratchet\ConnectionInterface $conn
     * @return void
     */
    public function onOpen(ConnectionInterface $conn)
    {
        // Store the new connection when it is opened
        $this->clients->attach($conn);
        echo "New connection! ({$conn->resourceId})\n";
    }

    /**
     * Summary of onMessage
     * @param Ratchet\ConnectionInterface $from
     * @param mixed $msg
     * @return void
     */
    public function onMessage(ConnectionInterface $from, $msg)
    {
        // Broadcast the message to all connected clients
        foreach ($this->clients as $client) {
            $client->send($msg);
        }
    }

    /**
     * Summary of onClose
     * @param Ratchet\ConnectionInterface $conn
     * @return void
     */
    public function onClose(ConnectionInterface $conn)
    {
        // Remove the connection when it is closed
        $this->clients->detach($conn);
        echo "Connection {$conn->resourceId} has disconnected\n";
    }

    /**
     * Summary of onError
     * @param Ratchet\ConnectionInterface $conn
     * @param Exception $e
     * @return void
     */
    public function onError(ConnectionInterface $conn, \Exception $e)
    {
        // Handle errors
        echo "An error has occurred: {$e->getMessage()}\n";
        $conn->close();
    }
}

// Start the WebSocket server
$server = new \Ratchet\App('localhost', 8080);
$server->route('/chat', new Chat, ['*']);
$server->run();
