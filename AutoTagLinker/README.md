# AutoTagLinker Plugin dla PicoCMS

Plugin, który automatycznie zamienia określone słowa w treści na linki do odpowiednich stron tagów.

## Autor

Rafał Skonieczka

## Wersja

1.2

## Licencja

MIT

## Opis

AutoTagLinker to wtyczka dla PicoCMS, która automatycznie wykrywa określone słowa kluczowe w treści artykułów i zamienia je na linki prowadzące do stron tagów. Wtyczka jest szczególnie przydatna do:

- Zwiększenia wewnętrznej struktury linkowania w Twojej witrynie
- Poprawy SEO poprzez tworzenie powiązań tematycznych
- Ułatwienia nawigacji użytkownikom 

Przykładowo, gdy w treści pojawi się słowo "MySQL", wtyczka może automatycznie zamienić je na link do `/tags?q=MySQL`.

## Instalacja

1. Skopiuj plik `AutoTagLinker.php` do katalogu `plugins` w Twojej instalacji PicoCMS
2. Stwórz katalog `plugins/AutoTagLinker` jeśli jeszcze nie istnieje
3. (Opcjonalnie) Skopiuj plik `config.php` do katalogu `plugins/AutoTagLinker`
4. (Opcjonalnie) Dodaj konfigurację wtyczki do pliku `config.yml`

## Konfiguracja

AutoTagLinker można konfigurować na trzech poziomach, z malejącym priorytetem:

1. **Konfiguracja per-artykuł** - poprzez meta tagi w nagłówku artykułu (najwyższy priorytet)
2. **Plik konfiguracyjny wtyczki** - `plugins/AutoTagLinker/config.php`
3. **Globalna konfiguracja** - w pliku `config.yml` (najniższy priorytet)

### Dostępne opcje konfiguracyjne

| Opcja | Typ | Domyślna wartość | Opis |
|------|------|---------|-------------|
| tags_to_link | array | ['MySQL', 'PHP', ... ] | Lista słów do automatycznego linkowania |
| links_per_word | integer | 1 | Maksymalna liczba linków do wygenerowania dla każdego słowa (-1 = bez limitu) |
| tag_url_pattern | string | '/tags?q=$1' | Wzorzec URL dla linków. $1 to miejsce na wykryte słowo |
| process_headings | boolean | false | Czy przetwarzać nagłówki (h1, h2, itp.) |
| enable_by_default | boolean | true | Domyślnie włącz/wyłącz wtyczkę dla wszystkich stron |
| case_sensitive | boolean | false | Czy rozróżniać wielkość liter przy dopasowywaniu słów |
| exclude_tags | array | [] | Lista tagów, które nie powinny być automatycznie linkowane |

### 1. Konfiguracja w pliku config.yml

```yaml
##
# Auto Tag Linker Plugin
#
auto_tag_words:
  - MySQL
  - PHP
  - JavaScript
  - HTML
  - CSS
  # ... więcej słów
auto_tag_links_per_word: 3  # 3 linki per słowo, -1 = wszystkie wystąpienia
auto_tag_url_pattern: "/tags?q=$1"  # Format URL dla linków
auto_tag_process_headings: false  # Nie przetwarzaj nagłówków
auto_tag_enable_by_default: true  # Domyślnie włączone dla wszystkich stron
auto_tag_case_sensitive: false  # Nie rozróżniaj wielkości liter
auto_tag_exclude:  # Wykluczenia globalne
  - PHP  # Nie linkuj słowa PHP
```

### 2. Konfiguracja w plugins/AutoTagLinker/config.php

```php
<?php
return [
    // Lista słów do automatycznego linkowania
    'tags_to_link' => [
        'MySQL', 'PHP', 'JavaScript', 'HTML', 'CSS',
        // ... więcej słów
    ],
    
    // Maksymalna liczba linków per słowo
    'links_per_word' => 3,
    
    // Wzorzec URL dla linków
    'tag_url_pattern' => '/tags?q=$1',
    
    // Pozostałe opcje
    'process_headings' => false,
    'enable_by_default' => true,
    'case_sensitive' => false,
    'exclude_tags' => [
        // Lista wykluczonych tagów
    ]
];
?>
```

### 3. Konfiguracja na poziomie artykułu

W nagłówku YAML każdego artykułu Markdown można dodać następujące meta tagi:

```yaml
---
Title: Przykładowy artykuł
Description: Opis artykułu
Tags: MySQL,PHP
template: post
Date: 2025.04.12
# Konfiguracja AutoTagLinker dla tego artykułu
AutoTagLinker: true  # Włącz/wyłącz wtyczkę dla tego artykułu
AutoTagWords: [MySQL, PHP]  # Określ własną listę tagów dla tego artykułu
AutoTagLinksPerWord: 2  # Liczba linków per słowo dla tego artykułu
AutoTagExclude: [Laravel]  # Tagi do wykluczenia dla tego artykułu
---
```

## Funkcje

### Automatyczne linkowanie tagów

Wtyczka automatycznie wykrywa określone słowa w treści i zamienia je na linki do odpowiednich stron tagów.

### Inteligentne pomijanie

Wtyczka inteligentnie pomija:
- Tekst w blokach kodu
- Tekst już znajdujący się w linkach
- Nagłówki (opcjonalnie)
- Tagi wykluczane

### Limity linkowania

Można skonfigurować maksymalną liczbę linków wygenerowanych dla każdego słowa:
- `1` (domyślna) - linkuje tylko pierwsze wystąpienie każdego słowa
- `3` - linkuje maksymalnie 3 wystąpienia każdego słowa
- `-1` - linkuje wszystkie wystąpienia każdego słowa

### Włączanie/wyłączanie per-strona

Wtyczkę można włączyć lub wyłączyć dla określonych stron za pomocą meta tagu `AutoTagLinker: true/false`.

## Przykłady

### Przykład 1: Podstawowe zastosowanie

Tekst źródłowy:
```
Artykuł o MySQL i jego funkcjach. MySQL to popularny system zarządzania bazami danych.
```

Tekst po przetworzeniu (przy ustawieniu `links_per_word: 2`):
```
Artykuł o <a href="/tags?q=MySQL">MySQL</a> i jego funkcjach. <a href="/tags?q=MySQL">MySQL</a> to popularny system zarządzania bazami danych.
```

### Przykład 2: Własny wzorzec URL

Konfiguracja:
```yaml
auto_tag_url_pattern: "/category/$1.html"
```

Tekst źródłowy:
```
JavaScript jest popularnym językiem programowania.
```

Tekst po przetworzeniu:
```
<a href="/category/JavaScript.html">JavaScript</a> jest popularnym językiem programowania.
```

## Rozwiązywanie problemów

### Wzorzec URL nie działa

Upewnij się, że `$1` w `tag_url_pattern` jest używane jako miejsce wstawienia wykrytego słowa. Np. `/tags?q=$1`.

### Za dużo lub za mało linków

Dostosuj ustawienie `links_per_word`:
- Dla pojedynczego linku na słowo: `links_per_word: 1`
- Dla określonej liczby linków: `links_per_word: 3`
- Dla wszystkich wystąpień: `links_per_word: -1`

### Linki pojawiają się w blokach kodu

To się nie powinno zdarzyć, ponieważ wtyczka automatycznie pomija bloki kodu. Jeśli jednak występuje ten problem, sprawdź, czy bloki kodu są poprawnie oznaczone tagami `<pre><code>` i `</code></pre>`.

### Zbyt wiele tagów jest linkowane

Możesz:
1. Ograniczyć listę `auto_tag_words` tylko do najważniejszych tagów
2. Użyć `auto_tag_exclude` do wykluczenia niektórych tagów
3. Wyłączyć wtyczkę dla konkretnej strony z `AutoTagLinker: false`

## Uwagi końcowe

AutoTagLinker to potężne narzędzie do automatycznego wzbogacania treści Twojej witryny o wewnętrzne linki. Użyj go z rozwagą, aby zachować równowagę między użytecznością a nadmiernym linkowaniem. Nadmierna liczba linków może przytłoczyć czytelników i negatywnie wpłynąć na SEO.
