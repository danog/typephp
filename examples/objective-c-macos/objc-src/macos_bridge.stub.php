<?php

/** Thin wrappers around the macOS AppKit API. */
function macos_app_init(string $applicationName): void {}

function macos_create_window(string $title, int $width, int $height): void {}

function macos_add_label(
    string $text,
    int $x,
    int $y,
    int $width,
    int $height,
    int $fontSize,
    bool $bold
): int {}

function macos_add_button(string $title, int $x, int $y, int $width, int $height): int {}

function macos_set_control_text(int $controlId, string $text): void {}

function macos_show_window(): void {}

/** Return the activated control ID, or -1 after the window is closed. */
function macos_next_event(): int {}
