# Extract twig string
A console application to extract localizable resources from Twig templates

## Installation
```
composer require digirati/extract-twig-strings
```

## Usage
```
Usage:
  twig-extract [options] [--] [<paths>]...

Arguments:
  paths                 Paths to the Twig templates to analyze

Options:
  -d, --domain=DOMAIN   The default domain to save translations under [default: "messages"]
  -l, --locale=LOCALE   The locale to associate with exported messages [default: "en_GB"]
  -o, --output=OUTPUT   Path to the directory that messages will be stored in [default: "/Users/stephen.fraser/github.com/digirati-co-uk/extract-twig-strings"]
  -R, --recursive       Search input paths recursively for Twig files
  -x, --format=FORMAT   The format to serialize messages to.  Defaults to: po.  Must be one of: po, mo, xliff, res [default: "po"]
  -h, --help            Display this help message
  -q, --quiet           Do not output any message
  -V, --version         Display this application version
      --ansi            Force ANSI output
      --no-ansi         Disable ANSI output
  -n, --no-interaction  Do not ask any interactive question
  -v|vv|vvv, --verbose  Increase the verbosity of messages: 1 for normal output, 2 for more verbose output and 3 for debug

Help:
  Extracts `translate` calls from Twig templates
```

Example:
```
vendor/bin/twig-extract -o exported-translation path/to/templates
```
