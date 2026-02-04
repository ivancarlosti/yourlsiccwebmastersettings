# ICC Meta Redirect plugin for YOURLS
This YOURLS plugin make it possible to change logo, title, page footer, add custom CSS, and customize favicon lines into your YOURLS installation.

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

[ivancarlos]: https://ivancarlos.me
[buymeacoffee]: https://www.buymeacoffee.com/ivancarlos
