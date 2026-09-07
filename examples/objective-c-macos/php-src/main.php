<?php

function main(): void
{
    macos_app_init('TypePHP macOS Hello');
    macos_create_window('TypePHP macOS Hello', 640, 380);

    macos_add_label('TypePHP + Objective-C++', 44, 292, 552, 40, 28, true);
    macos_add_label(
        "The application state and event loop are implemented in TypePHP.\n" .
        'Objective-C++ only provides thin wrappers around AppKit.',
        64,
        178,
        512,
        88,
        15,
        false,
    );

    $statusLabel = macos_add_label('Waiting for an AppKit event.', 64, 130, 512, 24, 13, false);
    $button = macos_add_button('Send event to TypePHP', 210, 64, 220, 40);
    $clickCount = 0;

    macos_show_window();
    while (true) {
        $controlId = macos_next_event();
        if ($controlId < 0) {
            break;
        }
        if ($controlId !== $button) {
            continue;
        }

        $clickCount++;
        $suffix = $clickCount === 1 ? 'time' : 'times';
        macos_set_control_text($statusLabel, "Button clicked $clickCount $suffix; handled by TypePHP.");
    }
}
