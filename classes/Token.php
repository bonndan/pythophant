<?php
/**
 * Token interface
 * 
 * @package PythoPhant
 */
interface Token
{
    const T_STRING = 'T_STRING';
    const T_ASSIGN = 'T_ASSIGN';
    const T_RETURNVALUE = 'T_RETURNVALUE';
    const T_COMMA = 'T_COMMA';
    const T_NEWLINE = 'T_NEWLINE';
    const T_INDENT= 'T_INDENT';
    const T_OPEN_BRACE = 'T_OPEN_BRACE';
    const T_CLOSE_BRACE = 'T_CLOSE_BRACE';
    const T_CONST = 'T_CONST';
    const T_CONSTANT_ENCAPSED_STRING = 'T_CONSTANT_ENCAPSED_STRING';
    const T_CONCAT = 'T_CONCAT';
    const T_MEMBER = 'T_MEMBER';
    const T_CALL = 'T_CALL';
    const T_IF = 'T_IF';
    const T_SHORT_IF = 'T_SHORT_IF';
    const T_VARIABLE = 'T_VARIABLE';
    
    /**
     * constructor requires the name, the content and the line number
     * 
     * @param string $tokenName
     * @param string $content
     * @param int    $line
     */
    function __construct($tokenName, $content, $line);

    /**
     * get the content
     * 
     * @return string
     */
    function getContent();

    /**
     * get the name of the token
     * 
     * @return string 
     */
    function getTokenName();

    /**
     * set the content
     * 
     * @param string $content
     * 
     * @return Token 
     */
    function setContent($content);

    /**
     * get the line number
     * @return int 
     */
    function getLine();

    /**
     * set the line number
     * 
     * @param int $line 
     */
    function setLine($line);
}
