# Mentions plugin

![Elgg 3.0](https://img.shields.io/badge/Elgg-3.0-green.svg)
[![Latest Stable Version](https://poser.pugx.org/Elgg/mentions/v/stable.svg)](https://packagist.org/packages/Elgg/mentions)
[![License](https://poser.pugx.org/Elgg/mentions/license.svg)](https://packagist.org/packages/Elgg/mentions)

 * Replaces @username with links to the user's profile
 * Sends notifications to users mentioned in posts

## Notes

### Supported content types

To add support for custom object types in outgoing notifications,
add a corresponding language key pair to your language file:

``mentions:notification_types:object:<object_subtype>``

### Object fields scanned for mentions

Use `'get_fields','mentions'` hook to expand the scope of scanned fields
beyond object `title` and `description`. The hook receives `entity` and expects
and array of fields in return.

### Replacement of mentions with anchors

To add a view which should be scanned for @mentions and replaced with an anchor,
use `'get_views', 'mentions'` hook.
