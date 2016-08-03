## Parable PHP Framework

Parable is intended to be a tiny, fast and out-of-your-way PHP framework that just lets you get stuff done. To achieve 
this, Parable includes only what is needed to create small to medium-sized web applications, but allows any PSR-4 
compatible library to be added easily for extensibility.

So why build Parable if there's so many perfectly valid PHP frameworks already out there? Partly to see if I could and
partly because I wanted something small, quick and comfortably to quickly build something on top of.

Parable isn't by any means production-ready, secure or foolproof. So only try it if you feel like filing some bug reports!

## Requirements

- PHP 5.4+, PHP 7
- Composer
- Sense of adventure

## Installation

Parable can be installed by using [http://getcomposer.org/](Composer). Simply run:

`composer require devvoh/parable 0.8.*`

You'll get the latest version this way. If you want to install a specific version, include the version (0.8.0, for example). 
It is, however, preferred to use the above version notation, since it will also get you updated and fixed versions, but no
backwards-compatibility breaking changes.

After you've run the above command, you'll have a composer.json and a vendor folder. Parable is in there, but it's not
quite ready to be used. To initialize Parable's folder structure and files, run the following command:

`vendor/bin/parable init`

Now you're ready! Simply open the (properly installed apache2 & php 5.5+) url it's in in your browser and you should
see a welcome page.

## Documentation & More

Documentation is currently non-existent, but I am more than willing and available to answer any questions. I am also very
open to suggestions and improvements. Parable is what I personally need in a framework, and if it seems to fit what you
need as well 'except for these small things', I would love to hear from you to see if maybe we can't work it in somehow.

## Details

Parable probably won't ever be truly done, but the basis that stands at this point will provide a good platform to build out
from. Perhaps dependency injection, unit tests, documentation, etc. will all make their way into it at some point.

Any questions or constructive feedback? Find me at [devvoh.com](http://devvoh.com) or ask me a question by adding an 
issue on github.

## License

Parable PHP Framework is open-sourced software licensed under the MIT license.