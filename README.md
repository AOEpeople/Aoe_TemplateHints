# Aoe_TemplateHints

http://www.fabrizio-branca.de/magento-advanced-template-hints-20.html

Aoe_TemplateHints extends the default Magento "Template Hints" developer functionality by adding more information for each block:

- shows all blocks (and not only the blocks inheriting from Mage_Core_Block_Template),
- show the cache status of blocks (cached, uncached or nested in a cached block),
- add much more useful data depending on the block type (e.g. template file or cms block-id - check the original blog post for a reference),
- cleans up the visual appearance of the template hints,
- can be triggered without changing configuration settings in the backend
- ...and is much more fun to use :)

To show the template hints simply add `?ath=1` to the shop URL after installing this module.

Please make sure to uninstall/deactive the module before going live!

## PHPStorm support

Install "Remote Call" plugin
