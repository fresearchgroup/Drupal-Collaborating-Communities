The User Points Voting API module extends the power to award points to voting
modules that depend on Voting API (Fivestar, Rate, etc).

The module does the following:
 - Adds points to a user's account when they cast a vote using a Voting API-
   enabled module.
 - Deducts points from a user's account if a vote is cancelled.
 - Administrator can configure how many points each vote is worth based on
   vote tag and vote value range.
 - Administrator can configure how many points can be earned in a 24-hour
   period via voting.

To install the module:
 - Copy the module to your /sites/all/modules/contrib directory.
 - Enable the module at: /admin/modules
 - Grant rights to the module at: /admin/people/permissions
 - Configure the module at: /admin/config/people/userpoints_votingapi
