<?php

namespace Psalm\MockeryPlugin\Hooks;

use PhpParser;
use Psalm\Codebase;
use Psalm\CodeLocation;
use Psalm\Context;
use Psalm\FileManipulation;
use Psalm\StatementsSource;
use Psalm\Type;
use Psalm\Plugin\Hook;

class MockReturnTypeUpdater implements Hook\AfterMethodCallAnalysisInterface
{
    /**
     * Called after an expression has been checked
     *
     * @param  PhpParser\Node\Expr  $expr
     * @param  Context              $context
     * @param  string[]             $suppressed_issues
     * @param  FileManipulation[]   $file_replacements
     *
     * @return null
     */
    public static function afterMethodCallAnalysis(
        PhpParser\Node\Expr $expr,
        string $method_id,
        string $appearing_method_id,
        string $declaring_method_id,
        Context $context,
        StatementsSource $statements_source,
        Codebase $codebase,
        array &$file_replacements = [],
        Type\Union &$return_type_candidate = null
    ) {
        if ($return_type_candidate && $method_id === 'Mockery::mock' && isset($expr->args[0])) {
            $first_arg = $expr->args[0]->value;

            $fq_class_name = null;

            if ($first_arg instanceof PhpParser\Node\Expr\ClassConstFetch
                && $first_arg->name instanceof PhpParser\Node\Identifier
                && $first_arg->name->name === 'class'
            ) {
                $fq_class_name = $first_arg->class->getAttribute('resolvedName');
            } elseif ($first_arg instanceof PhpParser\Node\Expr\BinaryOp\Concat
                && $first_arg->left instanceof PhpParser\Node\Expr\ClassConstFetch
                && $first_arg->left->name instanceof PhpParser\Node\Identifier
                && $first_arg->left->name->name === 'class'
                && $first_arg->right instanceof PhpParser\Node\Scalar\String_
                && $first_arg->right->value[0] === '['
            ) {
                $fq_class_name = $first_arg->left->class->getAttribute('resolvedName');
            }

            if ($fq_class_name) {
                foreach ($return_type_candidate->getTypes() as $return_atomic_type) {
                    if ($return_atomic_type instanceof Type\Atomic\TNamedObject) {
                        $return_atomic_type->value = 'Mockery\MockInterface';
                        $return_atomic_type->extra_types[] = new Type\Atomic\TNamedObject($fq_class_name);
                        break;
                    }
                }
            }
        }

        return null;
    }
}
