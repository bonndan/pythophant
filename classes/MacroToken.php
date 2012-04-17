<?php

class MacroToken extends PHPToken
implements CustomToken, MacroConsumer
{
    /**
     * scanner instance
     * @var Scanner 
     */
    protected $scanner;
    
    /**
     *
     * @param TokenList $tokenList 
     */
    public function affectTokenList(TokenList $tokenList)
    {
        $macro = new PythoPhant_Macro();
        $macroTokens = $this->scanner->scanSource($macro->getSource());
    }

    public function setAuxValue($value)
    {
        
    }

    /**
     * inject the macro scanner
     * 
     * @param Scanner $scanner 
     * 
     * @return MacroToken
     */
    public function setScanner(Scanner $scanner)
    {
        $this->scanner = $scanner;
        return $this;
    }
}
