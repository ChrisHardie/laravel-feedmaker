<?php
// config for ChrisHardie/Feedmaker
return [
    // How often to update feeds from sources, in minutes
    'default_update_frequency' => 60,

    // Feed index web route
    'url' => '/',

    // Threshold of repeated failures for treating not crawlable exceptions
    // as warnings instead of debug messages
    'feed_exception_min_for_warnings' => 4,
];
