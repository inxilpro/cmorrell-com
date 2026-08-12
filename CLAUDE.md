# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

Personal website for Chris Morrell (cmorrell.com). Laravel 12 app that serves mostly static content — blog posts written as either Blade templates or Markdown files with YAML front matter.

## Commands

- **Dev server:** `composer dev` (runs Laravel server, queue worker, Pail log viewer, and Vite concurrently)
- **Tests:** `php artisan test` or `./vendor/bin/phpunit`
- **Single test:** `php artisan test --filter=TestName`
- **Code formatting:** `composer fix-style` (php-cs-fixer)
- **Build frontend:** `npm run build`
- **Download stats:** `php artisan downloads:stats` (fetches Packagist/NPM download counts)

## Architecture

### Content System

Routes are auto-registered from the filesystem in `routes/web.php` with a priority system:
1. **Markdown pages** (`resources/views/markdown/pages/*.md`) — lowest priority, rendered by `MarkdownController` through `MarkdownConverter`
2. **Blade pages** (`resources/views/pages/*.blade.php`) — higher priority, served directly as views
3. **Manual routes** — highest priority (e.g., the home page)

Each markdown page also gets a `.md` raw endpoint via `RawMarkdownController`.

### Markdown Pipeline

`App\Support\MarkdownConverter` extends League CommonMark with:
- GitHub-flavored markdown
- Front matter (YAML) for page metadata (`title`, `og`)
- Torchlight for syntax highlighting
- Default Tailwind CSS classes applied per element type (headings, paragraphs, code blocks, etc.)

The `<x-markdown>` Blade component (`App\View\Components\Markdown`) can render markdown inline or from a file.

### Key Conventions

- Uses tabs for PHP indentation
- Tailwind CSS v4 with a custom `font-slant` utility (House Slant font)
- Custom `FinderCollection` wraps Symfony Finder with Laravel's LazyCollection interface
- `app/helpers.php` is autoloaded (contains `md_path()` helper)
- MySQL database
- RSS feed via `spatie/laravel-feed` configured in `config/feed.php`, items defined in `App\Http\Feed`
- `App\Http\Downloads` fetches package download stats from Packagist and NPM APIs, cached with `Cache::flexible`
