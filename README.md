# ST AdBlocker Detector

**A WordPress plugin by Sampath Tharanga**

Detects ad blocker browser extensions and completely blocks website access
until the visitor disables their ad blocker.

---

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

---

## Installation

1. Download the latest release ZIP from the [Releases](../../releases) page
2. Go to **WordPress Admin → Plugins → Add New → Upload Plugin**
3. Upload the ZIP file and click **Activate**
4. Navigate to **ST AdBlock** in the admin menu to configure settings

---

## Requirements

- WordPress 5.2 or higher
- PHP 7.2 or higher

---

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

---


## Screenshots

![Frontend Overlay]([screenshots/screenshot-1.png](https://github.com/user-attachments/assets/f49aafb5-a6bc-4d33-a9ab-a17d421489cc))


## License

GPL v2 or later — see [LICENSE](LICENSE)

---

## Developer

**Sampath Tharanga**

---

## References & Inspiration

This plugin was built with reference to the architecture and detection
techniques used in the following open-source WordPress plugin:

| | Detail |
|---|---|
| **Plugin Name** | CHP Ads Block Detector |
| **Author** | Suresh Chand |
| **Author URI** | https://sureshchand.com.np |
| **Plugin URI** | https://chpadblock.com |
| **License** | GPL v2 or later |

### Concepts referenced from CHP Ads Block Detector:

- **Google Ads script probe** — Loading `pagead2.googlesyndication.com/pagead/js/adsbygoogle.js`
  and checking if `window.adsbygoogle` is defined, as a reliable method to
  detect network-level ad blockers.
- **CSS class-based detection** — Injecting a hidden decoy `<div>` with
  common ad-related class names (`.adsbox`, `.adsbygoogle`, `.Ad-Container`,
  etc.) and checking if it gets hidden by the ad blocker.
- **FairAdblock detection** — Checking for the presence of a `#stndz-style`
  element injected by the FairAdblock extension.
- **Randomized class names** — Generating obfuscated, site-specific CSS class
  names to prevent ad blockers from targeting the overlay by class name.
- **NoScript fallback** — Wrapping the block overlay in a `<noscript>` tag
  so browsers with JavaScript disabled are also blocked.
- **Minified JS output** — Serving the frontend detection script in a
  minified/obfuscated form to reduce fingerprinting.
- **WordPress settings architecture** — The pattern of storing settings as a
  JSON-encoded string in `wp_options`, using `wp_parse_args()` to merge with
  defaults, and saving via AJAX with nonce verification.

> **Note:** This plugin was independently developed by Sampath Tharanga.
> The CHP Ads Block Detector plugin is credited purely as a reference and
> source of inspiration. No proprietary or closed-source code from CHP was
> copied or redistributed. Both plugins are licensed under GPL v2.
