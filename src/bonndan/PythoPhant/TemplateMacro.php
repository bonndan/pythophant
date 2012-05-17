<?php
namespace PythoPhant;

/**
 * PythoPhant_Macro
 * 
 * @author Daniel Pozzi <bonndan@googlemail.com>
 */
class TemplateMacro implements Macro
{

    /**
     * raw sources
     * @var string  
     */
    private $source;

    /**
     * params containing values to replace in source
     * @var array
     */
    private $params = array();

    /**
     * construct with a file and have its contents used as source
     * 
     * @param SplFileObject $file 
     */
    public function __construct(\SplFileObject $file = null)
    {
        if ($file instanceof \SplFileObject) {
            $this->setSource(file_get_contents($file->getPathname()));
        }
    }

    /**
     * set the raw, unprocessed source
     * 
     * @param string $source
     */
    public function setSource($source)
    {
        $this->source = (string) $source;
    }

    /**
     * set the values which will be replaced in the source
     * 
     * @param array $params 
     */
    public function setParams(array $params)
    {
        $this->params = $params;
    }

    /**
     * scans the source and returns a string
     * 
     * @return string 
     */
    public function getSource()
    {
        return vsprintf($this->source, $this->params);
    }

    /**
     * clean a token list
     * 
     * @param TokenList $tokenList
     * @param type $indentation 
     */
    public function cleanTokenList(TokenList $tokenList, $indentation = 0)
    {
        /**
         * remove open tag and newline 
         */
        $first = $tokenList[0];
        if ($first->getTokenName() == 'T_OPEN_TAG') {
            $tokenList->offsetUnset(0);
        }
        $first = $tokenList[0];
        if ($first instanceof NewLineToken) {
            $tokenList->offsetUnset(0);
        }

        /**
         * indent before each newline token 
         */
        if ($indentation > 0) {
            $lastToken = new NewLineToken(Token::T_NEWLINE, PHP_EOL, 0);
            foreach ($tokenList as $token) {
                if ($lastToken instanceof NewLineToken) {
                    if ($token instanceof DocCommentToken) {
                        $token->indent($indentation);
                    } else {
                        try {
                            $lastTokenIndex = $tokenList->getTokenIndex($lastToken) + 1;
                        } catch (OutOfBoundsException $exc) {
                            $lastTokenIndex = $tokenList->getTokenIndex($token);
                        }
                        $tokenList->injectToken(IndentationToken::create($indentation), $lastTokenIndex);
                    }
                }

                $lastToken = $token;
            }
        }
    }

}
