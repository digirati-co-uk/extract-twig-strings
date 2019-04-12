<?php

use Digirati\ExtractTwigStrings\Command\ExtractCommand;
use Symfony\Component\Console\Application;
use Symfony\Component\Translation\Dumper\IcuResFileDumper;
use Symfony\Component\Translation\Dumper\MoFileDumper;
use Symfony\Component\Translation\Dumper\PoFileDumper;
use Symfony\Component\Translation\Dumper\XliffFileDumper;
use Symfony\Component\Translation\Writer\TranslationWriter;

$autoloadFiles = array(__DIR__ . '/vendor/autoload.php',
                       __DIR__ . '/../../autoload.php');

foreach ($autoloadFiles as $autoloadFile) {
    if (file_exists($autoloadFile)) {
        require_once $autoloadFile;
    }
}

$dumpers = [
    'po' => new PoFileDumper(),
    'mo' => new MoFileDumper(),
    'xliff' => new XliffFileDumper(),
    'res' => new IcuResFileDumper()
];

$writer = new TranslationWriter();
foreach ($dumpers as $format => $dumper) {
    $writer->addDumper($format, $dumper);
}

$app = new Application();
$app->add($command = new ExtractCommand($writer, array_keys($dumpers)));
$app->setDefaultCommand($command->getName(), true);
$app->run();
