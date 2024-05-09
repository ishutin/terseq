<?php

declare(strict_types=1);

namespace Terseq\Builders\Operations\Query\Enums;

enum ComparisonOperator: string
{
    case EQ = '=';
    case NE = '<>';
    case LE = '<=';
    case LT = '<';
    case GE = '>=';
    case GT = '>';
    case BETWEEN = 'BETWEEN';
    case ATTRIBUTE_NOT_EXISTS = 'attribute_not_exists';
    case ATTRIBUTE_EXISTS = 'attribute_exists';
    case ATTRIBUTE_TYPE = 'attribute_type';
    case CONTAINS = 'contains';
    case BEGINS_WITH = 'begins_with';
    case SIZE = 'size';
    case IN = 'IN';
}
