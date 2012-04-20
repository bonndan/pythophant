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
     * inject a token at a certain position in the list. The position can be
     * passed as int or Token
     * 
     * @param Token     $token injected token
     * @param Token|int $index position
     * 
     * @return TokenList
     */
    public function injectToken(Token $token, $index)
    {
        if ($index instanceof Token) {
            $index = $this->getTokenIndex($index);
        }
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
            throw new OutOfBoundsException(
                'Token is not part of the list: ' . $token->getContent()
            );
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
            if (!in_array($next->getTokenName(), array(Token::T_WHITESPACE, Token::T_INDENT))) {
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
     * get all tokens belonging to an expression in reverse direction starting
     * from the given token
     * 
     * @param Token $token
     * @param bool  $newLineEnds
     * 
     * @return array Token[]
     */
    public function getPreviousExpression(Token $token, $newLineEnds = true)
    {
        $tokens = array();
        $prev = $this->getPreviousNonWhitespace($token, $newLineEnds);
        $stop = false;
        $delimiters = PythoPhant_Grammar::getExpressionDelimiters();
        while($prev instanceof Token && !$stop) {
            $stop = $this->isTokenIncluded(array($prev), $delimiters);
            if ($stop) {
                break;
            }
            $tokens[] = $prev;
            $prev = $this->getPreviousNonWhitespace($prev, $newLineEnds);
        }
        
        $tokens = array_reverse($tokens);
        
        return $tokens;
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
     * @param array $tokens      tokens to move, from left to right
     * @param Token $destination index
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
        
        foreach ($tokens as $token) {
            $this->offsetUnset($this->getTokenIndex($token));
            $destIndex = $this->getTokenIndex($destination);
            $this->injectToken($token, $destIndex);
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
    
    /**
     *
     * @param type $offset
     * 
     * @return Token 
     * @throws Ou
     */
    public function offsetGet($offset)
    {
        if (!$this->offsetExists($offset)) {
            throw new OutOfBoundsException('Unknown offset: ' . $offset);
        }
        
        return $this->tokens[$offset];
    }
    
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
 
    /**
     * pick a token from the list relative the passed Token
     * 
     * @param Token $position      the token defining the position
     * @param int   $offset        the offset, positive or negative
     * @param bool  $nonWhiteSpace no whitespace only?
     * 
     * @return Token|null
     * @throws InvalidArgumentException
     */
    public function getAdjacentToken(Token $position, $offset, $nonWhiteSpace = true)
    {
        if (intval($offset) == 0) {
            throw new InvalidArgumentException(
                'The offset for picking a token must be different from zero.'
            );
        }
        
        if ($nonWhiteSpace == false) {
            try {
                $index = $this->getTokenIndex($position);
                return $this->offsetGet($index + intval($offset));
            } catch (OutOfBoundsException $exc) {
                return null;
            }
        }
        
        $i = 0;
        if ($offset < 0) {
            while ($i > $offset) {
                $position = $this->getPreviousNonWhitespace($position);
                $i--;
            }
        } else {
            while ($i < $offset) {
                $position = $this->getnextNonWhitespace($position);
                $i++;
            }
        }
        
        return $position;
    }
}