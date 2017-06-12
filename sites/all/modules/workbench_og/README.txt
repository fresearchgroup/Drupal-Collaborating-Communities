DESCRIPTION
-----------------------
This module allows integration between workbench moderation and organic groups.
Organic group roles can be defined to be responsible to perform different
transitions that will move content from the different stages.

USE CASE
------------------------
A perfect usecase for the usage of this module: a website with several groups
that contains a content workflow process. 
- Users with "Content author" role in
group "Finance" can create content that needs to be approved before being
published to "Finance". 
- Only "Content editors" of group "Finance" can review
and publish those nodes. 
- "Content authors" can create new revisions of content
that is curently published. That content remains published till the new revision
gets reviewed and published. 
- "Content authors" and "Content editors" of
different groups will not have access to content that is under review in group
"Finance".

The module requires OG 7.x-2.x 

INSTALLATION
-------------------------
After installing the module, new permissions will be available to allow passing 
trough the normal workflow. Per group you can define who can transition content
from stage to stage. You can configure permissions in OG permissions, defining 
global or specific group permissions.

After that, when visiting a node page, group members with enough permissions 
should be able to review and publish group nodes.
