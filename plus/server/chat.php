<?php
require '../../config.cli.php';
import('#net.driver.EventTCP');
import('#net.protocol.ChatServer');
import('#net.protocol.FlashPolicy');

if($argv[1]=='flash')
{
    echo "Flash Policy Server",NL;
    $protocol = new FlashPolicy;
    $server = new EventTCP('0.0.0.0',$protocol->default_port);
    $server->setProtocol($protocol);
    $server->run();
}
elseif($argv[1]=='chat')
{
    echo "Chat Server",NL;
    $protocol = new ChatServer;
    $server = new EventTCP('0.0.0.0',$protocol->default_port);
    $server->setProtocol($protocol);
    $server->run();
}
else
{

}
