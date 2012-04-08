<?php
/**
 * grammar rules
 * 
 *  
 */
abstract class PythoPhant_Grammar
{
    /**
     * indentation is four spaces 
     */
    const INDENTATION_LEVEL = 4;
    
    const T_ASSIGN = '=';
    const T_IF = 'if';
    const T_IN = 'in'; 
    const T_IS = 'is';
    const T_OPEN_BRACE = '(';
    const T_CLOSE_BRACE = ')';
    const T_OPEN_ARRAY = '[';
    const T_CLOSE_ARRAY = ']';
    const T_OPEN_BLOCK = "{";
    const T_CLOSE_BLOCK = "}";
    const T_THIS = 'this';
    const T_THIS_MEMBER = '@';
    const T_SELF = 'self';
    const T_MEMBER = '.';
    const T_COMMA = ',';
    const T_NEWLINE = "\n";
    const T_RETURNVALUE = "";
    const T_QUESTION = "?";
    const T_COLON = ":";
    const T_NOT = "not";
    const T_EXCLAMATION = "!";
    
    const T_BIT_AND = "&";
    const T_BIT_OR = "|";
    const T_BIT_XOR = "^";
    const T_BIT_NOT = "~";
    const T_BIT_SHIFTLEFT = "<<";
    const T_BIT_SHIFTRIGHT = ">>";
    
    const T_JSON_OPEN_OBJECT = "{";
    const T_JSON_CLOSE_OBJECT = "}";
    
    const T_ACCESSIBLE = 'accessible';
    
    /**
     * declaration tokens
     * @var array 
     */
    public static $declarations = array(
        'T_FUNCTION',
        'T_CLASS',
        'T_EXTENDS',
        'T_IMPLEMENTS',
        'T_INTERFACE'
    );
    
        
    /**
     * return values
     * 
     * @var array 
     */
    public static $returnValues = array(
        'string', 'int', 'integer', 'float', 'double', 'bool', 'boolean', 'void'
    );
    
    /**
     *
     * @var array 
     */
    public static $modifiers = array(
        'T_PRIVATE',
        'T_PROTECTED',
        'T_PUBLIC',
        'T_FINAL',
        'T_ABSTRACT',
        'T_ACCESSIBLE',
    );
    
    /**
     * control tokens
     * @var array 
     */
    public static $controls = array(
        'T_IF',
        'T_ELSE',
        'T_ELSEIF',
        'T_FOR',
        'T_FOREACH',
        'T_WHILE',
        'T_SWITCH',
        'T_CASE',
        'T_TRY',
        'T_CATCH',
    );
    
    /**
     * control tokens which are not followed by braces
     * @var array 
     */
    public static $controlsWithoutBraces = array(
        'T_ELSE',
        'T_CASE',
        'T_TRY',
    );
    
    /**
     * visibility tokens
     * @var array 
     */
    public static $visibilities = array(
        'T_PRIVATE',
        'T_PROTECTED',
        'T_PUBLIC',
    );
    
    /**
     * tokens which prevent a newline semicolon
     * @var type 
     */
    public static $preventSemicolon = array(
        'T_OPEN_TAG',
        'T_DOC_COMMENT',
        'T_COMMA',
        'T_OPEN_ARRAY',
        'T_JSON_OPEN_OBJECT',
    );
    
    /**
     * tokens indicating that the next token may be a variable
     * @var type 
     */
    public static $preVariableIndicators = array(
        Token::T_RETURNVALUE,
        'T_STATIC',
        'T_PRIVATE',
        'T_PROTECTED',
        'T_ACCESSIBLE',
        'T_ASSIGN',
        'T_DOUBLE_COLON',
        'T_COMMA',
        'T_STRING',
        Token::T_OPEN_BRACE,
        'T_DOUBLE_ARROW',
        'T_AS',
        'T_ECHO',
        'T_BOOLEAN_AND',
        'T_BOOLEAN_OR',
        'T_LOGICAL_AND',
        'T_LOGICAL_OR',
        'T_RETURN',
        'T_NOT',
        'T_MEMBER',
        'T_CONCAT',
        'T_ARRAY',
        'T_OPEN_ARRAY',
        'T_COLON',
        Token::T_JSON_OPEN_ARRAY,
    );
    
    /**
     * tokens indicating that the previous token is a variable 
     */
    public static $postVariableIndicators = array(
        'T_CLOSE_BRACE',
        'T_CLOSE_ARRAY',
        'T_COMMA',
        'T_ASSIGN',
        'T_MEMBER',
        'T_AS',
        'T_DOUBLE_ARROW',
        'T_OPEN_ARRAY',
        'T_BOOLEAN_AND',
        'T_BOOLEAN_OR',
        'T_LOGICAL_AND',
        'T_LOGICAL_OR',
        'T_IS_IDENTICAL',
        'T_IS_NOT_IDENTICAL',
        'T_CONST',
        'T_CONSTANT_ENCAPSED_STRING',
        'T_CLOSE_ARRAY',
        Token::T_JSON_ASSIGN,
    );
    
    /**
     * block closing tokens
     * @var type 
     */
    public static $blockClosers = array(
        Token::T_CLOSE_BRACE,
        Token::T_JSON_CLOSE_ARRAY,
        Token::T_JSON_CLOSE_OBJECT,
    );
    
    /**
     * not functions, but require braces
     * @var array 
     */
    public static $constructsWithBraces = array(
        'T_UNSET',
        'T_ISSET',
        'T_EMPTY',
    );
    /**
     * @deprecated
     * @var array 
     */
    public static $stopsQuestionSubject = array(
        'T_IF',
        'T_ELSEIF',
        'T_NOT',
        'T_BOOLEAN_AND',
        'T_BOOLEAN_OR',
        'T_LOGICAL_AND',
        'T_LOGICAL_OR',
        'T_RETURN',
        'T_ASSIGN',
    );
    
    /**
     * get the token names which mark the end of an expression when searching
     * backwards
     * 
     * @return array 
     */
    public static function getExpressionDelimiters()
    {
        return array_merge(
            self::$controls,
            array(
                'T_NOT',
                'T_BOOLEAN_AND',
                'T_BOOLEAN_OR',
                'T_LOGICAL_AND',
                'T_LOGICAL_OR',
                'T_RETURN',
                'T_ASSIGN',
            )
        );
    }
}
