<?php
/**
 * MacroConsumer
 * 
 * interface for tokens using macros, i.e. requiring a scanner
 */
interface MacroConsumer
{
    /**
     * inject a scanner to tokenize the macro
     * 
     * @param Scanner $scanner instance of scanner
     */
    public function setScanner(Scanner $scanner);
}