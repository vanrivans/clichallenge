<?php

namespace Vanrivans\Clichallenge\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class Play extends Command
{
    /**
     * The name of the command (the part after "bin/demo").
     *
     * @var string
     */
    protected static $defaultName = 'play';

    /**
     * The command description shown when running "php bin/demo list".
     *
     * @var string
     */
    protected static $defaultDescription = 'Play the game!';

    /**
     * Execute the command
     *
     * @param  InputInterface  $input
     * @param  OutputInterface $output
     * @return int 0 if everything went fine, or an exit code.
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        
        $io = new SymfonyStyle($input, $output);
    
        $input = (string) $io->ask(sprintf('Input string?'));
        
        // Challange1 => convert string to upper case
        $challange1 = $this->convToUpper($input);
        $io->success(sprintf('Your string: %s', $challange1));

        // Challange2 => convert string to alternate upper and lower case
        $challange2 = $this->convToAlternateUpperLower($input);
        $io->success(sprintf('Your string: %s', $challange2));

        // Export to CSV
        $filename = "data_export_" . date("Y-m-d") . "_" . date("His") . ".csv";
        $this->CSVHeaders($filename);
        $io->success(sprintf('%s',  $this->CSVExport($this->convToLower($input), $filename)));

        return Command::SUCCESS;
    }

    /**
     * function to converts all string to upper case
     * @var string
     */
    protected function convToUpper($string = ''): string
    {
        if ($string) {
            $result = strtoupper($string);
            return $result;
        }
    }

    /**
     * function to converts all string to lower case
     * @var string
     */
    protected function convToLower($string = ''): string
    {
        if ($string) {
            $result = strtolower($string);
            return $result;
        }
    }

    /**
     * function to converts the string to alternate upper and lower case
     * @var string
     */
    protected function convToAlternateUpperLower($string = ''): string
    {
        if ($string) {
            $result = '';

            // Loop 
            for ($i = 0; $i < strlen($string); $i++) {
                // Check if string position is even or odd
                if ($i % 2 == 0) {
                    $result .= $string[$i];
                } else {
                    $result .= $this->convToUpper($string[$i]);
                }
            }
            return $result;
        }
    }

    /**
     * function csv headers configuration
     */
    protected function CSVHeaders($filename) {
        // disable caching
        $now = gmdate("D, d M Y H:i:s");
        header("Cache-Control: max-age=0, no-cache, must-revalidate, proxy-revalidate");
        header("Last-Modified: {$now} GMT");
    
        // force download  
        header("Content-Type: application/force-download");
        header("Content-Type: application/octet-stream");
        header("Content-Type: application/download");
    
        // disposition / encoding on response body
        header("Content-Disposition: attachment;filename={$filename}");
        header("Content-Transfer-Encoding: binary");
    }
    
    /**
     * function export csv
     */
    protected function CSVExport($string, $filename): string
    {
        ob_start();
        
        $df = fopen("downloads/" . $filename, 'w');

        $exp = str_split($string);

        $cols = [];
        for ($a = 0; $a < count($exp); $a++) {
            $cols[] = $a;
        }
        fputcsv($df, $exp, ',');

        fclose($df);
        
        ob_get_clean();

        return 'CSV Created!';
    }

}
