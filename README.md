Default WordPress Post Thumbnail
================================

A small WordPress plugin to select an image from your "Media Library", to be displayed as your post thumbnail, when no post thumbnail has been specified.

Usage
-----

This plugin adds two settings to the "Settings > Media" screen on your WordPress installation: one to select which image in your "Media Library" is to be the default image, and another to specify whether or not to run the filter inside the WordPress admin (the filter runs on the frontend by default).

To make use of the plugin requires no code modifications to your theme. The plugin filters when the `the_post_thumbnail()` template tag is called, displaying your default image instead, if no post thumbnail has been specified.