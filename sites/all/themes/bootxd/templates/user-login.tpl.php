<?php
 // split the username and password so we can put the form links were we want (they are in the "user-login-links" div bellow)
 print drupal_render($form['name']);
 print drupal_render($form['pass']);
   ?>

   <div class="user-login-links">
 <span class="password-link"><a href="/user/password">Forget your password?</a></span> | <span class="register-link"><a href="/user/register">Create an account</a></span>
   </div>

   <?php
       // render login button
 print drupal_render($form['form_build_id']);
 print drupal_render($form['form_id']);
 print drupal_render($form['actions']);
   ?>

<!-- /user-login-custom-form -->
