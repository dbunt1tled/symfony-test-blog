<?php
/**
 * Created by PhpStorm.
 * User: sid
 * Date: 22.08.18
 * Time: 22:53
 */

namespace App\Command;

use FOS\OAuthServerBundle\Model\ClientManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class CreateClientCommand extends Command
{
    /**
     * @var ClientManagerInterface
     */
    private $clientManager;

    /**
     * ClientCommand constructor.
     *
     * @param ClientManagerInterface $clientManager
     */
    public function __construct(ClientManagerInterface $clientManager)
    {
        parent::__construct();

        $this->clientManager = $clientManager;
    }

    protected function configure()
    {
        $this
            ->setName('oauth:create-client')
            ->setDescription('Creates a new OAuth2 client')
            ->addOption(
                'redirect-uri',
                null,
                InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY,
                'Sets redirect uri for client. Use this option multiple times to set multiple redirect URIs.',
                null
            )
            ->addOption(
                'grant-type',
                null,
                InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY,
                'Sets allowed grant type for client. Use this option multiple times to set multiple grant types..',
                null
            )
            ->setHelp(<<<EOT
The <info>%command.name%</info> command creates a new client.

<info>php %command.full_name% [--redirect-uri=...] [--grant-type=...]</info>

EOT
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        $io->title('Client Credentials');

        // Create a new client
        $client = $this->clientManager->createClient();
        $client->setRedirectUris($input->getOption('redirect-uri'));
        $client->setAllowedGrantTypes($input->getOption('grant-type'));

        // Save the client
        $this->clientManager->updateClient($client);

        // Give the credentials back to the user
        $headers = ['Client ID', 'Client Secret'];
        $rows = [
            [$client->getPublicId(), $client->getSecret()],
        ];

        $io->table($headers, $rows);

        return 0;
    }
}