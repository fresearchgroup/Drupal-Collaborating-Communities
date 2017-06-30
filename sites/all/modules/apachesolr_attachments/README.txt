
Apache Solr Attachments for 7.x

Requires the ability to run java and installation of tika 0.3 or higher,
or access to a solr server set up for content extraction (e.g. a Solr
1.4 final release).  For Solr, there is a patch to apply to the solrconfig
to add another request handler.

When Apache Solr Attachments was added afterwards, then re-indexing is
necessary. To diagnose functionality, use the solr search at
<your-domain>:<your-port>/solr/admin/ for something like
url:*.pdf or url:*.doc. The results will show if any files were indexed.

see:
http://lucene.apache.org/tika/gettingstarted.html
http://lucene.apache.org/tika/formats.html

Tika will extract many file formats, including PDFs, MS Office (2003 format
as well as new docx format).  Java 6 or 7 may be needed on some
platforms to support all formats.  The page on formats seems not to be 100%
up to date.  In particular, https://issues.apache.org/jira/browse/TIKA-152
is committed, so it does currently support MS Office 2007 documents to
some reasonable degree.

The easiest-to-find pre-built Tika app is available from the download page:
http://tika.apache.org/download.html

e.g. tika-app-1.1.jar

You can copy/move the jar to somewhere convenient, though it's probably a good idea
to keep it outside your docroot.

Solr 1.4.1 uses tika 0.4, 1.4.2-dev uses tika 0.7, and Solr 3.5.0 uses 0.10,
and newer releases of Sorl are likey to use 1.1+.  Note that the version
numbering changed, so 0.1 was followed by 1.0 and 1.1.

If you need to build tika from source using maven (mvn).  Get the tika
source from:
http://lucene.apache.org/tika/download.html

You may need to increase the memory for java/mvn using (for example):
export MAVEN_OPTS="-Xmx1024m -Xms512m"

mvn install

will build the full set of tika applications - it will build the app jar
in a location like tika-app/target/tika-app-1.1.jar

Copy tika-app-1.1.jar from there or point the module path to it.

See also build instructions at: http://drupal.org/node/540974#comment-1944082

If you are using Solr to extract your content, you need to copy (or symlink)
the contents of contrib/extraction/lib to a directory named lib under your
solr home, or alter solrconfig.xml to add the orgiginal directory as a
lib directory.

