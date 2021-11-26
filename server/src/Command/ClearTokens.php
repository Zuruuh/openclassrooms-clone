<?php

namespace App\Command;

use App\Repository\ResetUserPasswordTokenRepository as TokenRepo;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ClearTokens extends Command
{
    protected static $defaultName = 'app:clear-tokens';

    private EntityManagerInterface $em;
    private TokenRepo              $tokenRepo;


    public function __construct(
        TokenRepo              $tokenRepo,
        EntityManagerInterface $em,
    ) {
        $this->tokenRepo = $tokenRepo;
        $this->em        = $em;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setHelp('Deletes all invalid tokens in database.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $tokens = $this->tokenRepo->findAll();

        $removed = 0;
        foreach ($tokens as $token) {
            $issuedAt = $token->getIssuedAt()->getTimestamp();

            if ($issuedAt + (60 * 60 * 2) < time()) {
                $this->em->remove($token);
                $this->em->flush();

                ++$removed;
            }
        }

        $output->writeln('Total tokens: ' . sizeof($tokens));
        $output->writeln('Tokens removed: ' . $removed);

        return Command::SUCCESS;
    }
}
