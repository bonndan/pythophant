<?php

/**
 * File scanner, uses the php tokenizer
 * 
 * @category PythoPhant 
 */
class PythoPhant_Scanner implements Scanner
{
    /**
     * @var TokenList
     */
    private $tokenList;

    /**
     * token factory
     * @var TokenFactory 
     */
    private $tokenFactory;

    /**
     * token factory
     * 
     * @param TokenFactory $factory  factory instance
     */
    public function __construct(TokenFactory $factory)
    {
        $this->tokenFactory = $factory;
        $this->tokenList = new TokenList;
    }

    /**
     * parses a string
     * 
     * @param string $source
     * 
     * @return void 
     */
    public function scanSource($source)
    {
        $tokens = token_get_all($source);
        $currentLine = 1;

        foreach ($tokens as $index => $token) {

            $currentLine = (is_array($token) && isset($token[2])) ?
                $token[2] : $currentLine;
            $content = is_string($token) ? $token : $token[1];

            try {
                $tokenNames = (array) $this->tokenFactory->getTokenName($token);
            } catch (LogicException $exception) {
                print(
                    $exception->getMessage() . ' in line ' . $currentLine . ': '
                    . serialize($content)
                );
                return;
            }

            foreach ($tokenNames as $tokenName) {
                try {
                    $tokenInstance = $this->tokenFactory->createToken(
                        $tokenName, $content, $currentLine
                    );
                } catch (PythoPhant_Exception $exc) {
                    $message = $exc->getMessage() . ' in line ' . $currentLine;
                    if (isset($tokens[$index-1])) {
                        $message .= ' after ' . @$tokens[$index-3][1] . @$tokens[$index-2][1] . @$tokens[$index-1][1];
                    } else {
                        $message .= ' before ' . @$tokens[$index+1][1];
                    }
                        
                    throw new PythoPhant_Exception($message);
                }
                $this->tokenList->pushToken($tokenInstance);
            }
        }
    }

    /**
     * get the token list
     * 
     * @return TokenList 
     */
    public function getTokenList()
    {
        return $this->tokenList;
    }

}