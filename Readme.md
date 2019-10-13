# TYPO3 Extension uri2links

This extension converts external links provided by editors to TYPO3 links. Given is website `https://demo.vm/`
and editor sets as link `https://demo.vm/my-sit/contact` and this is actually an existing page, the link
will be transformed to `t3://page?uid=123`.

All links in fields in TCA properties with `renderType=inputLink` are checked, links in RTE are ignored!

## Installation

- Install extensions as any other.
- All new links will be checked

## Todos

- Support RTE
- Command call for mass checking



