# EditorialFlow

A custom WordPress plugin for managing editorial status, reviewer information, notes, and status history from the WordPress post editor.

## Requirements

- WordPress 6.4 or later
- PHP 7.4 or later
- MySQL

## Features

- Editorial status, reviewer name, and editorial notes per post
- Editorial status column in the Posts list
- Status history tracked in a custom database table (append-only, with deletable records)
- Configurable default editorial status
- Custom roles and capabilities
- REST API for editorial status updates
- Admin-side status updates without page reload

## Editorial Statuses

Draft → In Review → Approved → Needs Changes

## Installation

1. Copy the `editorialflow` folder into `wp-content/plugins/`.
2. Activate **EditorialFlow** from **Plugins → Installed Plugins**.
3. Set the default status from the **EditorialFlow Settings** page.
4. Use the **Editorial Status** meta box on any post.

## REST API

`POST /wp-json/editorialflow/v1/status/{post_id}`

Validates the post, user permissions, and status value before updating.

## Security

Nonce verification, capability checks, input sanitization, output escaping, REST permission callbacks, and prepared `$wpdb` queries throughout.

## Version

Current release: 1.0.0