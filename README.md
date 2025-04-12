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

Add the following configuration to your `config.yml` file:

```yaml
AutoTagLinker:
  enabled: true
  tagPrefix: '#'  # Character used to identify tags (default: '#')
  tagClass: 'auto-tag'  # CSS class applied to tag links
  linkFormat: '?tags=%tag%'  # URL format for tag links (%tag% is replaced with the tag name)
  caseSensitive: false  # Whether tag matching is case sensitive
  excludedTags: []  # List of tags to exclude from auto-linking
  excludedSections:  # Content sections to exclude from processing
    - 'code'
    - 'pre'
```

### Configuration Options

| Option | Description | Default |
|--------|-------------|---------|
| `enabled` | Enable or disable the plugin | `true` |
| `tagPrefix` | Character used to identify tags | `'#'` |
| `tagClass` | CSS class applied to tag links | `'auto-tag'` |
| `linkFormat` | URL format for tag links | `'?tags=%tag%'` |
| `caseSensitive` | Whether tag matching is case sensitive | `false` |
| `excludedTags` | List of tags to exclude from auto-linking | `[]` |
| `excludedSections` | Content sections to exclude from processing | `['code', 'pre']` |

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

### Custom Tag Formats

You can customize how tags are identified and processed by adjusting the configuration:

```yaml
AutoTagLinker:
  tagRegex: '/\#([a-zA-Z0-9_\-]+)/'  # Custom regex for identifying tags
```

### Tag Blacklist

Prevent specific tags from being auto-linked:

```yaml
AutoTagLinker:
  excludedTags:
    - 'nolink'
    - 'private'
```

### Styling Tag Links

You can style the auto-generated tag links using CSS:

```css
.auto-tag {
  color: #3498db;
  text-decoration: none;
  font-weight: bold;
}

.auto-tag:hover {
  text-decoration: underline;
}
```

## Compatibility

- Requires Pico CMS 2.0 or higher
- PHP 7.0 or higher

## License

This plugin is released under the MIT License.

## Support

For issues, feature requests, or contributions, please visit the [GitHub repository](https://github.com/yourusername/AutoTagLinker).
