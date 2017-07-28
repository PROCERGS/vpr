<?php
namespace PROCERGS\VPR\CoreBundle\DoctrineExtensions\Query\PostgreSQL;


use Doctrine\ORM\Query\Lexer, Doctrine\ORM\Query\AST\Functions\FunctionNode;

class Unaccent1 extends FunctionNode
{

    private $var1;

    public function getSql(\Doctrine\ORM\Query\SqlWalker $sqlWalker)
    {
        return  'lower_unaccent(' . $this->var1->dispatch($sqlWalker) . ')';
    }

    public function parse(\Doctrine\ORM\Query\Parser $parser)
    {
        $parser->match(Lexer::T_IDENTIFIER);
        $parser->match(Lexer::T_OPEN_PARENTHESIS);
        $this->var1 = $parser->ArithmeticPrimary();
        $parser->match(Lexer::T_CLOSE_PARENTHESIS);
    }
}