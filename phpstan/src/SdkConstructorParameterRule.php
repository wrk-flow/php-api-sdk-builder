<?php

declare(strict_types=1);

namespace WrkFlow\PHPstan\ApiSdkBuilder;

use PhpParser\Node;
use PHPStan\Analyser\Scope;
use PHPStan\Node\InClassMethodNode;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;
use PHPStan\Type\ObjectType;
use PHPStan\Type\TypeCombinator;
use Psr\Http\Message\ResponseInterface;
use WrkFlow\ApiSdkBuilder\Contracts\SDKContainerFactoryContract;
use WrkFlow\ApiSdkBuilder\Entities\EndpointDIEntity;
use WrkFlow\ApiSdkBuilder\Interfaces\EndpointInterface;
use WrkFlow\ApiSdkBuilder\Responses\AbstractResponse;
use Wrkflow\GetValue\GetValue;

/**
 * @implements Rule<InClassMethodNode>
 */
final class SdkConstructorParameterRule implements Rule
{
    public function getNodeType(): string
    {
        return InClassMethodNode::class;
    }

    public function processNode(Node $node, Scope $scope): array
    {
        if (strtolower($node->getOriginalNode()->name->toString()) !== '__construct') {
            return [];
        }

        $class = new ObjectType($node->getClassReflection()->getName());
        $names = [];

        if ((new ObjectType(EndpointInterface::class))->isSuperTypeOf($class)->yes()) {
            $names[EndpointDIEntity::class] = 'di';
        }

        if ((new ObjectType(AbstractResponse::class))->isSuperTypeOf($class)->yes()) {
            $names[ResponseInterface::class] = 'response';
            $names[GetValue::class] = 'body';
            $names[SDKContainerFactoryContract::class] = 'container';
        }

        $errors = [];

        foreach ($node->getMethodReflection()->getVariants()[0]->getParameters() as $index => $parameter) {
            foreach ($names as $type => $expected) {
                if ((new ObjectType($type))->isSuperTypeOf(TypeCombinator::removeNull($parameter->getType()))->yes()
                    && $parameter->getName() !== $expected) {
                    $errors[] = RuleErrorBuilder::message(sprintf(
                        'SDK constructor parameter $%s must be named $%s; the SDK factory supplies this argument by name.',
                        $parameter->getName(),
                        $expected,
                    ))->identifier('larastrict.sdk.constructorParameter')
                        ->line($node->getOriginalNode()->params[$index]->getStartLine())
                        ->build();
                }
            }
        }

        return $errors;
    }
}
