# ICC Webmaster Settings plugin for YOURLS
This YOURLS plugin make it possible to change several settings like logo, title, page footer, add custom CSS, customize favicon lines, add reCAPTCHA, 2FA and per-domain/page redirects into your YOURLS installation.

<!-- buttons -->
[![Stars](https://img.shields.io/github/stars/ivancarlosti/yourlsiccwebmastersettings?label=⭐%20Stars&color=gold&style=flat)](https://github.com/ivancarlosti/yourlsiccwebmastersettings/stargazers)
[![Watchers](https://img.shields.io/github/watchers/ivancarlosti/yourlsiccwebmastersettings?label=Watchers&style=flat&color=red)](https://github.com/sponsors/ivancarlosti)
[![Forks](https://img.shields.io/github/forks/ivancarlosti/yourlsiccwebmastersettings?label=Forks&style=flat&color=ff69b4)](https://github.com/sponsors/ivancarlosti)
[![Downloads](https://img.shields.io/github/downloads/ivancarlosti/yourlsiccwebmastersettings/total?label=Downloads&color=success)](https://github.com/ivancarlosti/yourlsiccwebmastersettings/releases)
[![GitHub commit activity](https://img.shields.io/github/commit-activity/m/ivancarlosti/yourlsiccwebmastersettings?label=Activity)](https://github.com/ivancarlosti/yourlsiccwebmastersettings/pulse)
[![GitHub Issues](https://img.shields.io/github/issues/ivancarlosti/yourlsiccwebmastersettings?label=Issues&color=orange)](https://github.com/ivancarlosti/yourlsiccwebmastersettings/issues)  
[![License](https://img.shields.io/github/license/ivancarlosti/yourlsiccwebmastersettings?label=License)](LICENSE)
[![GitHub last commit](https://img.shields.io/github/last-commit/ivancarlosti/yourlsiccwebmastersettings?label=Last%20Commit)](https://github.com/ivancarlosti/yourlsiccwebmastersettings/commits)
[![Security](https://img.shields.io/badge/Security-View%20Here-purple)](https://github.com/ivancarlosti/yourlsiccwebmastersettings/security)
[![Code of Conduct](https://img.shields.io/badge/Code%20of%20Conduct-2.1-4baaaa)](https://github.com/ivancarlosti/yourlsiccwebmastersettings?tab=coc-ov-file)
<!-- endbuttons -->

## Inspiration
* Project inspired by [YOURLS-GWallChangeLogo](https://github.com/gioxx/YOURLS-GWallChangeLogo), [YOURLS-GWallChangeTitle](https://github.com/gioxx/YOURLS-GWallChangeTitle),.

## Instructions
* Download the plugin release
* Create the folder `icc-webmaster-settings` into YOURLS path `/user/plugins` and store `plugin.php`, `authenticator.php` on it
* Activate plugin in `/admin/plugins.php` page of your YOURLS installation
* Access `Webmaster Settings` page
* Manage settings and save

## Domain & Page Redirects

A single YOURLS installation can serve several domains. This feature redirects the root page of each domain (and any page YOURLS cannot resolve as a short URL) to a URL of your choice, with a selectable HTTP code.

Enable it in **Webmaster Settings → Domain & Page Redirect Settings**, choose the default HTTP code (301 or 302) and add one rule per line:

```text
# domain[/path]        target-url                    [301|302]
example-a.com           https://example-a.com/
example-b.net           https://example-b.com/        301
example-a.com/promo     https://example-a.com/promo
example-b.net/blog/*    https://blog.example.com/
*                       https://main.example.com/
```

* `example-a.com` (no path, or `/`) redirects only the domain root.
* `example-a.com/promo` redirects that exact path; a trailing `*` turns it into a prefix match, so `example-b.net/blog/*` also matches `/blog/` and `/blog/post-1`.
* `*` as the domain matches every domain, so `* https://main.example.com/` redirects the root of all domains that have no more specific rule above it.
* The optional third column (`301` or `302`) overrides the default HTTP code for that rule.
* Lines starting with `#` are comments and blank lines are ignored. Rules are evaluated top to bottom and the first match wins: put specific domains before the `*` catch-all.
* `example.com` and `www.example.com` are treated as the same host.
* Redirects are only evaluated for requests YOURLS cannot resolve, so existing short URLs, admin pages and other YOURLS pages are never affected.
* A rule pointing to the same scheme, host and path it was requested from is skipped to prevent redirect loops (HTTP to HTTPS upgrades of the same page still work).
* Redirects issued here keep their configured HTTP code even when **Force 302 Redirect** is enabled.
* A request to the domain root (`/`) is handed to the plugin by a small `index.php` that the plugin manages in the YOURLS root: see *Redirecting the domain root* below.

### Redirecting the domain root (`/`)

YOURLS ships no `index.php` in its root, and both the recommended `.htaccess` rules and the nginx configuration documented by YOURLS skip the bare `/` request because it maps to an existing directory. A request to `https://your.domain/` is therefore answered by the web server itself — usually with a **403 Forbidden** — and never reaches YOURLS, so no rule can be applied to it.

To solve this, the plugin manages a small `index.php` in the YOURLS root that hands `/` over to it:

* It is created when you save the settings with the feature enabled, and when the plugin is activated while the feature is already enabled.
* It is never written over an existing `index.php`: when the file exists and was not generated by this plugin, it is left untouched and a warning is shown on the settings page.
* It is removed when you disable the feature or deactivate the plugin, and it carries an `ICC Webmaster Settings` marker, so a foreign `index.php` is never deleted either.
* If the plugin is not active while the file is still there, the file answers with a 404 instead of entering a redirect loop.
* Deleting the file is harmless: it is recreated the next time you save the settings.

When no rule matches the domain root, the plugin answers with a 404 instead of redirecting `/` to `YOURLS_SITE` (which would be an endless loop).

If the plugin cannot write to the YOURLS root (read-only file system, restrictive permissions) the settings page tells you, and you can create the file yourself with this content:

```php
<?php
// index.php in the YOURLS root
require_once __DIR__ . '/includes/load-yourls.php';

if (function_exists('icc_domain_redirect_handle_root')) {
    icc_domain_redirect_handle_root();
}

yourls_die(yourls__('Not found'), yourls__('Page not found'), 404);
```

A plain web server redirect for the root also works, for example `Redirect 301 / https://example.com/` in Apache or `location = / { return 301 https://example.com/; }` in nginx, but it applies to every domain served by the installation.

## Requirements
* YOURLS 1.10+

## Screenshots

<img width="2036" height="1978" alt="1" src="https://github.com/user-attachments/assets/1c7b614a-abdf-4578-9919-2e0c93b4031e" />

<img width="2036" height="1234" alt="2" src="https://github.com/user-attachments/assets/ee0948ad-a686-4ca8-baec-ce13125de7b2" />

## CSS snippet suggestion

```
#footer p a {
    background: none !important;
    padding-left: 0px !important;
}

input {
    font-family: Verdana, Arial;
    font-size: 10px;
    color: #595441;
    background-color: #FFFFFF;
    border: 1px solid #88c0eb;
    margin: 1px;
}

#error-message {
    padding: 10px;
}
```

<!-- footer -->
---

## 🧑‍💻 Consulting and technical support
* For personal support and queries, please submit a new issue to have it addressed.
* For commercial related questions, please [**contact me**][ivancarlos] for consulting costs.

[cc]: https://docs.github.com/en/communities/setting-up-your-project-for-healthy-contributions/adding-a-code-of-conduct-to-your-project
[contributing]: https://docs.github.com/en/articles/setting-guidelines-for-repository-contributors
[security]: https://docs.github.com/en/code-security/getting-started/adding-a-security-policy-to-your-repository
[support]: https://docs.github.com/en/articles/adding-support-resources-to-your-project
[it]: https://docs.github.com/en/communities/using-templates-to-encourage-useful-issues-and-pull-requests/configuring-issue-templates-for-your-repository#configuring-the-template-chooser
[prt]: https://docs.github.com/en/communities/using-templates-to-encourage-useful-issues-and-pull-requests/creating-a-pull-request-template-for-your-repository
[funding]: https://docs.github.com/en/articles/displaying-a-sponsor-button-in-your-repository
[ivancarlos]: https://ivancarlos.me
