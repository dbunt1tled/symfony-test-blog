<?php
/**
 * Created by PhpStorm.
 * User: sid
 * Date: 22.08.18
 * Time: 23:13
 */

namespace App\Command;

use FOS\OAuthServerBundle\Model\AccessTokenManagerInterface;
use FOS\OAuthServerBundle\Model\AuthCodeManagerInterface;
use FOS\OAuthServerBundle\Model\RefreshTokenManagerInterface;
use FOS\OAuthServerBundle\Model\TokenManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CleanCommand extends Command
{
    /**
     * @var AccessTokenManagerInterface
     */
    private $accessTokenManager;

    /**
     * @var RefreshTokenManagerInterface
     */
    private $refreshTokenManager;

    /**
     * @var AuthCodeManagerInterface
     */
    private $authCodeManager;

    /**
     * CleanCommand constructor.
     *
     * @param AccessTokenManagerInterface $accessTokenManager
     * @param RefreshTokenManagerInterface $refreshTokenManager
     * @param AuthCodeManagerInterface $authCodeManager
     */
    public function __construct(AccessTokenManagerInterface $accessTokenManager, RefreshTokenManagerInterface $refreshTokenManager, AuthCodeManagerInterface $authCodeManager)
    {
        parent::__construct();

        $this->accessTokenManager = $accessTokenManager;
        $this->refreshTokenManager = $refreshTokenManager;
        $this->authCodeManager = $authCodeManager;
    }

    protected function configure()
    {
        $this
            ->setName('oauth:clean')
            ->setDescription('Clean expired tokens')
            ->setHelp(<<<EOT
The <info>%command.name%</info> command will remove expired OAuth2 tokens.

  <info>php %command.full_name%</info>
EOT
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $services = [
            'Access token' => $this->accessTokenManager,
            'Refresh token' => $this->refreshTokenManager,
            'Auth code' => $this->authCodeManager,
        ];

        /** @var TokenManagerInterface $service */
        foreach ($services as $name => $service) {
            if ($service instanceof TokenManagerInterface || $service instanceof AuthCodeManagerInterface) {
                $result = $service->deleteExpired();
                $output->writeln(sprintf('Removed <info>%d</info> items from <comment>%s</comment> storage.', $result, $name));
            }
        }

        return 0;
    }
}