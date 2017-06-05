Quiz.module [![Build Status](https://travis-ci.org/atquizz/quizz.module.svg?branch=7.x-6.x)](https://travis-ci.org/atquizz/quizz.module) [![Gitter chat](https://badges.gitter.im/atquizz/quizz.module.png)](https://gitter.im/atquizz/quizz.module)
====

Overview
--------

The quizz.module provides a framework allows you to create interactive quizzes
for your visitors. It allows for the creation of questions of varying types, and
to collect those questions into quizzes.

Features
--------

This list isn't complete (not even close)

 - Administrative features:
    o Assign feedback to responses to help point out places for further study
    o Supports multiple answers to questions
    o Limit the number of takes users are allowed
    o Randomize questions during the quiz
    o Assign only specific questions from the question bank

 - User features:
   o Can create/edit own quizzes.
   o Can take a quiz if have 'view quizzes' permissions, and receive score

Dependencies
------------

- https://www.drupal.org/project/ctools
- https://www.drupal.org/project/entity
- https://www.drupal.org/project/views
- https://www.drupal.org/project/views_bulk_operations
- https://www.drupal.org/project/xautoload

Strongly recommended:

- https://www.drupal.org/project/date (date_popup, date_views)
- https://www.drupal.org/project/field_group
- https://www.drupal.org/project/jquery_countdown

Quizz only provides truefalse question by default. You may need download more
question types from:

- https://www.drupal.org/project/quizz_cloze
- https://www.drupal.org/project/quizz_ddlines
- https://www.drupal.org/project/quizz_dragdrop
- https://www.drupal.org/project/quizz_h5p
- https://www.drupal.org/project/quizz_matching
- https://www.drupal.org/project/quizz_memory
- https://www.drupal.org/project/quizz_multichoice
- https://www.drupal.org/project/quizz_pool
- https://www.drupal.org/project/quizz_scale
- https://www.drupal.org/project/quizz_text (long/short answer)

Configuration
-------------

1. Config for global quizz at /admin/quizz/settings
2. Quizz provides one quiz type by default. You can add more at /admin/quizz/types
3. For each quiz type, you can change default options.
4. Add the "access quiz" permission to roles under Administer >> User management >> Access control

How to create a quiz
--------------------

1. Begin by creating a series of questions that you would like to include in
   the quiz. Go to Create content >> <question type> (for example, Multichoice).

2. Next, create a basic quiz by going to Create content >> Quiz. You will have
   the opportunity to set numerous options such as the number of questions,
   whether or not to shuffle question order, etc. When finished, click "Submit."

3. Finally, add questions to the quiz by clicking the "Manage questions" tab.
  Here you can also edit the order of the questions, and the max score for each
  question.

Credits
-------

- Original quiz.module's contributors.
- Original forker: /u/thehong. Sponsored by GO1 (http://www.go1.com.au/)
