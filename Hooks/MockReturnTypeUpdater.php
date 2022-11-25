<?php

namespace Psalm\MockeryPlugin\Hooks;

use PhpParser;
use PhpParser\Node\Arg;
use Psalm\Plugin\EventHandler\AfterMethodCallAnalysisInterface;
use Psalm\Plugin\EventHandler\Event\AfterMethodCallAnalysisEvent;
use Psalm\Type;

class MockReturnTypeUpdater implements AfterMethodCallAnalysisInterface
{
    /**
     * Called after an expression has been checked
     */
    public static function afterMethodCallAnalysis(AfterMethodCallAnalysisEvent $event): void
    {
        $return_type_candidate = $event->getReturnTypeCandidate();
        $expr = $event->getExpr();
        $method_id = $event->getMethodId();
        if (
            $return_type_candidate
            && self::isMockMethod($method_id)
            && isset($expr->args[0])
            && $expr->args[0] instanceof Arg
        ) {
            $first_arg = $expr->args[0]->value;

            $fq_class_name = null;

            if (
                $first_arg instanceof PhpParser\Node\Expr\ClassConstFetch
                && $first_arg->name instanceof PhpParser\Node\Identifier
                && $first_arg->name->name === 'class'
            ) {
                /** @var class-string */
                $fq_class_name = $first_arg->class->getAttribute('resolvedName');
            } elseif (
                $first_arg instanceof PhpParser\Node\Expr\BinaryOp\Concat
                && $first_arg->left instanceof PhpParser\Node\Expr\ClassConstFetch
                && $first_arg->left->name instanceof PhpParser\Node\Identifier
                && $first_arg->left->name->name === 'class'
                && $first_arg->right instanceof PhpParser\Node\Scalar\String_
                && $first_arg->right->value[0] === '['
            ) {
                $left = $first_arg->left;
                /** @var class-string */
                $fq_class_name = $left->class->getAttribute('resolvedName');
            } elseif ($first_arg instanceof PhpParser\Node\Scalar\String_) {
                $value = $first_arg->value;
                if (substr($value, 0, 6) === 'alias:') {
                    $value = substr($value, 6);
                } elseif (substr($first_arg->value, 0, 9) === 'overload:') {
                    $value = substr($value, 9);
                }

                $bracket_position = strpos($value, '[');
                if ($bracket_position !== false) {
                    $fq_class_name = substr($value, 0, $bracket_position);
                } else {
                    $fq_class_name = $value;
                }
            }

            if ($fq_class_name) {
                foreach ($return_type_candidate->getAtomicTypes() as $return_atomic_type) {
                    if ($return_atomic_type instanceof Type\Atomic\TNamedObject) {
                        $new_return_type = $return_type_candidate->getBuilder();
                        $new_return_type->removeType($return_atomic_type->getId());

                        $return_atomic_type = $return_atomic_type
                            ->setValue('Mockery\MockInterface')
                            ->addIntersectionType(new Type\Atomic\TNamedObject($fq_class_name))
                        ;

                        $event->setReturnTypeCandidate($new_return_type->addType($return_atomic_type)->freeze());
                        break;
                    }
                }
            }
        }
    }

    private static function isMockMethod(string $method_id): bool
    {
        return $method_id === 'Mockery::mock' || $method_id === 'Mockery::spy';
    }
}
