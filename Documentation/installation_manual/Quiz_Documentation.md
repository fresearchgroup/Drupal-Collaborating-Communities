# Quiz
*Karanjit Gill*

### Modules:
1. [Quiz](https://www.drupal.org/project/quiz) 
1. [Quiz_userpoints](https://www.drupal.org/project/quiz_userpoints) 
1. [jQuery Countdown](https://www.drupal.org/project/jquery_countdown) 


### Implementation:
1. Install and enable the above modules.
1. Goto Quiz -> Quiz Settings -> Question Configuration to configure question settings for different quiz types.
1. Goto Quiz -> Quiz Settings -> Quiz Configuration
    1. Configure fields under Global Configuration
    1. Set Administrator Review options
    1. Under Add-on Configuration -> Check Display Timer for the jQuery module.
    1. Under Look and feel enter a name for the quiz type.
1. Goto Quiz -> Quiz Settings -> Quiz Form Configuration
    1. Under Taking Options
    1. Set general quiz settings
    1. Set random questions
    1. Set review options
    1. Choose number of attempts
    1. Set a time limit in seconds
    1. Check Always available inder Availability options
    1. Set passing rate under Pass/Fail Options


### Quiz Page:
Add a page for all quiz questions

1. Goto Structure -> Views -> Add new view -> View name -> Enter a name -> Show -> Content -> of type -> Quiz -> Newest First
2. Check Create a Page -> Page title -> Enter a title -> Path -> Enter a path -> Display Format -> Bootstrap Thumbnails  -> of -> teasers -> with links -> without comments -> Set number of items to display -> Check Use a Pager -> Create a menu link -> Menu -> Main Menu -> Enter link text.-> Continue and Edit.
1. On next page under Format -> Show -> For -> This page -> Bootstrap Thumbnails -> Apply -> Cancel Fields window -> Fields -> Add -> For -> This page -> Search -> Post date -> Content:Post date -> .Apply -> Format -> Show -> Settings -> Image -> Post date -> Title -> Content title -> Apply. 
1. Add a Quiz button
    1. Goto Structure -> Blocks -> Enter Block title -> Enter Block description -> Add Text or HTML to the Body(Ex Add Quiz).
    1. Region Settings -> bootxd -> Primary -> Bootstrap -> Primary.
    1. Pages option in the sidebar -> Show block on specific pages -> only listed pages -> Enter path of the quiz page relative to the drupal root 
Ex. quiz for http://localhost/drupal/quiz 
    1. Roles -> Check users that can see the Add Quiz button.

### Manage Quizzes:
1. Add a Quiz
    1. Goto Add  Content -> Quiz -> Set a Quiz title -> Select Publish under Section -> Write a description about the quiz in the body field -> Change settings if needed.
    1. Add User-points to quiz
    1. Goto Userpoints (in the options sidebar) -> Add -> Select Userpoints Category to the points category for adding the quiz score. -> Award Mode -> Only when quiz is passed -> Check Award Once -> Score Type -> Numeric -> Save
1. Manage Quiz
    1. Ths window appears after saving a new quiz or {Select a quiz} -> Quiz -> Manage questions
    1. Create a Question
    1. On the Manage questions page -> Create new question -> Select Question type (ex. Multiple choice Question) -> Write the question in the Question field -> Enter a title -> Answer -> Check correct answer and type the answer -> Settings -> Choose appropriate fields -> Select Workflow stage -> Save.
    1. Adding questions to a quiz from question bank
    1. On the manage questions window -> Question Bank -> Check the question that need to be added -> Operations -> Add questions to quiz.
    1. Goto -> Manage questions -> Questions in this quiz -> Set number of random questions -> Set max score for each questions.-> Submit.



