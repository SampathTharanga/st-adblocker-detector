# ST AdBlocker Detector

**A WordPress plugin by Sampath Tharanga**

Detects ad blocker browser extensions and completely blocks website access
until the visitor disables their ad blocker.

## Features

- Detects all major ad blockers (uBlock Origin, AdBlock Plus, etc.)
- Full-page overlay blocks site access when ad blocker is detected
- Customizable popup title, message, and buttons
- Overlay background color & opacity control
- Blur background effect
- Randomized CSS class names to evade ad-blocker blocklists
- NoScript fallback for JavaScript-disabled browsers
- Hide on mobile option
- Clean, modern admin settings panel
- Lightweight — no jQuery dependency on frontend

## Installation

1. Download the latest release ZIP from the [Releases](../../releases) page
2. Go to **WordPress Admin → Plugins → Add New → Upload Plugin**
3. Upload the ZIP file and click **Activate**
4. Navigate to **ST AdBlock** in the admin menu to configure settings

## Requirements

- WordPress 5.2 or higher
- PHP 7.2 or higher

## Settings

| Option | Description |
|---|---|
| Enable Plugin | Turn detection on or off |
| Popup Title | Heading shown in the popup |
| Message Content | Body text (supports HTML via WP Editor) |
| Popup Width | Width of the popup box (in %) |
| Overlay Color | Background color of the overlay |
| Overlay Opacity | Transparency of the overlay (50–100%) |
| Blur Background | Blurs the site content behind the overlay |
| Refresh Button | Show/hide with custom label |
| Close Button | Show/hide with custom label |
| Hide on Mobile | Skip detection on mobile devices |
| NoScript Fallback | Block users with JavaScript disabled |
| Header Injection | Inject scripts via `wp_body_open` hook |

## License

GPL v2 or later — see [LICENSE](LICENSE)

## Developer

**Sampath Tharanga**
