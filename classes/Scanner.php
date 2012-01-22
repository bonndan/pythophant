<?php

require_once dirname(__FILE__) . '/TokenFactory.php';
require_once dirname(__FILE__) . '/TokenList.php';

/**
 * File scanner, uses php tokenizer
 * 
 *  
 */
class Scanner
{

    /**
     * @var TokeList
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
     * @param string       $filename name and path of the file to convert
     * @param TokenFactory $factory  factory instance
     */
    public function __construct(TokenFactory $factory)
    {
        $this->tokenFactory = $factory;
        $this->tokenList = new TokenList;
    }

    /**
     * parses the sources and pushes all tokens on the list
     * 
     * @param string $filename file contents
     */
    public function scanFile($filename)
    {
        if (!is_file($filename)) {
            throw new InvalidArgumentException('Not a file ' . $filename);
        }

        return $this->scanSource(file_get_contents($filename));
    }

    /**
     * parses a string
     * 
     * @param type $source
     * @return type 
     */
    public function scanSource($source)
    {
        $tokens = token_get_all($source);
        $currentLine = 1;

        foreach ($tokens as $token) {

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
                $tokenInstance = $this->tokenFactory->createToken(
                    $tokenName, $content, $currentLine
                );
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