<?php
/**
 * This file is part of TypePHP.
 *
 * @link     https://www.swoole.com/
 * @contact  service@swoole.com
 */

namespace TypePhp\Exception;

use PhpParser\Node;

/**
 * A local variable received values of incompatible static types. The function
 * is re-generated with that local degraded to a dynamic php::Var slot.
 */
final class LocalTypeConflict extends \RuntimeException
{
    public function __construct(
        public readonly Node $node,
        public readonly string $variable,
        string $message,
    ) {
        parent::__construct($message);
    }
}
