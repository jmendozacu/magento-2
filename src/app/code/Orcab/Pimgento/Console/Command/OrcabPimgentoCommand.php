<?php

namespace Orcab\Pimgento\Console\Command;

use \Symfony\Component\Console\Command\Command;
use \Symfony\Component\Console\Input\InputInterface;
use \Symfony\Component\Console\Output\OutputInterface;
use \Symfony\Component\Console\Input\InputOption;
use \Pimgento\Import\Model\Import as ImportModel;
use \Exception;

class OrcabPimgentoCommand extends Command
{
    const IMPORT_CODE    = 'code';

    const IMPORT_PREFIX  = 'prefix';

    const IMPORT_ARCHIVE = 'archive';

    /**
     * @var \Pimgento\Import\Model\Import
     */
    protected $_import;

    /**
     * PimgentoImportCommand constructor.
     *
     * @param \Pimgento\Import\Model\Import $import
     * @param null $name
     */
    public function __construct(ImportModel $import, $name = null)
    {
        parent::__construct($name);
        $this->_import = $import;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('orcab:import')
            ->setDescription('Import PIM files to Magento')
            ->addOption(self::IMPORT_CODE, null, InputOption::VALUE_REQUIRED)
            ->addOption(self::IMPORT_PREFIX, null, InputOption::VALUE_REQUIRED)
            ->addOption(self::IMPORT_ARCHIVE, null, InputOption::VALUE_OPTIONAL);
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $code    = $input->getOption(self::IMPORT_CODE);
        $prefix  = $input->getOption(self::IMPORT_PREFIX);
        $archive = $input->getOption(self::IMPORT_ARCHIVE);

        if (!$code) {
            $this->_usage($output);
        } else {
            $this->_import($code, $prefix, $archive, $output);
        }
    }

    /**
     * Run import
     *
     * @param string $code
     * @param string $prefix
     * @param string $archive
     * @param OutputInterface $output
     */
    protected function _import($code, $prefix, $archive, OutputInterface $output)
    {
        try {
            $import = $this->_import->load($code);

            if ($handle = opendir($import->getUploadDir())) {
                while ($file = readdir($handle)) {
                    if ($file != '.' && $file != '..' && preg_match("/^{$prefix}/", $file)) {
                        $files[] = $file;
                    }
                }
                closedir($handle);
            }

            if (isset($files)) {
                foreach ($files as $file) {
                    $import->setFile($file)->setStep(0);

                    if ($archive) {
                        // Archive file
                        $output->writeln('Archive file: '.$file);
                        copy($import->getUploadDir().'/'.$file, $import->getUploadDir().'/archive/'.$file);
                    }
                    while ($import->canExecute()) {
                        $import->execute();

                        $output->writeln($import->getComment());
                        $output->writeln($import->getMessage());

                        if (!$import->getContinue()) {
                            break;
                        }

                        $import->next();
                    }

                    if ($archive) {
                        // Remove file
                        $output->writeln('Remove file: '.$file);
                        unlink($import->getUploadDir().'/'.$file);
                    }
                }
            } else {
                $output->writeln('No matching prefix');
            }
        } catch (Exception $e) {
            $output->writeln($e->getMessage());
        }
    }

    /**
     * Print command usage
     *
     * @param OutputInterface $output
     */
    protected function _usage(OutputInterface $output)
    {
        $imports = $this->_import->getCollection();

        /* Options */
        $output->writeln('<comment>' . __('Options:') . '</comment>');
        $output->writeln(' <info>--code</info>');
        $output->writeln(' <info>--prefix</info>');
        $output->writeln(' <info>--archive</info>');
        $output->writeln('');

        /* Codes */
        $output->writeln('<comment>' . __('Available codes:') . '</comment>');
        foreach ($imports as $import) {
            $output->writeln(' <info>' . $import->getCode() . '</info>');
        }
        $output->writeln('');

        /* Example */
        $import = $imports->getFirstItem();
        if ($import->getCode()) {
            $output->writeln('<comment>' . __('Example:') . '</comment>');
            $output->writeln(
                ' <info>pimgento:import --code=' . $import->getCode() . ' --prefix=' . $import->getCode() . ' --archive 1</info>'
            );
        }
    }

}
