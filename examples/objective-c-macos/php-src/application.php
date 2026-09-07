<?php

final class HelloApplication
{
    private static int $statusLabel = 0;
    private static int $button = 0;
    private static int $clickCount = 0;

    public static function build(string $platformApi): void
    {
        ui_create_window('TypePHP Native Hello', 640, 380);
        ui_add_label('TypePHP + Objective-C++', 44, 292, 552, 40, 28, true);
        ui_add_label(
            "The interface and application state are implemented in TypePHP.\n" .
            "Objective-C++ only provides thin wrappers around $platformApi.",
            64,
            178,
            512,
            88,
            15,
            false,
        );

        self::$statusLabel = ui_add_label('Waiting for a native UI event.', 64, 130, 512, 24, 13, false);
        self::$button = ui_add_button('Send event to TypePHP', 210, 64, 220, 40);
    }

    public static function handleEvent(int $controlId): void
    {
        if ($controlId !== self::$button) {
            return;
        }

        self::$clickCount++;
        $suffix = self::$clickCount === 1 ? 'time' : 'times';
        ui_set_control_text(
            self::$statusLabel,
            'Button clicked ' . self::$clickCount . " $suffix; handled by TypePHP.",
        );
    }
}
