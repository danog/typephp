<?php

use TypePhp\CompilerTest;
use TypePhp\Exception\TestError;

final class FixedReferenceStorageTest extends BaseTest
{
    /** @dataProvider fixedStorageProvider */
    public function testFixedStorageCannotBeCapturedByReference(string $fixture, string $type): void
    {
        $this->expectException(TestError::class);
        $this->expectExceptionMessage('of fixed type ' . $type . '; initialize it with std::any()');

        $this->compileFixture($fixture);
    }

    public static function fixedStorageProvider(): iterable
    {
        yield 'string' => ['fixed-reference-string.php', 'php::Str'];
        yield 'array' => ['fixed-reference-array.php', 'php::Array'];
        yield 'object' => ['fixed-reference-generic-object.php', 'php::Object'];
        yield 'typed object' => ['fixed-reference-object.php', 'php::Object'];
        yield 'stream' => ['fixed-reference-stream.php', 'php::Stream'];
        yield 'std container' => ['fixed-reference-std-container.php', 'php::StdVector'];
    }

    public function testFixedStorageCannotBePassedToKnownReferenceParameter(): void
    {
        $this->expectException(TestError::class);
        $this->expectExceptionMessage('variable $value of fixed type php::Str');

        $this->compileFixture('fixed-reference-argument.php');
    }

    public function testFixedStorageCannotBeUsedAsReferenceAssignmentSource(): void
    {
        $this->expectException(TestError::class);
        $this->expectExceptionMessage('variable $value of fixed type php::Array');

        $this->compileFixture('fixed-reference-assignment.php');
    }

    public function testFixedStorageCannotBeReturnedByReference(): void
    {
        $this->expectException(TestError::class);
        $this->expectExceptionMessage('variable $value of fixed type php::Str');

        $this->compileFixture('fixed-reference-return.php');
    }

    public function testFixedStaticStorageCannotBeCapturedByReference(): void
    {
        $this->expectException(TestError::class);
        $this->expectExceptionMessage('variable $value of fixed type php::Str');

        $this->compileFixture('fixed-reference-static.php');
    }

    public function testFixedStorageCannotUseToRefKeyword(): void
    {
        $this->expectException(TestError::class);
        $this->expectExceptionMessage('variable $value of fixed type php::Array');

        $this->compileFixture('fixed-reference-to-ref.php');
    }

    public function testExplicitAnyCanUseReferenceStorage(): void
    {
        $code = $this->compileFixture('fixed-reference-explicit-any.php');

        self::assertStringContainsString('php::Var value', $code);
        self::assertStringContainsString('value.toReference()', $code);
    }

    private function compileFixture(string $fixture): string
    {
        global $translator;
        $compiler = CompilerTest::create(TYPEPHP_ROOT_PATH);
        $translator = $compiler;
        $source = TYPEPHP_ROOT_PATH . '/phpunit/code/' . $fixture;
        $compiler->addFiles([$source]);
        $compiler->prepareFile($source);
        $generated = $compiler->convertFile($source);
        $code = file_get_contents($generated);
        self::assertIsString($code);
        return $code;
    }
}
