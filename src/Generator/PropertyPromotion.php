<?php
/**
 * This file is part of TypePHP.
 *
 * @link     https://www.swoole.com/
 * @contact  service@swoole.com
 */

namespace TypePhp\Generator;

use PhpParser\Node;
use PhpParser\Node\Expr;
use TypePhp\Entity\ArgInfo;
use TypePhp\Entity\FunctionDef;

trait PropertyPromotion
{
    /**
     * Constructor property promotion of a compiled (non-native, non-trait)
     * class is compiled as the assignments `$this->prop = $prop;` PHP itself
     * desugars it to: the property is declared on this class, so the write is
     * a slot write with the property's static type check, not a runtime
     * property lookup by name (which allocated the name and hashed it on
     * every construction). Must run before the local declarations are
     * generated, since the assignments may need temporaries.
     */
    protected function genPropertyPromotionStmts(FunctionDef $functionDef): string
    {
        $stmts = [];
        $code = '';
        foreach ($functionDef->argInfoList as $argInfo) {
            if (!$argInfo->property) {
                continue;
            }
            if ($this->classDef === null || $this->classDef->nativeObject || $this->classDef->trait) {
                $code .= $this->genPropertyPromotion($argInfo);
                continue;
            }
            $propertyName = $argInfo->phpName ?: $this->unescapeVarName($argInfo->name);
            $stmts[] = new Node\Stmt\Expression(new Expr\Assign(
                new Expr\PropertyFetch(new Expr\Variable('this'), new Node\Identifier($propertyName)),
                new Expr\Variable($propertyName),
            ));
        }
        if ($stmts !== []) {
            $code .= $this->parseStmts($stmts);
        }
        return $code;
    }

    protected function genPropertyPromotion(ArgInfo $argInfo): string
    {
        $code = '';
        $propertyName = $argInfo->phpName ?: $this->unescapeVarName($argInfo->name);
        if ($this->classDef?->nativeObject) {
            $value = $this->getNativeObjectArgumentType($argInfo) !== null
                ? $argInfo->name
                : $this->convertExprFromType($argInfo->type, $argInfo->name);
            $property = $this->classDef->getProperty($propertyName);
            return 'this_.' . $this->getNativeObjectPropertyCppName($property, $this->classDef)
                . ' = ' . $value . ';' . PHP_EOL;
        }
        // Write through the declaring class scope so that private promoted
        // properties of a parent class are found when `$this` is a subclass.
        $code .= $this->withoutLocalClassEntryHoisting(
            fn () => $this->emitDynamicPropertyWrite('this_', $this->genCharPtr($propertyName), $argInfo->name),
        );
        $code .= ";\n";
        return $code;
    }
}
