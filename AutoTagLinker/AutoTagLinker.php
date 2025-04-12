<?php

/**
 * Auto Tag Linker Plugin for PicoCMS
 *
 * This plugin automatically links specified words in content to their corresponding tag pages.
 *
 * @author Rafał Skonieczka
 * @license MIT
 * @version 1.2
 */
class AutoTagLinker extends AbstractPicoPlugin
{
    /**
     * List of words to be automatically linked to tag pages
     * @var array
     */
    protected $tagsToLink = [];

    /**
     * Maximum number of links per word
     * @var int
     */
    protected $linksPerWord = 1;

    /**
     * Flag to avoid processing content in code blocks
     * @var bool
     */
    protected $inCodeBlock = false;

    /**
     * Custom configuration options
     * @var array
     */
    protected $pluginConfig = [];

    /**
     * Enable/disable the plugin per page
     * @var bool
     */
    protected $enabled = true;

    /**
     * Current page metadata
     * @var array
     */
    protected $currentMeta = [];

    /**
     * Path to the plugin configuration file
     * @var string
     */
    protected $configFilePath = '';

    /**
     * Construct the plugin
     *
     * @param Pico $pico instance of Pico
     */
    public function __construct(Pico $pico)
    {
        parent::__construct($pico);

        // Set the path to the configuration file
        $this->configFilePath = __DIR__ . '/config.php';
    }

    /**
     * Load plugin config from config.yml, separate config file, and page meta
     *
     * @param array &$settings array of config settings
     */
    public function onConfigLoaded(&$settings)
    {
        // Default configuration - tylko awaryjne ustawienia domyślne
        $this->pluginConfig = [
            'tags_to_link' => [
                'MySQL', 'PHP', 'JavaScript', 'HTML', 'CSS', 'Linux',
                'Apache', 'Nginx', 'Docker', 'Git', 'WordPress'
            ],
            'links_per_word' => 1,
            'tag_url_pattern' => '/tags?q=$1',
            'process_headings' => false,
            'enable_by_default' => true,
            'case_sensitive' => false,
            'exclude_tags' => []
        ];

        // Pomiń ładowanie ustawień z config.yml i przejdź bezpośrednio do config.php
        $this->loadPluginConfigFile();
    }

    /**
     * Load global configuration from config.yml - metoda pozostawiona dla kompatybilności
     *
     * @param array &$settings array of config settings
     */
    protected function loadGlobalConfig(&$settings)
    {
        // Metoda celowo pozostawiona pusta - konfiguracja tylko z pliku config.php
    }

    /**
     * Load plugin configuration from separate config file
     */
    protected function loadPluginConfigFile()
    {
        if (file_exists($this->configFilePath)) {
            // Dodaj debug
            error_log('AutoTagLinker: Loading config from ' . $this->configFilePath);

            $customConfig = include $this->configFilePath;

            if (is_array($customConfig)) {
                // Dodaj debug
                error_log('AutoTagLinker: Config loaded successfully: ' . json_encode($customConfig));

                // Zastąp całą konfigurację zamiast łączyć
                $this->pluginConfig = $customConfig;
            } else {
                // Dodaj debug
                error_log('AutoTagLinker: Config is not an array');
            }
        } else {
            // Dodaj debug
            error_log('AutoTagLinker: Config file not found at ' . $this->configFilePath);
        }

        // Apply configuration to instance variables
        $this->tagsToLink = $this->pluginConfig['tags_to_link'];
        $this->linksPerWord = $this->pluginConfig['links_per_word'];
        $this->enabled = $this->pluginConfig['enable_by_default'];

        // Debug ustawień
        error_log('AutoTagLinker: Final settings - '
            . 'tagsToLink: ' . implode(',', $this->tagsToLink)
            . ', linksPerWord: ' . $this->linksPerWord
            . ', enabled: ' . ($this->enabled ? 'true' : 'false'));
    }

    /**
     * Check for page-specific configuration in meta tags
     *
     * @param array &$meta page meta
     */
    public function onMetaParsed(&$meta)
    {
        $this->currentMeta = $meta;

        // Check if auto-tagging is explicitly enabled/disabled for this page
        if (isset($meta['AutoTagLinker'])) {
            $this->enabled = (bool) $meta['AutoTagLinker'];
        }

        // Check for page-specific tags to link
        if (isset($meta['AutoTagWords']) && is_array($meta['AutoTagWords'])) {
            $this->tagsToLink = $meta['AutoTagWords'];
        }

        // Check for page-specific links per word
        if (isset($meta['AutoTagLinksPerWord']) && is_numeric($meta['AutoTagLinksPerWord'])) {
            $this->linksPerWord = intval($meta['AutoTagLinksPerWord']);
        }

        // Check for exclude tags specific to this page
        if (isset($meta['AutoTagExclude']) && is_array($meta['AutoTagExclude'])) {
            $this->pluginConfig['exclude_tags'] = array_merge(
                $this->pluginConfig['exclude_tags'],
                $meta['AutoTagExclude']
            );
        }
    }

    /**
     * Process content after it has been parsed
     *
     * @param string &$content parsed content
     */
    public function onContentParsed(&$content)
    {
        // Skip processing if plugin is disabled for this page
        if (!$this->enabled || empty($content) || empty($this->tagsToLink)) {
            return;
        }

        // Skip processing for certain page types
        if (isset($this->currentMeta['template']) && in_array($this->currentMeta['template'], ['tags', 'feed'])) {
            return;
        }

        // Split content by code blocks to avoid processing words in code
        $parts = preg_split('/(<pre><code>|<\/code><\/pre>)/', $content, -1, PREG_SPLIT_DELIM_CAPTURE);

        for ($i = 0; $i < count($parts); $i++) {
            // Skip code blocks and their delimiters
            if ($parts[$i] === '<pre><code>') {
                $this->inCodeBlock = true;
                continue;
            } elseif ($parts[$i] === '</code></pre>') {
                $this->inCodeBlock = false;
                continue;
            }

            // Only process content outside of code blocks
            if (!$this->inCodeBlock) {
                // Skip headings if configured to do so
                if (!$this->pluginConfig['process_headings']) {
                    $parts[$i] = $this->processContentExcludingHeadings($parts[$i]);
                } else {
                    $parts[$i] = $this->processContent($parts[$i]);
                }
            }
        }

        $content = implode('', $parts);
    }

    /**
     * Process content excluding headings
     *
     * @param string $content Content to process
     * @return string Processed content
     */
    protected function processContentExcludingHeadings($content)
    {
        // Split content by headers
        $parts = preg_split('/(<h[1-6].*?>.*?<\/h[1-6]>)/is', $content, -1, PREG_SPLIT_DELIM_CAPTURE);

        for ($i = 0; $i < count($parts); $i++) {
            // Process only non-heading parts
            if (!preg_match('/^<h[1-6].*?>.*?<\/h[1-6]>$/is', $parts[$i])) {
                $parts[$i] = $this->processContent($parts[$i]);
            }
        }

        return implode('', $parts);
    }

    /**
     * Process content to link tags
     *
     * @param string $content Content to process
     * @return string Processed content
     */
    protected function processContent($content)
    {
        // Skip if the content is empty
        if (empty($content)) {
            return $content;
        }

        // Simple regex replacement for each word in tagsToLink
        foreach ($this->tagsToLink as $tag) {
            // Skip excluded tags
            if (in_array($tag, $this->pluginConfig['exclude_tags'])) {
                continue;
            }

            // Pattern to match the word with word boundaries and not inside HTML tags or existing links
            $pattern = '/\b(' . preg_quote($tag, '/') . ')\b(?![^<]*>|[^<>]*<\/a>)/';

            // Make pattern case-insensitive if configured
            if (!$this->pluginConfig['case_sensitive']) {
                $pattern .= 'i';
            }

            // Replace with linked version using the configured number of replacements
            // If linksPerWord is -1, replace all occurrences (no limit)
            $limit = ($this->linksPerWord < 0) ? -1 : $this->linksPerWord;

            // Use the configured URL pattern but replace $1 with the original tag format from config
            $url = str_replace('$1', $tag, $this->pluginConfig['tag_url_pattern']);

            // Closure function to maintain the original case of the matched word in link text
            // but use the configured tag format in the URL
            $content = preg_replace_callback($pattern, function ($matches) use ($url) {
                return '<a href="' . $url . '">' . $matches[1] . '</a>';
            }, $content, $limit);
        }

        return $content;
    }
}
