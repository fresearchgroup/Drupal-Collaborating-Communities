<?php

/**
 * Sami is An API documentation generator — https://github.com/FriendsOfPHP/Sami
 *
 * Install sami:
 *
 *  curl -O http://get.sensiolabs.org/sami.phar
 *  chmod a+x sami.phar
 *  mv sami /usr/local/bin/sami
 *
 * Generate documentation:
 * 
 *  cd /path/to/quizz/
 *  sami render misc/sami_config.php
 */
use Sami\Sami;
use Symfony\Component\Finder\Finder;

$iterator = Finder::create()
  ->files()
  ->name('*.module')
  ->name('*.inc')
  ->name('*.php')
  ->in('/Users/andy/git/modules/quiz')
;

return new Sami($iterator, array(
    'title'                => 'Drupal Quizz module',
    'default_opened_level' => 10
  ));
