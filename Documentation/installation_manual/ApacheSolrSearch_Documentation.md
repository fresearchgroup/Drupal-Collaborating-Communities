# Apache Solr Search:
*Karanjit Gill*


Drupal Description: This module integrates Drupal with the Apache Solr search platform. Solr search can be used as a replacement for core content search and boasts both extra features and better performance. If you're looking for Apache Solr integration, this is possibly the best option available

Configuration steps:
1. Install and Enable [Apache Solr module](https://www.drupal.org/project/apachesolr) in Drupal.
2. Install Solr and make sure it is running.
3. Goto Configuration -> Search and Metadata -> Apache Solr search
4. Click +Add search environment.
5. Enter solr server URL. For example http://localhost:8983/solr/{core_name}
6. Add a description for the search environment.
7. You can choose to make this search environment default if it is not already.
8. Provide read and write access (Recommended).
9. Click Test Connection to check if the solr is reachable.
10.  Click on Save.
11. Switch to Default Index Tab.
12. Under CONFIGURATION select the content-types to be indexed. For example Article,etc.
13. Click on Save.
14. Index Site
    1. Under Actions tap on Queue all content for re-indexing
    1. Then click on Index all queued content.
15. Happy Searching!

# Apache Solr Attachments:
*Karanjit Gill*

Drupal Description: An add-on module for Apache Solr Search Integration to enable indexing and searching of file attachments. The text of the attachments may be extracted locally using Tika (a java application) or remotely by Solr (using the same Tika library).

Configuration steps:
1. Skip to #3 if the File field is already set.
2. Add a File field.
    1. Goto -> Structure -> Content-type -> {any content that needs an attachment} -> Manage Fields -> Add new field -> {field name} -> Select a field type -> FILE
    1. Open Edit tab for the field created above -> Enable Display Field -> Check the field displayed by default checkbox.
    1. Goto Configuration -> Search and Metadata -> Apache Solr Search -> Default Index -> Configuration -> Check the File checkbox -> Save.
3. Install [Apache Solr Attachments module](https://www.drupal.org/project/apachesolr_attachments) in Drupal.
4. For SQL 5.7 a change in schema is required(at the time of writing this manual). 
    1. Get the patch file from https://www.drupal.org/node/2803667 or use the one here.
    1. Paste the patch in the sites/all/modules/apachesolr_attachments/
    1. Apply the patch to the  apachesolr_attachments.install file with the following command
    1. patch  apachesolr_attachments.install -i {pathfile}.patch
5. Enable Apache Solr Attachments module.
6. Download Apache Tika App jar from https://tika.apache.org/download.html. Ex. tika-app-1.15.jar
7. Place jar file in the {drupal_root}/sites/all/libraries/{tika_dir} folder
8. Goto Configuration -> Search and Metadata -> Apache Solr search -> Attachments
9. Select Tika under Extract using.
10. Enter the absolute path to the tika directory created in the step #3.
11. Enter the name of the jar, Ex. tika-app-1.15.jar.
12. Click Save.
13. Under Action click on Test your tika extraction to check if the jar is reachable.
14. Re-index your site.
15. Happy Searching!

