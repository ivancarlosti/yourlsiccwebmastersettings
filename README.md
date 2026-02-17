# ICC Meta Redirect plugin for YOURLS
This YOURLS plugin make it possible to change logo, title, page footer, add custom CSS, and customize favicon lines into your YOURLS installation.

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
[![GitHub Sponsors](https://img.shields.io/github/sponsors/ivancarlosti?label=GitHub%20Sponsors&color=ffc0cb)][sponsor]
[![Buy Me a Coffee](https://img.shields.io/badge/Buy%20Me%20a%20Coffee-ffdd00)][buymeacoffee]
[![Patreon](https://img.shields.io/badge/Patreon-f96854)][patreon]
<!-- endbuttons -->

## Inspiration
* Project inspired by [YOURLS-GWallChangeLogo](https://github.com/gioxx/YOURLS-GWallChangeLogo), [YOURLS-GWallChangeTitle](https://github.com/gioxx/YOURLS-GWallChangeTitle),.

## Instructions
* Download the plugin release
* Create the folder `icc-webmaster-settings` into YOURLS path `/user/plugins` and store `plugin.php` on it
* Activate plugin in `/admin/plugins.php` page of your YOURLS installation
* Access `Webmaster Settings` page
* Manage settings and save
* Go back to another page to see logo, title, footer, custom css and favicon changes

## Requirements
* YOURLS 1.10+

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

| 🩷 Project support |
| :---: |
If you found this project helpful, consider [**buying me a coffee**][buymeacoffee]
|Thanks for your support, it is much appreciated!|

[cc]: https://docs.github.com/en/communities/setting-up-your-project-for-healthy-contributions/adding-a-code-of-conduct-to-your-project
[contributing]: https://docs.github.com/en/articles/setting-guidelines-for-repository-contributors
[security]: https://docs.github.com/en/code-security/getting-started/adding-a-security-policy-to-your-repository
[support]: https://docs.github.com/en/articles/adding-support-resources-to-your-project
[it]: https://docs.github.com/en/communities/using-templates-to-encourage-useful-issues-and-pull-requests/configuring-issue-templates-for-your-repository#configuring-the-template-chooser
[prt]: https://docs.github.com/en/communities/using-templates-to-encourage-useful-issues-and-pull-requests/creating-a-pull-request-template-for-your-repository
[funding]: https://docs.github.com/en/articles/displaying-a-sponsor-button-in-your-repository
[ivancarlos]: https://ivancarlos.me
[buymeacoffee]: https://buymeacoffee.com/ivancarlos
[patreon]: https://www.patreon.com/ivancarlos
[paypal]: https://icc.gg/donate
[sponsor]: https://github.com/sponsors/ivancarlosti
