ABOUT

This module provides "suggested terms" for free-tagging Taxonomy fields based on terms
already submitted.  It replaces the description field on free-tagging fields with a
clickable list of previously entered terms.  If javascript is not enabled the list will
still appear but not be clickable.  It provides the best of both worlds between a
pre-existing list of terms and the ability to add new terms on the fly as needed.

REQUIREMENTS

- Drupal 6.x

CONFIGURATION

Once the module is enabled, go to admin/config/content/suggestedterms to configure the module.
Terms may be ordered by popularity, alphabetically, or most recently added and the list
may be limited to any number of terms.  The settings will apply to all free-tagging fields
on all nodes.

MODULE WEIGHT

In order to function properly, this module's weight must be higher (heavier) than that of
some other taxonomy-related modules. If the suggested terms are not showing up at all, this
could be the cause. Module weights are stored in the {system} table in the database.
Known modules that must have a lighter weight than suggestedterms:
 taxonomy (core)
 hs_taxonomy (hierarchical_select)
An example of a query that will set the weight of this module to '3':
sql-query "UPDATE {system} SET weight = 3 WHERE name = 'suggestedterms'"

Original issue: http://drupal.org/node/188786

AUTHOR AND CREDIT

Original Development:
Gareth Arch
garch@tampabay.rr.com

Larry Garfield
garfield@palantir.net
http://www.palantir.net/

Maintainer:
Beth Binkovitz
binkovitz@palantir.net/

This module was initially developed by Palantir.net and released to the Drupal
community under the GNU General Public License v2.
