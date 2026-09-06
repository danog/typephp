<?php

use TypePhp\CompilerTest;
use TypePhp\Exception\TestError;

final class VarIntTypesTest extends BaseTest
{
    public function testRemovedNativeTypesDirectiveHasStableMigrationDiagnostic(): void
    {
        $this->expectException(TestError::class);
        $this->expectExceptionMessage(
            '`use native_types` has been removed; native scalar types are now the default',
        );

        $compiler = $this->createCompiler();
        $source = TYPEPHP_ROOT_PATH . '/phpunit/code/removed-native-types.php';
        $compiler->addFiles([$source]);
        $compiler->prepareFile($source);
    }

    public function testVarIntTypesDirectiveIsCaseInsensitiveAndOnlyBoxesIntegers(): void
    {
        $code = $this->compileSource(
            $this->createCompiler(),
            TYPEPHP_ROOT_PATH . '/phpunit/code/varint-types-case-insensitive.php',
        );

        self::assertStringContainsString('php::Var integer = 42L;', $code);
        self::assertStringContainsString('php::Float floating = php::toFloat(1.5);', $code);
        self::assertStringContainsString('php::Bool boolean = php::toBool(true);', $code);
    }

    public function testVarIntTypesModeDoesNotLeakIntoTheNextFile(): void
    {
        $compiler = $this->createCompiler();
        $varIntSource = TYPEPHP_ROOT_PATH . '/phpunit/code/local-literal-declaration-initializer-varint.php';
        $defaultSource = TYPEPHP_ROOT_PATH . '/phpunit/code/local-literal-declaration-initializer.php';
        $compiler->addFiles([$varIntSource, $defaultSource]);

        $compiler->prepareFile($varIntSource);
        $compiler->prepareFile($defaultSource);
        $this->compilePrepared($compiler, $varIntSource);
        $defaultCode = $this->compilePrepared($compiler, $defaultSource);

        self::assertStringContainsString('php::Int integer = php::toInt(42L);', $defaultCode);
        self::assertStringNotContainsString('php::Var integer = 42L;', $defaultCode);
    }

    private function createCompiler(): CompilerTest
    {
        global $translator;
        $compiler = CompilerTest::create(TYPEPHP_ROOT_PATH);
        $translator = $compiler;
        return $compiler;
    }

    private function compileSource(CompilerTest $compiler, string $source): string
    {
        $compiler->addFiles([$source]);
        $compiler->prepareFile($source);
        return $this->compilePrepared($compiler, $source);
    }

    private function compilePrepared(CompilerTest $compiler, string $source): string
    {
        $generated = $compiler->convertFile($source);
        $code = file_get_contents($generated);
        self::assertIsString($code);
        return $code;
    }
}
