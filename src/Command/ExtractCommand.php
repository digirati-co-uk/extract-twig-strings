<?php


namespace Digirati\ExtractTwigStrings\Command;

use Digirati\ExtractTwigStrings\MessageExtractor;
use Digirati\ExtractTwigStrings\Utils\TwigUtils;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\RuntimeException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Translation\MessageCatalogue;
use Symfony\Component\Translation\Writer\TranslationWriter;
use Twig\Environment;
use Twig\Lexer;
use Twig\Loader\FilesystemLoader;
use Twig\Node\Node;
use Twig\Parser;
use Twig\Source;
use Twig\TwigFilter;
use Twig\TwigFunction;

class ExtractCommand extends Command
{
    /**
     * @var TranslationWriter
     */
    private $writer;

    /**
     * @var array
     */
    private $outputFormats;

    public function __construct(TranslationWriter $writer, array $outputFormats)
    {
        $this->writer = $writer;
        $this->outputFormats = $outputFormats;

        parent::__construct();
    }

    public function configure()
    {
        $validFormatString = implode(', ', $this->outputFormats);

        $this
            ->setName('twig-extract')
            ->setDescription('Extracts `translate` calls from Twig templates')
            ->addOption('domain',
                '-d',
                InputOption::VALUE_REQUIRED,
                'The default domain to save translations under',
                'messages'
            )
            ->addOption(
                'output',
                '-o',
                InputOption::VALUE_REQUIRED,
                'Path to the directory that messages will be stored in',
                getcwd()
            )
            ->addOption(
                'format',
                '-x',
                InputOption::VALUE_REQUIRED,
                "The format to serialize messages to.  Defaults to: {$this->outputFormats[0]}.  Must be one of: $validFormatString",
                $this->outputFormats[0]
            )
            ->addArgument('paths', InputArgument::IS_ARRAY, 'Paths to the Twig templates to analyze');
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $format = $input->getOption('format');
        $outputPath = $input->getOption('output');
        $files = $input->getArgument('paths');

        if (empty($files)) {
            throw new RuntimeException("No input files given");
        }

        $extractor = new MessageExtractor();
        $extract = function (Node $node, callable $extractChildCallback) use ($extractor) {
            $message = $extractor->apply($node);

            if ($message !== null) {
                yield $message;
            } else {
                foreach ($node as $child) {
                    yield from $extractChildCallback($child, $extractChildCallback);
                }
            }
        };

        $catalogue = new MessageCatalogue('en_US');

        $loader = new FilesystemLoader();
        $env = new Environment($loader);
        $env->registerUndefinedFilterCallback(TwigUtils::undefinedSymbolHandler(TwigFilter::class));
        $env->registerUndefinedFunctionCallback(TwigUtils::undefinedSymbolHandler(TwigFunction::class));
        $lexer = new Lexer($env);
        $parser = new Parser($env);

        $counter = 0;

        foreach ($files as $file) {
            $source = new Source(file_get_contents($file), basename($file), realpath($file));
            $tokens = $lexer->tokenize($source);
            $rootNode = $parser->parse($tokens);

            foreach ($extract($rootNode, $extract) as $message) {
                $counter++;
                $messageKey = sprintf("%s:%d", $message->getSource(), $message->getSourceLocation());
                $messageValue = $message->getValue();

                $catalogue->set($messageKey, $messageValue);
            }

            $this->writer->writeTranslations(
                $catalogue,
                $format,
                [
                    'path' => $outputPath
                ]
            );
        }

        $output->writeln("<info>Successfully  extracted $counter translation strings</info>");
    }
}