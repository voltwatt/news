<?php declare(strict_types=1);

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

#[AsCommand(
    name: 'app:remove-files',
    description: 'Remove all uploaded files',
)]
class RemoveUploadedFilesCommand extends Command
{
    public function __construct(
        #[Autowire('%kernel.project_dir%')] private readonly string $projectDir
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $dirFiles = [
            'articles',
        ];

        $uploadDir = $this->projectDir.'/public/uploads/';

        foreach ($dirFiles as $dir) {
            $files = glob($uploadDir.$dir.'/*');
            if ($files !== false) {
                foreach ($files as $file) {
                    if (is_file($file)) {
                        unlink($file);
                    }
                }
            }
        }

        $output->writeln('All files removed.');

        return Command::SUCCESS;
    }
}
