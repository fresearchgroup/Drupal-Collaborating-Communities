# Auto-tagging content with Mltag:
*Karanjit Gill*

[Porter Stemmer module](https://www.drupal.org/project/porterstemmer):

Drupal Description: This module implements the Porter stemming algorithm to improve English-language searching with the Drupal built-in Search module.

1. Install and enable the Porter stemmer module for Drupal 7.
2. After installation you need to rebuild the search index.
    1. Goto Configuration -> Search and Metadata -> Search Settings -> Indexing Status -> Re-index Site.
    1. Goto Configuration -> System -> Cron -> Run Cron. To start the rebuild.

[MLtag module](https://www.drupal.org/project/mltag):

Drupal Description: The module suggests tags to the user depending upon the content of the post using Learning Algorithms. Based on an algorithm and a ranking mechanism the user will be provided with a list of tags from which he can select those that best describe the article and also train a user-content learning model in the background. The model trains on the user's past tagging patterns and posts on the website which are then used to propose tags.

1. If the tag fields are already set skip to #3.
2. Add Tag field.
    1. Goto Structure -> Content-Types -> {any content type that needs to have tags} -> Manage Fields -> Add new fields -> {field name} -> Select field type -> Term reference -> Select a widget -> Autocomplete term widget (tagging).
    1. Open edit tab for the tags field created above -> Scroll to “Tags” field settings -> Number of values -> Unlimited (Recommended, but it can be limited to any value)
    1. Click on Enable Translation under Field Translation.(Reason for Error #1).
    1. Choose the appropriate Vocabulary for this field.
3. Install and enable MLtag module.
4. Goto Configuration -> MLtag settings -> Algorithm -> Select an algorithm (Word Co-occurence is Recommended) -> Check Enable Learning Algorithm -> Set a Implicit Tag count -> Add vocabulary terms to the trained model.
5. Goto -> Configure fields for MLtag -> Select the fields for the desired content types.
6. Goto -> Insert Values -> Perform Training to manually train the model.
7. The MLtag module is now setup. You can use this module under the MLtag settings option while creating a content.

[Suggested Terms](https://www.drupal.org/project/suggestedterms):

Drupal Description: This module provides "suggested terms" for free-tagging Taxonomy fields based on terms already submitted. It replaces the description field on free-tagging fields with a clickable list of previously entered terms. If javascript is not enabled the list will still appear but not be clickable. It provides the best of both worlds between a pre-existing list of terms and the ability to add new terms on the fly as needed.

1. Install and enable the module.
2. Goto Configuration -> Content Authoring -> Suggested Terms -> Set maximum number of links -> Set link order -> Select which terms to display -> Save configuration.
