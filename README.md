Shortcodes (module for Omeka S)
===============================

> __New versions of this module and support for Omeka S version 3.0 and above
> are available on [GitLab], which seems to respect users and privacy better
> than the previous repository.__

[Shortcodes] is a module for [Omeka S] that allows to insert shortcuts in site
pages in order to display more content via a simple string.

The shortcodes are well known in Wikipedia (named [wikitext]), [WordPress], [Omeka Classic],
and many other cms. The format that is used is the one of WordPress and Omeka Classic,
like `[shortcode feature=value]`. For example, `[media id=50]` will render the
media #50 in the html code. Or `[recent_items]` will display a list of the last
five items.

All core shortcodes of Omeka Classic are integrated and other ones are gradually
implemented.

Of course, other modules can add new ones: just add it as a config key under `[shortcodes]`.


Installation
------------

Uncompress files and rename module folder `Shortcodes`. Then install it like any
other Omeka module and follow the config instructions.

See general end user documentation for [Installing a module].


Quick start
-----------

A shortcode is a string to add in a textarea field a site page block. It looks
like `[shortcode feature=value]`. There can be multiple feature and values can
be protected with quotes or double quotes when they contain spaces: `[shortcode feature1="my value" feature2='my "value"']`.
When the shortcode is not identified, it is displayed as it.

Shortcodes work exactly like in Omeka Classic, with the same shortcuts, so you
can consult the [Omeka Classic] user manual. Nevertheless, some shortcuts are
marked deprecated and it is recommended to use the more semantic equivalent ones
for Omeka S.

For example, to render the media #50, you can use: `[media id=50]`. This is the
equivalent of `[file id=50]` that was used in Omeka Classic, and that is now a
simple alias.


Development
-----------

Any module can add new shortcode: just add it as a config key under `[shortcodes]`.

The shortcode can be any class that can be invoked; just add the interface
`Shortcode\Shortcode\ShortcodeInterface` to it, or extends the class from the
abstract class `Shortcode\Shortcode\AbstractShortcode`. If a partial is needed,
it is recommended to put it in directory common/shortcode.


TODO
----

- [ ] Shortcoder any.
- [ ] WordPress shortcodes (alias to Omeka).
- [ ] Use the WordPress parser for html.


Warning
-------

Use it at your own risk.

It’s always recommended to backup your files and your databases and to check
your archives regularly so you can roll back if needed.


Troubleshooting
---------------

See online issues on the [module issues] page on GitLab.


License
-------

This module is published under the [CeCILL v2.1] license, compatible with
[GNU/GPL] and approved by [FSF] and [OSI].

This software is governed by the CeCILL license under French law and abiding by
the rules of distribution of free software. You can use, modify and/ or
redistribute the software under the terms of the CeCILL license as circulated by
CEA, CNRS and INRIA at the following URL "http://www.cecill.info".

As a counterpart to the access to the source code and rights to copy, modify and
redistribute granted by the license, users are provided only with a limited
warranty and the software’s author, the holder of the economic rights, and the
successive licensors have only limited liability.

In this respect, the user’s attention is drawn to the risks associated with
loading, using, modifying and/or developing or reproducing the software by the
user in light of its specific status of free software, that may mean that it is
complicated to manipulate, and that also therefore means that it is reserved for
developers and experienced professionals having in-depth computer knowledge.
Users are therefore encouraged to load and test the software’s suitability as
regards their requirements in conditions enabling the security of their systems
and/or data to be ensured and, more generally, to use and operate it in the same
conditions as regards security.

The fact that you are presently reading this means that you have had knowledge
of the CeCILL license and that you accept its terms.


Copyright
---------

* Copyright Roy Rosenzweig Center for History and New Media, 2014
* Copyright Daniel Berthereau, 2021 (see [Daniel-KM])

The shortcode parser is an improved version of the one that is used [in WordPress]
since 2008 (version 2.5). The same is used [in Omeka Classic] since 2014
(version 2.2) too, and it can be found in older various places.


[Shortcodes]: https://github.com/Daniel-KM/Omeka-S-module-Shortcodes
[Omeka S]: https://omeka.org/s
[WordPress]: https://wordpress.com/support/shortcodes/
[Omeka Classic]: https://omeka.org/classic/docs/Content/Shortcodes/
[Installing a module]: http://dev.omeka.org/docs/s/user-manual/modules/#installing-modules
[module issues]: https://gitlab.com/Daniel-KM/Omeka-S-module-Shortcodes/issues
[CeCILL v2.1]: https://www.cecill.info/licences/Licence_CeCILL_V2.1-en.html
[GNU/GPL]: https://www.gnu.org/licenses/gpl-3.0.html
[FSF]: https://www.fsf.org
[OSI]: http://opensource.org
[in WordPress]: https://developer.wordpress.org/reference/functions/get_shortcode_atts_regex/
[in Omeka Classic]: https://github.com/omeka/Omeka/blob/master/application/views/helpers/Shortcodes.php#L96-L117
[GitLab]: https://gitlab.com/Daniel-KM
[Daniel-KM]: https://gitlab.com/Daniel-KM "Daniel Berthereau"
