<?php
namespace App\Command;
use App\WebSocket\NotificationServer;
use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
class WebSocketServerCommand extends Command
{
    protected static $defaultName = 'app:websocket-server';

    protected function configure()
    {
        $this
            ->setDescription('Start the WebSocket server');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $server = IoServer::factory(
            new HttpServer(
                new WsServer(
                    new NotificationServer()
                )
            ),
            8080
        );

        $output->writeln('WebSocket server started on port 8080');
        $server->run();

        return Command::SUCCESS;
    }
}