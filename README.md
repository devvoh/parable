## Parable PHP Framework

[![Latest Stable Version](https://poser.pugx.org/devvoh/parable/v/stable)](https://packagist.org/packages/devvoh/parable)
[![Latest Unstable Version](https://poser.pugx.org/devvoh/parable/v/unstable)](https://packagist.org/packages/devvoh/parable)
[![License](https://poser.pugx.org/devvoh/parable/license)](https://packagist.org/packages/devvoh/parable)

Parable is intended to be a tiny, fast and out-of-your-way PHP framework that just lets you get stuff done. To achieve 
this, Parable includes only what is needed to create small to medium-sized web applications, but allows any PSR-4 
compatible library to be added easily for extensibility.

So why build Parable if there's so many perfectly valid PHP frameworks already out there? Partly to see if I could and
partly because I wanted something small, quick and comfortably to quickly build something on top of.

Parable isn't by any means production-ready, secure or foolproof. So only try it if you feel like filing some bug reports!

## Requirements

- PHP 5.6, PHP 7
- Composer
- Sense of adventure

## Installation

Parable can be installed by using [Composer](http://getcomposer.org/). Simply run:

`composer require devvoh/parable ~0.8.0`

You'll get the latest version this way. If you want to install a specific version, include the version (0.8.0, for example). 
It is, however, preferred to use the above version notation, since it will also get you updated and fixed versions, but no
backwards-compatibility breaking changes.

After you've run the above command, you'll have a composer.json and a vendor folder. Parable is in there, but it's not
quite ready to be used. To initialize Parable's folder structure and files, run the following command:

`vendor/bin/parable init`

Now you're ready! Simply open the (properly installed apache2 & php 5.6 or 7) url it's in in your browser and you should
see a welcome page.

If you want to use nginx, that's cool too, but you'll have to set up url rewriting rules yourself.

## Getting Started

After you've run `parable init`, you should have a basic structure to work from. The example files show most of what you'll
need to build something, but one thing that might not be clear is how to configure Parable to work with a database.

The following example config shows a connection with a MySQL database:

    return [
        'app' => [
            'title'     => 'example',
            'version'   => '0.1.0',
        ],
        'initLocations' => [
            'app/Init',
        ],
        'database' => [
            'type'      => 'mysql',
            'location'  => 'localhost',
            'username'  => 'username',
            'password'  => 'password',
            'database'  => 'database',
        ],
    ];
    
Now, if you want to keep this out of your git (or other vcs) repository, you can place this in a separate config
file and exclude it. It's also possible to have the config class return different values based on whether
you're running dev/test/staging/production.

For an sqlite3 connection, use `'type' => 'sqlite3'`, where `'location'` corresponds to the location of the database
file on your filesystem.

## Documentation & More

Documentation is currently non-existent, but I am more than willing and available to answer any questions. I am also very
open to suggestions and improvements. Parable is what I personally need in a framework, and if it seems to fit what you
need as well 'except for these small things', I would love to hear from you to see if maybe we can't work it in somehow.

## Details

Parable probably won't ever be truly done, but the basis that stands at this point will provide a good platform to build out
from. The intent is to add unit testing, documentation and more as time moves on. For now, it's time for me to start
implementing Parable through projects, to find out where possible issues are left to find. But it's surprisingly
usable as it stands.

Any questions or constructive feedback? Find me at [devvoh.com](http://devvoh.com) or ask me a question by adding an 
issue on github.

## License

Parable PHP Framework is open-sourced software licensed under the MIT license.
