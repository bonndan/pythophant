PythoPhant
==========

An expermental php preprocessor, loosely inspired by python, rails etc. It's PHP
without many braces and indentation marking the blocks.

Rules
-----

* Indentation counts, i.e. makes the blocks. Indentation is 4 spaces.
* The semicolon is not required and should be omitted. A newline (PHP_EOL) ends a statement.

### Variables and Constants

* The leading dollar sign is not required for normal variables. Write one for dynamic variable access.
* Variables cannot be named like constants, because uppercase strings are treated like constants.
* String concatenation is implicit, do not use "." to concatenate string except on newlines.

### Classes, Methods and Members

* "class" or "interface" declarations have to be placed in the code.
* The "->" operator should be written as "."
* "@" is a shortcut for "$this->"
* The explicit declaration "function" can be omitted if a visibility modifier is used or if the indentation level is one.
* Opening and closing braces of a function's signature must on the same line.

### Json (experimental)

* Write native json using "[]" for arrays and "{}" for objects, ":" assigns values.
* Json objects are casted from (associative) arrays.

Some Magic
----------
* the colon ":" following a string is treated as an opening brace, the closing brace is inserted at eol
* use "!" and "?" for easily readable expressions like "myVar ctype_alnum?" or "myArray explode('.')!
* "!" can also be written as "not"
* the keyword "accessible" generates getters and setters for private class vars 

Todo
----

* multi-line args for if and similar conditionals 