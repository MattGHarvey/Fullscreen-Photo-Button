<?php
/*
Plugin Name: Fullscreen Photo Button
Description: Adds a fullscreen icon button above the first image in a post to display it in fullscreen.
Version: 1.0
Author: Matt Harvey - [robotSprocket.com]
*/

// Enqueue JavaScript and CSS for fullscreen functionality
add_action('wp_enqueue_scripts', function() {
    wp_add_inline_script('jquery', <<<JS
    function viewFullScreen() {
        const elem = document.querySelector(".fullscreen-photo-target");
        if (elem.requestFullscreen) {
            elem.requestFullscreen();
        } else if (elem.webkitRequestFullscreen) {
            elem.webkitRequestFullscreen();
        } else if (elem.msRequestFullscreen) {
            elem.msRequestFullscreen();
        }
    }
JS
    );

    wp_add_inline_style('wp-block-library', <<<CSS
    .fullscreen-photo-wrapper {
        display: flex;
        flex-direction: column;
        align-items: flex-end;
        margin-bottom: 1rem;
    }
    .fullscreen-icon-button {
        background: rgba(0, 0, 0, 0.6);
        border: none;
        color: white;
        padding: 0.4rem 0.6rem;
        border-radius: 4px;
        cursor: pointer;
        font-size: 1.2rem;
        margin-bottom: 0.5rem;
    }
    .fullscreen-icon-button:hover {
        background: rgba(0, 0, 0, 0.8);
    }
CSS
    );
});

// Filter post content to inject icon button above first image
add_filter('the_content', function($content) {
    if (!is_singular() || !in_the_loop() || !is_main_query()) return $content;

    // Find first image in the content
    $pattern = '/<img[^>]+>/i';
    if (preg_match($pattern, $content, $matches)) {
        $img = $matches[0];
        // Add class to target image
        $img_with_class = preg_replace('/<img/', '<img class="fullscreen-photo-target"', $img);

        // Wrap image and icon button in a container
        $wrapper = '<div class="fullscreen-photo-wrapper">
            <button class="fullscreen-icon-button" onclick="viewFullScreen()" title="View Full Screen">⤢</button>
            ' . $img_with_class . '
        </div>';

        // Replace the original image with the wrapped version
        $content = preg_replace($pattern, $wrapper, $content, 1);
    }

    return $content;
});