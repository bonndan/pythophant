<?php
/**
 * grammar rules
 * 
 *  
 */
abstract class PythoPhant_Grammar
{
    const INDENTATION_LEVEL = 4;
    
    const OPEN_BRACE = '(';
    
    /**
     * declaration tokens
     * @var array 
     */
    public static $declarations = array(
        'T_FUNCTION',
        'T_CLASS',
        'T_INTERFACE'
    );
    
        
    /**
     * return values
     * 
     * @var array 
     */
    public static $returnValues = array(
        'string', 'int', 'float', 'double', 'bool', 'boolean', 'void'
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
    );
    
    /**
     * control tokens which are not followed by braces
     * @var array 
     */
    public static $controlsWithoutBraces = array(
        'T_ELSE',
        'T_CASE',
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
        'T_OPEN_BRACE',
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
        'T_DOUBLE_COLON',
        'T_AS',
        'T_DOUBLE_ARROW',
        'T_OPEN_ARRAY',
        'T_BOOLEAN_AND',
        'T_BOOLEAN_OR',
        'T_LOGICAL_AND',
        'T_LOGICAL_OR',
    );
    
    /**
     * block closing tokens
     * @var type 
     */
    public static $blockClosers = array(
        Token::T_CLOSE_BRACE,
        JsonToken::T_JSON_CLOSE_ARRAY,
        JsonToken::T_JSON_CLOSE_OBJECT,
    );
    
    /**
     *
     * @var type 
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
}
