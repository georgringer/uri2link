# TYPO3 Extension `uri2link`

[![Donate](https://img.shields.io/badge/Donate-PayPal-green.svg)](https://www.paypal.me/GeorgRinger/5)
[![Latest Stable Version](https://poser.pugx.org/georgringer/uri2link/v/stable)](https://packagist.org/packages/georgringer/uri2link)
[![Monthly Downloads](https://poser.pugx.org/georgringer/uri2link/d/monthly)](https://packagist.org/packages/georgringer/uri2link)
[![License](https://poser.pugx.org/georgringer/uri2link/license)](https://packagist.org/packages/georgringer/uri2link)


This extension converts external links provided by editors to TYPO3 links. Given is website `https://demo.vm/`
and editor sets as link `https://demo.vm/my-sit/contact` and this is actually an existing page, the link
will be transformed to `t3://page?uid=123`.

All links in fields in TCA properties with `renderType=inputLink` are checked, links in RTE are ignored!

## Installation

- Install extensions as any other. Either use `composer require georgringer/uri2link` or install extension via TER.
- All new links will be checked

## Todos

- Support RTE
- Command call for mass checking
