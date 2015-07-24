<?php
namespace Sc\CoreBundle\Extension\Doctrine;
 
use Doctrine\ORM\Query\Lexer;
use Doctrine\ORM\Query\AST\Functions\FunctionNode;
 
/**
 * @example by https://gist.github.com/1234419 Jérémy Hubert 
 * "MATCH_AGAINST" "(" {StateFieldPathExpression ","}* InParameter {Literal}? ")"
 * 
 * return $this->createQueryBuilder('p')
 *   ->addSelect("MATCH_AGAINST (p.name, p.country, p.street, p.postal, p.city, p.state, :address 'IN NATURAL MODE') as score")
 *   ->add('where', 'MATCH_AGAINST(p.name, p.country, p.street, p.postal, p.city, p.state, :address) > 0.8')
 *   ->setParameter('address', $address)
 *   ->getQuery()
 *   ->getResult(); 
 */
class MatchAgainstFunction extends FunctionNode {
 
    public $columns = array();
    public $needle;
    public $mode;
 
    public function parse(\Doctrine\ORM\Query\Parser $parser)
    {
 
        $parser->match(Lexer::T_IDENTIFIER);
        $parser->match(Lexer::T_OPEN_PARENTHESIS);
 
        do {
            $this->columns[] = $parser->StateFieldPathExpression();
            $parser->match(Lexer::T_COMMA);
        }
        while ($parser->getLexer()->isNextToken(Lexer::T_IDENTIFIER));
 
        $this->needle = $parser->InParameter();
 
        while ($parser->getLexer()->isNextToken(Lexer::T_STRING)) {
            $this->mode = $parser->Literal();
        }
 
        $parser->match(Lexer::T_CLOSE_PARENTHESIS); 
    }
 
    public function getSql(\Doctrine\ORM\Query\SqlWalker $sqlWalker)
    {
        $haystack = null;
 
        $first = true;
        foreach ($this->columns as $column) {
            $first ? $first = false : $haystack .= ', ';
            $haystack .= $column->dispatch($sqlWalker);
        }
 
        $query = "MATCH(" . $haystack .
            ") AGAINST (" . $this->needle->dispatch($sqlWalker);
 
        if($this->mode) {
            $query .= " " . $this->mode->dispatch($sqlWalker) . " )";
        } else {
            $query .= " )";
        }
 
        return $query;
    }
} 