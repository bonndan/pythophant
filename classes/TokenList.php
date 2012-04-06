<?php

require_once dirname(__FILE__) . '/Token.php';
/**
 * token list
 * 
 *  
 */
class TokenList implements Iterator, Countable, ArrayAccess
{
    /**
     * pointer
     * @var int 
     */
    private $position = 0;
    
    /**
     * tokens
     * @var array Token[]
     */
    private $tokens = array();
    
    /**
     * add a new token to the list
     * 
     * @param Token $token instance
     * 
     * @return TokenList
     */
    public function pushToken(Token $token)
    {
        $this->tokens[] = $token;
        return $this;
    }
    
    /**
     * inject a token at a certain position in the list
     * 
     * @param Token $token
     * @param int   $index 
     * 
     * @return TokenList
     */
    public function injectToken(Token $token, $index)
    {
        array_splice($this->tokens, $index, 0, array($token));
        return $this;
    }
    
    /**
     * retrieve the index of a specific token
     * 
     * @param Token $token
     * 
     * @return int
     * @throws OutOfBoundsException
     */
    public function getTokenIndex(Token $token)
    {
        $index = array_search($token, $this->tokens, true);
        
        if ($index === FALSE) {
            throw new OutOfBoundsException('Token is not part of the list');
        }
        
        return $index;
    }
    
    /**
     * get the next token whihc is not a whitespace
     * 
     * @param Token $token
     * @param bool  $newLineEnds flag whether the newline token ends search
     * @param int   $incrementor search direction and step
     * 
     * @return Token|Null 
     */
    public function getNextNonWhitespace(Token $token, $newLineEnds = true, $incrementor = 1)
    {
        $index = $this->getTokenIndex($token);
        while($this->offsetExists($index+$incrementor)) {
            $next = $this->tokens[$index+$incrementor];
            if ($newLineEnds && $next instanceof NewLineToken) {
                break;
            }
            if (!in_array($next->getTokenName(), array('T_WHITESPACE', Token::T_INDENT))) {
                return $next;
            }
            $index = $index + $incrementor;
        }
    }
    
    /**
     * get the previous which is not a whitespace
     * 
     * @param Token $token
     * 
     * @return Token|Null 
     */
    public function getPreviousNonWhitespace(Token $token, $newLineEnds = true)
    {
        return $this->getNextNonWhitespace($token, $newLineEnds, -1);
    }
    
    /**
     * check if a token of a list is within a list of token names
     * 
     * @param array $tokens
     * @param array $names
     * 
     * @return boolean 
     */
    public function isTokenIncluded(array $tokens, array $names)
    {
        foreach ($tokens as $token) {
            if (is_null($token)) {
                return false;
            }
            if (in_array($token->getTokenName(), $names)) {
                return true;
            }
        }

        return false;
    }
    
    /**
     * moves tokens around, returns the new index of the last token
     * 
     * @param array $tokens      tokens to move
     * @param int   $destination index
     * 
     * @return int
     */
    public function moveTokensBefore(array $tokens, Token $destination)
    {
        if (count($tokens) == 0) {
            throw new PythoPhant_Exception(
                'Cannot move empty token list before token '
                . $destination->getTokenName() 
                . ' (' . $destination->getContent().', line ' 
                . $destination->getLine(). ')'
            );
        }
        
        $offset = 0;
        foreach ($tokens as $token) {
            $this->offsetUnset($this->getTokenIndex($token));
            $destIndex = $this->getTokenIndex($destination);
            $this->injectToken($token, $destIndex + $offset);
            //$offset++;
        }
        
        return $this->getTokenIndex($token);
    }
    
    /**
     * Iterator methods
     */
    public function rewind()  {$this->position = 0;}
    public function current() {return $this->tokens[$this->position];}
    public function key()     {return $this->position;}
    public function next()    {++$this->position;}
    public function valid()   {return isset($this->tokens[$this->position]);}
    
    /**
     * count 
     */
    public function count()
    {
        return count($this->tokens);
    }
    
    /**
     * ArrayAccess methods
     */
    public function offsetExists($offset){return isset($this->tokens[$offset]);}
    public function offsetGet($offset) {return $this->tokens[$offset];}
    public function offsetSet($offset, $value){throw new Exception('Use injectToken');}
    
    /**
     * remove a token, rebuild the index
     * 
     * @param type $offset 
     */
    public function offsetUnset($offset)
    {
        unset($this->tokens[$offset]);
        $this->tokens = array_values($this->tokens);
    }
    
}