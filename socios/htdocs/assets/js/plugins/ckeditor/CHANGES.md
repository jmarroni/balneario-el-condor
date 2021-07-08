CKEditor 4 Changelog
====================

## CKEditor 4.4.7

Fixed Issues:

* [#12825](http://dev.ckeditor.com/ticket/12825): Fixed: Preventing the [Table Resize](http://ckeditor.com/addon/tableresize) plugin from operating on elements outside the editor. Thanks to [Paul Martin](https://github.com/Paul-Martin)!
* [#12157](http://dev.ckeditor.com/ticket/12157): Fixed: Lost text formatting on pressing *Tab* when the [`config.tabSpaces`](http://docs.ckeditor.com/#!/api/CKEDITOR.config-cfg-tabSpaces) configuration option value was greater than zero.
* [#12777](http://dev.ckeditor.com/ticket/12777): Fixed: The `table-layout` CSS property should be reset by skins. Thanks to [vita10gy](https://github.com/vita10gy)!
* [#12812](http://dev.ckeditor.com/ticket/12812): Fixed: An uncaught security exception is thrown when [Line Utilities](http://ckeditor.com/addon/lineutils) are used in an inline editor loaded in a cross-domain `iframe`. Thanks to [Vitaliy Zurian](https://github.com/thecatontheflat)!
* [#12735](http://dev.ckeditor.com/ticket/12735): Fixed: [`config.fillEmptyBlocks`](http://docs.ckeditor.com/#!/api/CKEDITOR.config-cfg-fillEmptyBlocks) should only apply when outputting data.
* [#10032](http://dev.ckeditor.com/ticket/10032): Fixed: [Paste from Word](http://ckeditor.com/addon/pastefromword) filter is executed for every paste after using the button.
* [#12597](http://dev.ckeditor.com/ticket/12597): [Blink/Webkit] Fixed: Multi-byte Japanese characters entry not working properly after *Shift+Enter*.
* [#12387](http://dev.ckeditor.com/ticket/12387): Fixed: An error is thrown if a skin does not have the [`chameleon`](http://docs.ckeditor.com/#!/api/CKEDITOR.skin-method-chameleon) property defined and [`config.uiColor`](http://docs.ckeditor.com/#!/api/CKEDITOR.config-cfg-uiColor) is defined.
* [#12747](http://dev.ckeditor.com/ticket/12747): [IE8-10] Fixed: Opening a drop-down for a specific selection when the editor is maximized results in incorrect drop-down panel position.
* [#12850](http://dev.ckeditor.com/ticket/12850): [IEQM] Fixed: An error is thrown after focusing the editor.

## CKEditor 4.4.6

**Security Updates:**

* Fixed XSS vulnerability in the HTML parser reported by [Maco Cortes](https://www.facebook.com/Maaacoooo).

	Issue summary: It was possible to execute XSS inside CKEditor after persuading the victim to: (i) switch CKEditor to source mode, then (ii) paste a specially crafted HTML code, prepared by the attacker, into the opened CKEditor source area, and (iii) switch back to WYSIWYG mode.

**An upgrade is highly recommended!**

New Features:

* [#12501](http://dev.ckeditor.com/ticket/12501): Allowed dashes in element names in the [string format of allowed content rules](http://docs.ckeditor.com/#!/guide/dev_allowed_content_rules-section-string-format).
* [#12550](http://dev.ckeditor.com/ticket/12550): Added the `<main>` element to the [`CKEDITOR.dtd`](http://docs.ckeditor.com/#!/api/CKEDITOR.dtd).

Fixed Issues:

* [#12506](http://dev.ckeditor.com/ticket/12506): [Safari] Fixed: Cannot paste into inline editor if the page has `user-select: none` style. Thanks to [shaohua](https://github.com/shaohua)!
* [#12683](http://dev.ckeditor.com/ticket/12683): Fixed: [Filter](http://docs.ckeditor.com/#!/guide/dev_acf) fails to remove custom tags. Thanks to [timselier](https://github.com/timselier)!
* [#12489](http://dev.ckeditor.com/ticket/12489) and [#12491](http://dev.ckeditor.com/ticket/12491): Fixed: Various issues related to restoring the selection after performing operations on filler character. See the [fixed cases](http://dev.ckeditor.com/ticket/12491#comment:4).
* [#12621](http://dev.ckeditor.com/ticket/12621): Fixed: Cannot remove inline styles (bold, italic, etc.) in empty lines.
* [#12630](http://dev.ckeditor.com/ticket/12630): [Chrome] Fixed: Selection is placed outside the paragraph when the [New Page](http://ckeditor.com/addon/newpage) button is clicked. This patch significantly simplified the way how the initial selection (a selection after the content of the editable is overwritten) is being fixed. That might have fixed many related scenarios in all browsers.
* [#11647](http://dev.ckeditor.com/ticket/11647): Fixed: The [`editor.blur`](http://docs.ckeditor.com/#!/api/CKEDITOR.editor-event-blur) event is not fired on first blur after initializing the inline editor on an already focused element.
* [#12601](http://dev.ckeditor.com/ticket/12601): Fixed: [Strikethrough](http://ckeditor.com/addon/basicstyles) button tooltip spelling.
* [#12546](http://dev.ckeditor.com/ticket/12546): Fixed: The Preview tab in the [Document Properties](http://ckeditor.com/addon/docprops) dialog window is always disabled.
* [#12300](http://dev.ckeditor.com/ticket/12300): Fixed: The [`editor.change`](http://docs.ckeditor.com/#!/api/CKEDITOR.editor-event-change) event fired on first navigation key press after typing.
* [#12141](http://dev.ckeditor.com/ticket/12141): Fixed: List items are lost when indenting a list item with content wrapped with a block element.
* [#12515](http://dev.ckeditor.com/ticket/12515): Fixed: Cursor is in the wrong position when undoing after adding an image and typing some text.
* [#12484](http://dev.ckeditor.com/ticket/12484): [Blink/Webkit] Fixed: DOM is changed outside the editor area in a certain case.
* [#12688](http://dev.ckeditor.com/ticket/12688): Improved the tests of the [styles system](http://docs.ckeditor.com/#!/api/CKEDITOR.style) and fixed two minor issues.
* [#12403](http://dev.ckeditor.com/ticket/12403): Fixed: Changing the [font](http://ckeditor.com/addon/font) style should not lead to nesting it in the previous style element.
* [#12609](http://dev.ckeditor.com/ticket/12609): Fixed: Incorrect `config.magicline_putEverywhere` name used for a [Magic Line](http://ckeditor.com/addon/magicline) all-encompassing [`config.magicline_everywhere`](http://docs.ckeditor.com/#!/api/CKEDITOR.config-cfg-magicline_everywhere) configuration option.


## CKEditor 4.4.5

New Features:

* [#12279](http://dev.ckeditor.com/ticket/12279): Added a possibility to pass a custom evaluator to [`node.getAscendant()`](http://docs.ckeditor.com/#!/api/CKEDITOR.dom.node-method-getAscendant).

Fixed Issues:

* [#12423](http://dev.ckeditor.com/ticket/12423): [Safari7.1+] Fixed: *Enter* key moved cursor to a strange position.
* [#12381](http://dev.ckeditor.com/ticket/12381): [iOS] Fixed: Selection issue. Thanks to [Remiremi](https://github.com/Remiremi)!
* [#10804](http://dev.ckeditor.com/ticket/10804): Fixed: `CKEDITOR_GETURL` is not used with some plugins where it should be used. Thanks to [Thomas Andraschko](https://github.com/tandraschko)!
* [#9137](http://dev.ckeditor.com/ticket/9137): Fixed: The `<base>` tag is not created when `<head>` has an attribute. Thanks to [naoki.fujikawa](https://github.com/naoki-fujikawa)!
* [#12377](http://dev.ckeditor.com/ticket/12377): Fixed: Errors thrown in the [Image](http://ckeditor.com/addon/image) plugin when removing preview from the dialog window definition. Thanks to [Axinet](https://github.com/Axinet)!
* [#12162](http://dev.ckeditor.com/ticket/12162): Fixed: Auto paragraphing and *Enter* key in nested editables.
* [#12315](http://dev.ckeditor.com/ticket/12315): Fixed: Marked [`config.autoParagraph`](http://docs.ckeditor.com/#!/api/CKEDITOR.config-cfg-autoParagraph) as deprecated.
* [#12113](http://dev.ckeditor.com/ticket/12113): Fixed: A [code snippet](http://ckeditor.com/addon/codesnippet) should be presented in the [elements path](http://ckeditor.com/addon/elementspath) as "code snippet" (translatable).
* [#12311](http://dev.ckeditor.com/ticket/12311): Fixed: [Remove Format](http://ckeditor.com/addon/removeformat) should also remove `<cite>` elements.
* [#12261](http://dev.ckeditor.com/ticket/12261): Fixed: Filter has to be destroyed and removed from [`CKEDITOR.filter.instances`](http://docs.ckeditor.com/#!/api/CKEDITOR.filter-static-property-instances) on editor destroy.
* [#12398](http://dev.ckeditor.com/ticket/12398): Fixed: [Maximize](http://ckeditor.com/addon/maximize) does not work on an instance without a [title](http://docs.ckeditor.com/#!/api/CKEDITOR.config-cfg-title).
* [#12097](http://dev.ckeditor.com/ticket/12097): Fixed: JAWS not reading the number of options correctly in the [Text Color and Background Color](http://ckeditor.com/addon/colorbutton) button menu.
* [#12411](http://dev.ckeditor.com/ticket/12411): Fixed: [Page Break](http://ckeditor.com/addon/pagebreak) used directly in the editable breaks the editor.
* [#12354](http://dev.ckeditor.com/ticket/12354): Fixed: Various issues in undo manager when holding keys.
* [#12324](http://dev.ckeditor.com/ticket/12324): [IE8] Fixed: Undo steps are not recorded when changing 