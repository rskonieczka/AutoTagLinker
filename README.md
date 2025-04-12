# AutoTagLinker Plugin for Pico CMS

## Overview
AutoTagLinker is a plugin for the Pico CMS that automatically links tags within your content. This plugin scans your content for defined tags and converts them into clickable links, making your site more navigable and improving the user experience.

## Features
- Automatically identifies tags in your content
- Converts tags to clickable links
- Configurable tag formats and link destinations
- Supports custom link styling
- Option to exclude specific tags or content sections

## Installation

1. Download the AutoTagLinker plugin files
2. Place the files in your Pico CMS plugins directory (`plugins/AutoTagLinker/`)
3. Configure the plugin in your Pico configuration file

## Configuration

The configuration for AutoTagLinker is stored in the plugin's config.php file located at `plugins/AutoTagLinker/config.php`. The plugin comes with default settings, but you can modify them to suit your needs.

```php
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
```

### Configuration Options

| Option | Description | Default |
|--------|-------------|---------|
| `tags_to_link` | List of words to be automatically linked to tag pages | Sample list of common tech tags |
| `links_per_word` | Maximum number of links per word (-1 for unlimited) | `3` |
| `tag_url_pattern` | URL pattern for links. Use $1 to reference the matched word | `/tags?q=$1` |
| `process_headings` | Whether to process headers (h1, h2, etc.) for tag links | `false` |
| `enable_by_default` | Enable auto-tagging by default for all pages | `true` |
| `case_sensitive` | Whether tag matching should be case-sensitive | `false` |
| `exclude_tags` | Tags to be excluded from auto-linking | `[]` |

## Usage

Once installed and configured, the plugin works automatically. For example, if you have the following content:

```markdown
This is a post about #technology and #programming.
```

The plugin will transform it to:

```html
This is a post about <a href="?tags=technology" class="auto-tag">#technology</a> and <a href="?tags=programming" class="auto-tag">#programming</a>.
```

## Advanced Usage

### Disabling for Specific Pages

If you want to disable auto-tagging for specific pages, you can add the following metadata to the page:

```yaml
---
Title: My Page
AutoTagLinker: false
---
```

### Controlling Tag Processing

You can control which tags are processed by modifying the `tags_to_link` and `exclude_tags` arrays in the configuration.

## Compatibility

- Requires Pico CMS 2.0 or higher
- PHP 7.0 or higher

## License

This plugin is released under the MIT License.

## Support

For issues, feature requests, or contributions, please visit the [GitHub repository](https://github.com/yourusername/AutoTagLinker).
