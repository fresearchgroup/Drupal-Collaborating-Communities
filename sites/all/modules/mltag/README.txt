A module to populate Tags for content using Machine Learning Algorithms 
- a GSOC 2012 project. Here are some steps for using MLTag-

Installation-
MLTag requires "Porter Stemmer" module for it to work. Please download it first
from https://drupal.org/project/porterstemmer and then proceed.
 
1. Install MLTag and jump to the configuration page.
 
2. Select the algorithm from the drop down list.

3. Check the 'Enable Learning' checkbox if you want MLTag to propose Tags based
on the Learning Algorithm. Unchecking this option will propose Implicit tags 
which are based on current content only.

4. Click the 'Perform Training' button to train the model.

5. Click again to confirm the action. It takes some time to train the model 
based on the amount of content you have on your website.

6. The status of the Model is displayed right beneath the Perform Training 
Button.

7. Check the 'Implicit Tag Count' checkbox if you want MLTag to train both on 
Published & Unpublished content. Unchecking this option trains the model only 
on Published content.

8. Specify the max number of Implicit tags you want to propose in the text 
field. The minimum allowed value here is 10.

9. The next group of checkboxes displays the Vocabularies defined in the 
Taxonomy Module. You can add customized terms (which will be given higher 
priority) for training the model by choosing appropriate vocabularies here.

10. Below you can check the Node Types on which you want to use MLTag.

11. Save the settings.

Whenever you add/edit a new article. MLTag displays a button 'Suggest Tags' 
in the settings panel. On clicking this Implicit Tags and Learned Tags tables 
will be populated by ajax. For Implicit tags the terms with the highest Chi 
Square values are more likely to be tags for the current content. The Learned 
Tags are proposed based on the model we trained earlier.
Select any number of tags you want to tag the content with and save the article.
The content will be automatically tagged with the chosen terms.
The Learned model will also be updated based on your tagging preferences and 
on the new textual content you have posted on your website.
