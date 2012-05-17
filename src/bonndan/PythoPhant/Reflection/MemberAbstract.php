<?php
namespace PythoPhant\Reflection;

use PythoPhant\Token;
use PythoPhant\TokenList;
use PythoPhant\NewlineToken;

/**
 * MemberAbstract
 * 
 * abstract class implementing reflection element methods
 * 
 */
abstract class MemberAbstract extends ElementAbstract implements Member
{

    /**
     * modifiers, visibility
     * @var string
     */
    protected $modifiers = 'public';

    /**
     * return type
     * @var Token 
     */
    protected $type = null;

    /**
     * array of body tokens
     * @var array 
     */
    protected $bodyTokens = array();

    /**
     * the own token list
     * @var type 
     */
    protected $tokenList = null;

    /**
     * set modifiers like visibility, static, final
     * 
     * @param array $modifiers 
     */
    public function setModifiers(array $modifiers)
    {
        foreach ($modifiers as $key => $mod) {
            if ($mod instanceof Token) {
                $modifiers[$key] = $mod->getContent();
            }
        }

        $this->modifiers = implode(' ', $modifiers);
    }

    /**
     * returns the modifiers (visibility, abstract etc)
     * 
     * @return string
     */
    public function getModifiers()
    {
        return $this->modifiers;
    }

    /**
     * set the return true, force original content form ReturnValueToken
     * 
     * @param Token $type 
     */
    public function setType(Token $type)
    {
        $this->type = $type;
    }

    /**
     * add a collection of tokens to the body
     * 
     * @param array $tokens 
     * 
     * @return void
     */
    public function addBodyTokens(array $tokens)
    {
        foreach ($tokens as $token) {
            if ($token instanceof Token) {
                array_push($this->bodyTokens, $token);
            }
        }
    }

    /**
     * returns all body tokens in a tokenlist, adds a trailing newline at last
     * position if not present
     * 
     * @return TokenList 
     */
    public function getBodyTokenList()
    {
        if ($this->tokenList === null) {
            $this->tokenList = new TokenList();
            foreach ($this->bodyTokens as $token) {
                $this->tokenList->pushToken($token);
            }

            $count = $this->tokenList->count();
            if ($count > 0) {
                $last = $this->tokenList->offsetGet($count-1);
                if (!$last instanceof NewLineToken) {
                    $this->tokenList->pushToken(NewLineToken::createEmpty());
                }
            }
        }

        return $this->tokenList;
    }

    /**
     * returns the variable type
     * 
     * @return Token
     */
    public function getType()
    {
        return $this->type;
    }

}