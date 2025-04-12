<?php

/**
 * AutoTagLinker configuration file
 *
 * This file contains all settings for the AutoTagLinker plugin.
 * No configuration in config.yml is needed.
 *
 * @author Rafał Skonieczka
 */
return [
    // List of words to be automatically linked to tag pages
    'tags_to_link' => [
        'AI', 'Ansible', 'Artisan', 'Bash', 'Cheatsheet', 'DNS', 'DevOps',
        'Docker', 'Git', 'Google Ads', 'Kubernetes', 'LAMP', 'Laravel',
        'Linux', 'Malware', 'Marketing', 'MySQL', 'PHP', 'Perl',
        'Plugin', 'Python', 'SEO', 'SSL',
        'Sport', 'Tailwind', 'WordPress', 'htaccess'
    ],
    // Maximum number of links per word (-1 for unlimited)
    'links_per_word' => 3,
    // URL pattern for links. Use $1 to reference the matched word
    'tag_url_pattern' => '/tags?q=$1',
    // Whether to process headers (h1, h2, etc.) for tag links
    'process_headings' => false,
    // Enable auto-tagging by default for all pages
    'enable_by_default' => true,
    // Whether tag matching should be case-sensitive
    'case_sensitive' => false,
    // Tags to be excluded from auto-linking
    'exclude_tags' => [
        // Add any tags you want to exclude here
        // 'PHP',
        // 'MySQL'
    ]
];
