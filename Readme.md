PythoPhant
==========

An expermental php preprocessor, loosely inspired by python, rails etc. It's PHP
without many braces and indentation marking the blocks.

Rules
-----

* Indentation counts. Indentation is 4 spaces.
* The semicolon is not required and should be omitted. A newline (PHP_EOL) ends a statement.

### Variables

* The leading dollar sign is not required.
* Variables cannot be named like constants.
* Uppercase strings are treated like constants.
* String concatenation can be written as "+" (experimental)

### Classes, Methods and Members

* "class" or "interface" declarations have to be placed in the code.
* "implements" or "extends" must be on the same line as the class declaration (FIXME)
* The "->" operator can be written as "."
* "@" is a shortcut for "$this->"
* The explicit declaration "function" can be omitted if a visibility modifier is used or if the indentation level is one.
* Opening and closing braces of a function's signature must on the same line.

### Json (experimental)

* Write native json using "[]" for arrays and "{}" for objects, ":" assigns values.
* Json objects are casted from (associative) arrays.

Todo
----

* multi-line args for if and similar conditionals 
* "implements" or "extends" can be on the different line as the class declaration