Patch Builder
=============

[![Build Status](https://travis-ci.org/tomphp/patch-builder.png?branch=master)](https://travis-ci.org/tomphp/patch-builder)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/tomphp/patch-builder/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/tomphp/patch-builder/?branch=master)

A PHP Library which allows your to load some text, make some modifications to it
and then generate a patch.

Installation
------------

Installation is quite and easy with composer:

`composer require tomphp/patch-builder:dev-master`

Usage
-----

Simply create a `PatchBuffer` containing the content you want to modify then
apply the modifications with the methods available:

```php
use TomPHP\PatchBuilder\PatchBuffer;
use TomPHP\PatchBuilder\Types\LineNumber;
use TomPHP\PatchBuilder\Types\LineRange;

$buffer = PatchBuffer::createWithContents(file('somedata.txt'));

// Insert 2 at line 27
$buffer->insert(new LineNumber(27), array('hello', 'world'));

// Delete lines 12 to 16
$buffer->delete(LineRange::createFromNumbers(12, 16));

// Replace line 4 and 5 with a new line
$buffer->replace(LineRange::createFromNumbers(4, 5), array('hello moon'));
```

###Line Numbers

Line numbers and ranges are specified as `TomPHP\PatchBuilder\Types\LineNumber`
and `TomPHP\PatchBuilder\Types\LineRange` objects and refer to the line in the 
buffer in it's modified state.

If you want to refer to a line but its number in the original content you can use
a `TomPHP\PatchBuilder\Types\OriginalLineNumber` object instead and it will
be translated to the current line number in the modified file.

```php
use TomPHP\PatchBuilder\PatchBuffer;
use TomPHP\PatchBuilder\Types\LineNumber;
use TomPHP\PatchBuilder\Types\OrignalLineNumber;

$buffer = PatchBuffer::createWithContents(file('somedata.txt'));

// Insert 2 at line 5
$buffer->insert(new LineNumber(5), array('hello', 'world'));

// Actually inserts at line 8 because 2 lines have been added before here.
$buffer->insert(new OriginalLineNumber(6), array('hello', 'moon'));
```

If you try to access a line by original line number which has since been deleted
a `TomPHP\PatchBuilder\LineTracker\Exception\DeletedLineException` will be thrown.

###Outputting the patch

The buffer can be converted to a patch using a `PatchBuilder` class. Currently
only 1 `PatchBuilder` class is provided:

```php
use TomPHP\PatchBuilder\PatchBuffer;
use TomPHP\PatchBuilder\Builder\PhpDiffBuilder;

$filename = 'myfile.txt';

$src = 'orignal/' . $filename;
$dest = 'new/' . $filename;

$buffer = PatchBuffer::createWithContents(file($src));

// ... perform buffer manipulations ...

$builder = new PhpDiffBuilder();

echo $builder->buildPatch($src, $dest, $buffer);
```
