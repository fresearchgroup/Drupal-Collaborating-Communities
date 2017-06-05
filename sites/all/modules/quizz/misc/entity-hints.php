<?php

/**
 * Nodes represent the main site content items.
 *
 * @fieldable yes
 * @configuration no
 * @label Node
 */
class NodeArticleMetadataWrapper extends EntityDrupalWrapper
{

    /**
     * Node ID
     *
     * The unique ID of the node.
     *
     * @property
     * @var EntityValueWrapper integer
     */
    public $nid = null;

    /**
     * Revision ID
     *
     * The unique ID of the node's revision.
     *
     * @property
     * @var EntityValueWrapper integer
     */
    public $vid = null;

    /**
     * Is new
     *
     * Whether the node is new and not saved to the database yet.
     *
     * @property
     * @var EntityValueWrapper boolean
     */
    public $is_new = null;

    /**
     * Content type
     *
     * The type of the node.
     *
     * @property
     * @var EntityValueWrapper token
     * @required
     */
    public $type = null;

    /**
     * Title
     *
     * The title of the node.
     *
     * @property
     * @required
     */
    public $title = null;

    /**
     * Language
     *
     * The language the node is written in.
     *
     * @property
     * @var EntityValueWrapper token
     */
    public $language = null;

    /**
     * URL
     *
     * The URL of the node.
     *
     * @property
     * @var EntityValueWrapper uri
     */
    public $url = null;

    /**
     * Edit URL
     *
     * The URL of the node's edit page.
     *
     * @property
     * @var EntityValueWrapper uri
     */
    public $edit_url = null;

    /**
     * Status
     *
     * Whether the node is published or unpublished.
     *
     * @property
     * @var EntityValueWrapper integer
     */
    public $status = null;

    /**
     * Promoted to frontpage
     *
     * Whether the node is promoted to the frontpage.
     *
     * @property
     * @var EntityValueWrapper boolean
     */
    public $promote = null;

    /**
     * Sticky in lists
     *
     * Whether the node is displayed at the top of lists in which it appears.
     *
     * @property
     * @var EntityValueWrapper boolean
     */
    public $sticky = null;

    /**
     * Date created
     *
     * The date the node was posted.
     *
     * @property
     * @var EntityValueWrapper date
     */
    public $created = null;

    /**
     * Date changed
     *
     * The date the node was most recently updated.
     *
     * @property
     * @var EntityValueWrapper date
     */
    public $changed = null;

    /**
     * Author
     *
     * The author of the node.
     *
     * @property
     * @var EntityDrupalWrapper user
     * @required
     */
    public $author = null;

    /**
     * Translation source node
     *
     * The original-language version of this node, if one exists.
     *
     * @property
     * @var EntityDrupalWrapper node
     */
    public $source = null;

    /**
     * Revision log message
     *
     * In case a new revision is to be saved, the log entry explaining the changes for
     * this version.
     *
     * @property
     * @var EntityValueWrapper text
     */
    public $log = null;

    /**
     * Creates revision
     *
     * Whether saving this node creates a new revision.
     *
     * @property
     * @var EntityValueWrapper boolean
     */
    public $revision = null;

    /**
     * The main body text
     *
     * @field
     * @var EntityStructureWrapper text_formatted
     */
    public $body = null;

    /**
     * @return NodeController
     */
    public function getControllerClass()
    {
        return entity_get_controller('node');
    }


}


/**
 * Nodes represent the main site content items.
 *
 * @fieldable yes
 * @configuration no
 * @label Node
 */
class NodePageMetadataWrapper extends EntityDrupalWrapper
{

    /**
     * Node ID
     *
     * The unique ID of the node.
     *
     * @property
     * @var EntityValueWrapper integer
     */
    public $nid = null;

    /**
     * Revision ID
     *
     * The unique ID of the node's revision.
     *
     * @property
     * @var EntityValueWrapper integer
     */
    public $vid = null;

    /**
     * Is new
     *
     * Whether the node is new and not saved to the database yet.
     *
     * @property
     * @var EntityValueWrapper boolean
     */
    public $is_new = null;

    /**
     * Content type
     *
     * The type of the node.
     *
     * @property
     * @var EntityValueWrapper token
     * @required
     */
    public $type = null;

    /**
     * Title
     *
     * The title of the node.
     *
     * @property
     * @required
     */
    public $title = null;

    /**
     * Language
     *
     * The language the node is written in.
     *
     * @property
     * @var EntityValueWrapper token
     */
    public $language = null;

    /**
     * URL
     *
     * The URL of the node.
     *
     * @property
     * @var EntityValueWrapper uri
     */
    public $url = null;

    /**
     * Edit URL
     *
     * The URL of the node's edit page.
     *
     * @property
     * @var EntityValueWrapper uri
     */
    public $edit_url = null;

    /**
     * Status
     *
     * Whether the node is published or unpublished.
     *
     * @property
     * @var EntityValueWrapper integer
     */
    public $status = null;

    /**
     * Promoted to frontpage
     *
     * Whether the node is promoted to the frontpage.
     *
     * @property
     * @var EntityValueWrapper boolean
     */
    public $promote = null;

    /**
     * Sticky in lists
     *
     * Whether the node is displayed at the top of lists in which it appears.
     *
     * @property
     * @var EntityValueWrapper boolean
     */
    public $sticky = null;

    /**
     * Date created
     *
     * The date the node was posted.
     *
     * @property
     * @var EntityValueWrapper date
     */
    public $created = null;

    /**
     * Date changed
     *
     * The date the node was most recently updated.
     *
     * @property
     * @var EntityValueWrapper date
     */
    public $changed = null;

    /**
     * Author
     *
     * The author of the node.
     *
     * @property
     * @var EntityDrupalWrapper user
     * @required
     */
    public $author = null;

    /**
     * Translation source node
     *
     * The original-language version of this node, if one exists.
     *
     * @property
     * @var EntityDrupalWrapper node
     */
    public $source = null;

    /**
     * Revision log message
     *
     * In case a new revision is to be saved, the log entry explaining the changes for
     * this version.
     *
     * @property
     * @var EntityValueWrapper text
     */
    public $log = null;

    /**
     * Creates revision
     *
     * Whether saving this node creates a new revision.
     *
     * @property
     * @var EntityValueWrapper boolean
     */
    public $revision = null;

    /**
     * The main body text
     *
     * @field
     * @var EntityStructureWrapper text_formatted
     */
    public $body = null;

    /**
     * @return NodeController
     */
    public function getControllerClass()
    {
        return entity_get_controller('node');
    }


}


/**
 * Types of Quiz.
 *
 * @fieldable no
 * @configuration yes
 * @label Quiz type
 */
class QuizTypeQuizTypeMetadataWrapper extends EntityDrupalWrapper
{

    /**
     * Internal, numeric quiz type ID
     *
     * The ID used to identify this quiz type internally.
     *
     * @property
     * @var EntityValueWrapper integer
     */
    public $id = null;

    /**
     * Machine-readable name
     *
     * The machine-readable name identifying this quiz type.
     *
     * @property
     * @var EntityValueWrapper token
     */
    public $type = null;

    /**
     * Label
     *
     * The human readable label.
     *
     * @property
     * @var EntityValueWrapper text
     */
    public $label = null;

    /**
     * Weight
     *
     * Quiz type "weight" property.
     *
     * @property
     * @var EntityValueWrapper integer
     */
    public $weight = null;

    /**
     * Data
     *
     * Quiz type "data" property.
     *
     * @property
     * @var EntityValueWrapper text
     */
    public $data = null;

    /**
     * Status
     *
     * Quiz type "status" property.
     *
     * @property
     * @var EntityValueWrapper integer
     */
    public $status = null;

    /**
     * Module
     *
     * Quiz type "module" property.
     *
     * @property
     * @var EntityValueWrapper text
     */
    public $module = null;

    /**
     * Description
     *
     * Quiz type "description" property.
     *
     * @property
     * @var EntityValueWrapper text
     */
    public $description = null;

    /**
     * Help
     *
     * Quiz type "help" property.
     *
     * @property
     * @var EntityValueWrapper text
     */
    public $help = null;

    /**
     * @return Drupal\quizz\Entity\QuizTypeController
     */
    public function getControllerClass()
    {
        return entity_get_controller('quiz_type');
    }


}


/**
 * Quiz entity
 *
 * @fieldable yes
 * @configuration no
 * @label Quiz
 */
class QuizEntityQuizMetadataWrapper extends EntityDrupalWrapper
{

    /**
     * Quiz ID
     *
     * The unique ID of the quiz.
     *
     * @property
     * @var EntityValueWrapper integer
     */
    public $qid = null;

    /**
     * Vid
     *
     * Quiz "vid" property.
     *
     * @property
     * @var EntityValueWrapper integer
     */
    public $vid = null;

    /**
     * Type
     *
     * Quiz "type" property.
     *
     * @property
     * @var EntityValueWrapper token
     */
    public $type = null;

    /**
     * Language
     *
     * Quiz "language" property.
     *
     * @property
     * @var EntityValueWrapper text
     */
    public $language = null;

    /**
     * Status
     *
     * Quiz "status" property.
     *
     * @property
     * @var EntityValueWrapper integer
     */
    public $status = null;

    /**
     * Label
     *
     * The human readable label.
     *
     * @property
     * @var EntityValueWrapper text
     */
    public $title = null;

    /**
     * Created
     *
     * Quiz "created" property.
     *
     * @property
     * @var EntityValueWrapper date
     */
    public $created = null;

    /**
     * Changed
     *
     * Quiz "changed" property.
     *
     * @property
     * @var EntityValueWrapper date
     */
    public $changed = null;

    /**
     * Uid
     *
     * Quiz "uid" property.
     *
     * @property
     * @var EntityDrupalWrapper user
     */
    public $uid = null;

    /**
     * URL
     *
     * The URL of the entity.
     *
     * @property
     * @var EntityValueWrapper uri
     */
    public $url = null;

    /**
     * Open date
     *
     * @property
     * @var EntityValueWrapper date
     */
    public $quiz_open = null;

    /**
     * Close date
     *
     * @property
     * @var EntityValueWrapper date
     */
    public $quiz_close = null;

    /**
     * Revision_uid
     *
     * @property
     * @var EntityValueWrapper integer
     */
    public $revision_uid = null;

    /**
     * Log
     *
     * @property
     * @var EntityValueWrapper text
     */
    public $log = null;

    /**
     * Aid
     *
     * @property
     * @var EntityValueWrapper text
     */
    public $aid = null;

    /**
     * Number_of_random_questions
     *
     * @property
     * @var EntityValueWrapper integer
     */
    public $number_of_random_questions = null;

    /**
     * Max_score_for_random
     *
     * @property
     * @var EntityValueWrapper integer
     */
    public $max_score_for_random = null;

    /**
     * Pass_rate
     *
     * @property
     * @var EntityValueWrapper integer
     */
    public $pass_rate = null;

    /**
     * Summary_pass
     *
     * @property
     * @var EntityValueWrapper text
     */
    public $summary_pass = null;

    /**
     * Summary_pass_format
     *
     * @property
     * @var EntityValueWrapper text
     */
    public $summary_pass_format = null;

    /**
     * Summary_default
     *
     * @property
     * @var EntityValueWrapper text
     */
    public $summary_default = null;

    /**
     * Summary_default_format
     *
     * @property
     * @var EntityValueWrapper text
     */
    public $summary_default_format = null;

    /**
     * Randomization
     *
     * @property
     * @var EntityValueWrapper integer
     */
    public $randomization = null;

    /**
     * Backwards_navigation
     *
     * @property
     * @var EntityValueWrapper integer
     */
    public $backwards_navigation = null;

    /**
     * Keep_results
     *
     * @property
     * @var EntityValueWrapper integer
     */
    public $keep_results = null;

    /**
     * Repeat_until_correct
     *
     * @property
     * @var EntityValueWrapper integer
     */
    public $repeat_until_correct = null;

    /**
     * Takes
     *
     * @property
     * @var EntityValueWrapper integer
     */
    public $takes = null;

    /**
     * Show_attempt_stats
     *
     * @property
     * @var EntityValueWrapper integer
     */
    public $show_attempt_stats = null;

    /**
     * Time_limit
     *
     * @property
     * @var EntityValueWrapper integer
     */
    public $time_limit = null;

    /**
     * Quiz_always
     *
     * @property
     * @var EntityValueWrapper integer
     */
    public $quiz_always = null;

    /**
     * Tid
     *
     * @property
     * @var EntityValueWrapper integer
     */
    public $tid = null;

    /**
     * Has_userpoints
     *
     * @property
     * @var EntityValueWrapper integer
     */
    public $has_userpoints = null;

    /**
     * Userpoints_tid
     *
     * @property
     * @var EntityValueWrapper integer
     */
    public $userpoints_tid = null;

    /**
     * Time_left
     *
     * @property
     * @var EntityValueWrapper integer
     */
    public $time_left = null;

    /**
     * Max_score
     *
     * @property
     * @var EntityValueWrapper integer
     */
    public $max_score = null;

    /**
     * Allow_skipping
     *
     * @property
     * @var EntityValueWrapper integer
     */
    public $allow_skipping = null;

    /**
     * Allow_resume
     *
     * @property
     * @var EntityValueWrapper integer
     */
    public $allow_resume = null;

    /**
     * Allow_jumping
     *
     * @property
     * @var EntityValueWrapper integer
     */
    public $allow_jumping = null;

    /**
     * Allow_change
     *
     * @property
     * @var EntityValueWrapper integer
     */
    public $allow_change = null;

    /**
     * Show_passed
     *
     * @property
     * @var EntityValueWrapper integer
     */
    public $show_passed = null;

    /**
     * Mark_doubtful
     *
     * @property
     * @var EntityValueWrapper integer
     */
    public $mark_doubtful = null;

    /**
     * Review_options
     *
     * @property
     * @var EntityValueWrapper text
     */
    public $review_options = null;

    /**
     * Build_on_last
     *
     * @property
     * @var EntityValueWrapper text
     */
    public $build_on_last = null;

    /**
     * @return Drupal\quizz\Entity\QuizController
     */
    public function getControllerClass()
    {
        return entity_get_controller('quiz_entity');
    }


}


/**
 * @fieldable no
 * @configuration no
 * @label Quiz question relationship
 */
class QuizRelationshipQuizRelationshipMetadataWrapper extends EntityDrupalWrapper
{

    /**
     * Quiz question relationship ID
     *
     * The unique ID of the quiz question relationship.
     *
     * @property
     * @var EntityValueWrapper integer
     */
    public $qr_id = null;

    /**
     * Quiz_qid
     *
     * Quiz question relationship "quiz_qid" property.
     *
     * @property
     * @var EntityDrupalWrapper node
     */
    public $quiz_qid = null;

    /**
     * Quiz_vid
     *
     * Quiz question relationship "quiz_vid" property.
     *
     * @property
     * @var EntityValueWrapper integer
     */
    public $quiz_vid = null;

    /**
     * Qr_pid
     *
     * Quiz question relationship "qr_pid" property.
     *
     * @property
     * @var EntityValueWrapper integer
     */
    public $qr_pid = null;

    /**
     * Question_qid
     *
     * Quiz question relationship "question_qid" property.
     *
     * @property
     * @var EntityDrupalWrapper node
     */
    public $question_qid = null;

    /**
     * Question_vid
     *
     * Quiz question relationship "question_vid" property.
     *
     * @property
     * @var EntityValueWrapper integer
     */
    public $question_vid = null;

    /**
     * Question_status
     *
     * Quiz question relationship "question_status" property.
     *
     * @property
     * @var EntityValueWrapper integer
     */
    public $question_status = null;

    /**
     * Weight
     *
     * Quiz question relationship "weight" property.
     *
     * @property
     * @var EntityValueWrapper integer
     */
    public $weight = null;

    /**
     * Max_score
     *
     * Quiz question relationship "max_score" property.
     *
     * @property
     * @var EntityValueWrapper integer
     */
    public $max_score = null;

    /**
     * Auto_update_max_score
     *
     * Quiz question relationship "auto_update_max_score" property.
     *
     * @property
     * @var EntityValueWrapper integer
     */
    public $auto_update_max_score = null;

    /**
     * @return EntityAPIController
     */
    public function getControllerClass()
    {
        return entity_get_controller('quiz_relationship');
    }


}


/**
 * Types of result.
 *
 * @fieldable no
 * @configuration yes
 * @label Result type
 */
class QuizResultTypeQuizResultTypeMetadataWrapper extends EntityDrupalWrapper
{

    /**
     * Internal, numeric result type ID
     *
     * The ID used to identify this result type internally.
     *
     * @property
     * @var EntityValueWrapper integer
     */
    public $id = null;

    /**
     * Machine-readable name
     *
     * The machine-readable name identifying this result type.
     *
     * @property
     * @var EntityValueWrapper token
     */
    public $type = null;

    /**
     * Label
     *
     * The human readable label.
     *
     * @property
     * @var EntityValueWrapper text
     */
    public $label = null;

    /**
     * Weight
     *
     * Result type "weight" property.
     *
     * @property
     * @var EntityValueWrapper integer
     */
    public $weight = null;

    /**
     * Data
     *
     * Result type "data" property.
     *
     * @property
     * @var EntityValueWrapper text
     */
    public $data = null;

    /**
     * Status
     *
     * Result type "status" property.
     *
     * @property
     * @var EntityValueWrapper integer
     */
    public $status = null;

    /**
     * Module
     *
     * Result type "module" property.
     *
     * @property
     * @var EntityValueWrapper text
     */
    public $module = null;

    /**
     * Description
     *
     * Result type "description" property.
     *
     * @property
     * @var EntityValueWrapper text
     */
    public $description = null;

    /**
     * Help
     *
     * Result type "help" property.
     *
     * @property
     * @var EntityValueWrapper text
     */
    public $help = null;

    /**
     * @return Drupal\quizz\Entity\QuizTypeController
     */
    public function getControllerClass()
    {
        return entity_get_controller('quiz_result_type');
    }


}


/**
 * @fieldable yes
 * @configuration no
 * @label Quiz result
 */
class QuizResultQuizMetadataWrapper extends EntityDrupalWrapper
{

    /**
     * Label
     *
     * The human readable label.
     *
     * @property
     * @var EntityValueWrapper integer
     */
    public $result_id = null;

    /**
     * Type
     *
     * Quiz result "type" property.
     *
     * @property
     * @var EntityValueWrapper token
     */
    public $type = null;

    /**
     * Quiz_qid
     *
     * Quiz result "quiz_qid" property.
     *
     * @property
     * @var EntityDrupalWrapper quiz_entity
     */
    public $quiz_qid = null;

    /**
     * Quiz_vid
     *
     * Quiz result "quiz_vid" property.
     *
     * @property
     * @var EntityValueWrapper integer
     */
    public $quiz_vid = null;

    /**
     * Uid
     *
     * Quiz result "uid" property.
     *
     * @property
     * @var EntityDrupalWrapper user
     */
    public $uid = null;

    /**
     * Date started
     *
     * Quiz result "time_start" property.
     *
     * @property
     * @var EntityValueWrapper date
     */
    public $time_start = null;

    /**
     * Date finished
     *
     * Quiz result "time_end" property.
     *
     * @property
     * @var EntityValueWrapper date
     */
    public $time_end = null;

    /**
     * Released
     *
     * Quiz result "released" property.
     *
     * @property
     * @var EntityValueWrapper date
     */
    public $released = null;

    /**
     * Score
     *
     * Quiz result "score" property.
     *
     * @property
     * @var EntityValueWrapper integer
     */
    public $score = null;

    /**
     * Invalid
     *
     * Quiz result "is_invalid" property.
     *
     * @property
     * @var EntityValueWrapper boolean
     */
    public $is_invalid = null;

    /**
     * Evaluated
     *
     * Quiz result "is_evaluated" property.
     *
     * @property
     * @var EntityValueWrapper boolean
     */
    public $is_evaluated = null;

    /**
     * Time_left
     *
     * Quiz result "time_left" property.
     *
     * @property
     * @var EntityValueWrapper duration
     */
    public $time_left = null;

    /**
     * @return Drupal\quizz\Entity\ResultController
     */
    public function getControllerClass()
    {
        return entity_get_controller('quiz_result');
    }


}


/**
 * Types of answer.
 *
 * @fieldable no
 * @configuration yes
 * @label Answer type
 */
class QuizAnswerTypeQuizAnswerTypeMetadataWrapper extends EntityDrupalWrapper
{

    /**
     * Internal, numeric answer type ID
     *
     * The ID used to identify this answer type internally.
     *
     * @property
     * @var EntityValueWrapper integer
     */
    public $id = null;

    /**
     * Machine-readable name
     *
     * The machine-readable name identifying this answer type.
     *
     * @property
     * @var EntityValueWrapper token
     */
    public $type = null;

    /**
     * Handler
     *
     * Answer type "handler" property.
     *
     * @property
     * @var EntityValueWrapper text
     */
    public $handler = null;

    /**
     * Label
     *
     * The human readable label.
     *
     * @property
     * @var EntityValueWrapper text
     */
    public $label = null;

    /**
     * Weight
     *
     * Answer type "weight" property.
     *
     * @property
     * @var EntityValueWrapper integer
     */
    public $weight = null;

    /**
     * Data
     *
     * Answer type "data" property.
     *
     * @property
     * @var EntityValueWrapper text
     */
    public $data = null;

    /**
     * Status
     *
     * Answer type "status" property.
     *
     * @property
     * @var EntityValueWrapper integer
     */
    public $status = null;

    /**
     * Disabled
     *
     * Answer type "disabled" property.
     *
     * @property
     * @var EntityValueWrapper integer
     */
    public $disabled = null;

    /**
     * Module
     *
     * Answer type "module" property.
     *
     * @property
     * @var EntityValueWrapper text
     */
    public $module = null;

    /**
     * Description
     *
     * Answer type "description" property.
     *
     * @property
     * @var EntityValueWrapper text
     */
    public $description = null;

    /**
     * Help
     *
     * Answer type "help" property.
     *
     * @property
     * @var EntityValueWrapper text
     */
    public $help = null;

    /**
     * @return Drupal\quizz_question\Entity\QuestionTypeController
     */
    public function getControllerClass()
    {
        return entity_get_controller('quiz_answer_type');
    }


}


/**
 * @fieldable yes
 * @configuration no
 * @label Quiz result answer
 */
class QuizResultAnswerDefaultQuizDirectionsMetadataWrapper extends EntityDrupalWrapper
{

    /**
     * Label
     *
     * The human readable label.
     *
     * @property
     * @var EntityValueWrapper integer
     */
    public $id = null;

    /**
     * Result_id
     *
     * Quiz result answer "result_id" property.
     *
     * @property
     * @var EntityDrupalWrapper quiz_result
     */
    public $result_id = null;

    /**
     * Question_qid
     *
     * Quiz result answer "question_qid" property.
     *
     * @property
     * @var EntityDrupalWrapper node
     */
    public $question_qid = null;

    /**
     * Question_vid
     *
     * Quiz result answer "question_vid" property.
     *
     * @property
     * @var EntityValueWrapper integer
     */
    public $question_vid = null;

    /**
     * Tid
     *
     * Quiz result answer "tid" property.
     *
     * @property
     * @var EntityValueWrapper integer
     */
    public $tid = null;

    /**
     * Is_correct
     *
     * Quiz result answer "is_correct" property.
     *
     * @property
     * @var EntityValueWrapper boolean
     */
    public $is_correct = null;

    /**
     * Is_skipped
     *
     * Quiz result answer "is_skipped" property.
     *
     * @property
     * @var EntityValueWrapper boolean
     */
    public $is_skipped = null;

    /**
     * Points_awarded
     *
     * Quiz result answer "points_awarded" property.
     *
     * @property
     * @var EntityValueWrapper integer
     */
    public $points_awarded = null;

    /**
     * Answer_timestamp
     *
     * Quiz result answer "answer_timestamp" property.
     *
     * @property
     * @var EntityValueWrapper date
     */
    public $answer_timestamp = null;

    /**
     * Number
     *
     * Quiz result answer "number" property.
     *
     * @property
     * @var EntityValueWrapper integer
     */
    public $number = null;

    /**
     * Is_doubtful
     *
     * Quiz result answer "is_doubtful" property.
     *
     * @property
     * @var EntityValueWrapper boolean
     */
    public $is_doubtful = null;

    /**
     * Question type
     *
     * @property
     * @var EntityValueWrapper token
     */
    public $type = null;

    /**
     * @return Drupal\quizz\Entity\AnswerController
     */
    public function getControllerClass()
    {
        return entity_get_controller('quiz_result_answer');
    }


}


/**
 * @fieldable yes
 * @configuration no
 * @label Quiz result answer
 */
class QuizResultAnswerDefaultQuizPageMetadataWrapper extends EntityDrupalWrapper
{

    /**
     * Label
     *
     * The human readable label.
     *
     * @property
     * @var EntityValueWrapper integer
     */
    public $id = null;

    /**
     * Result_id
     *
     * Quiz result answer "result_id" property.
     *
     * @property
     * @var EntityDrupalWrapper quiz_result
     */
    public $result_id = null;

    /**
     * Question_qid
     *
     * Quiz result answer "question_qid" property.
     *
     * @property
     * @var EntityDrupalWrapper node
     */
    public $question_qid = null;

    /**
     * Question_vid
     *
     * Quiz result answer "question_vid" property.
     *
     * @property
     * @var EntityValueWrapper integer
     */
    public $question_vid = null;

    /**
     * Tid
     *
     * Quiz result answer "tid" property.
     *
     * @property
     * @var EntityValueWrapper integer
     */
    public $tid = null;

    /**
     * Is_correct
     *
     * Quiz result answer "is_correct" property.
     *
     * @property
     * @var EntityValueWrapper boolean
     */
    public $is_correct = null;

    /**
     * Is_skipped
     *
     * Quiz result answer "is_skipped" property.
     *
     * @property
     * @var EntityValueWrapper boolean
     */
    public $is_skipped = null;

    /**
     * Points_awarded
     *
     * Quiz result answer "points_awarded" property.
     *
     * @property
     * @var EntityValueWrapper integer
     */
    public $points_awarded = null;

    /**
     * Answer_timestamp
     *
     * Quiz result answer "answer_timestamp" property.
     *
     * @property
     * @var EntityValueWrapper date
     */
    public $answer_timestamp = null;

    /**
     * Number
     *
     * Quiz result answer "number" property.
     *
     * @property
     * @var EntityValueWrapper integer
     */
    public $number = null;

    /**
     * Is_doubtful
     *
     * Quiz result answer "is_doubtful" property.
     *
     * @property
     * @var EntityValueWrapper boolean
     */
    public $is_doubtful = null;

    /**
     * Question type
     *
     * @property
     * @var EntityValueWrapper token
     */
    public $type = null;

    /**
     * @return Drupal\quizz\Entity\AnswerController
     */
    public function getControllerClass()
    {
        return entity_get_controller('quiz_result_answer');
    }


}


/**
 * @fieldable yes
 * @configuration no
 * @label Quiz result answer
 */
class QuizResultAnswerDefaultQuizDdlinesMetadataWrapper extends EntityDrupalWrapper
{

    /**
     * Label
     *
     * The human readable label.
     *
     * @property
     * @var EntityValueWrapper integer
     */
    public $id = null;

    /**
     * Result_id
     *
     * Quiz result answer "result_id" property.
     *
     * @property
     * @var EntityDrupalWrapper quiz_result
     */
    public $result_id = null;

    /**
     * Question_qid
     *
     * Quiz result answer "question_qid" property.
     *
     * @property
     * @var EntityDrupalWrapper node
     */
    public $question_qid = null;

    /**
     * Question_vid
     *
     * Quiz result answer "question_vid" property.
     *
     * @property
     * @var EntityValueWrapper integer
     */
    public $question_vid = null;

    /**
     * Tid
     *
     * Quiz result answer "tid" property.
     *
     * @property
     * @var EntityValueWrapper integer
     */
    public $tid = null;

    /**
     * Is_correct
     *
     * Quiz result answer "is_correct" property.
     *
     * @property
     * @var EntityValueWrapper boolean
     */
    public $is_correct = null;

    /**
     * Is_skipped
     *
     * Quiz result answer "is_skipped" property.
     *
     * @property
     * @var EntityValueWrapper boolean
     */
    public $is_skipped = null;

    /**
     * Points_awarded
     *
     * Quiz result answer "points_awarded" property.
     *
     * @property
     * @var EntityValueWrapper integer
     */
    public $points_awarded = null;

    /**
     * Answer_timestamp
     *
     * Quiz result answer "answer_timestamp" property.
     *
     * @property
     * @var EntityValueWrapper date
     */
    public $answer_timestamp = null;

    /**
     * Number
     *
     * Quiz result answer "number" property.
     *
     * @property
     * @var EntityValueWrapper integer
     */
    public $number = null;

    /**
     * Is_doubtful
     *
     * Quiz result answer "is_doubtful" property.
     *
     * @property
     * @var EntityValueWrapper boolean
     */
    public $is_doubtful = null;

    /**
     * Question type
     *
     * @property
     * @var EntityValueWrapper token
     */
    public $type = null;

    /**
     * @return Drupal\quizz\Entity\AnswerController
     */
    public function getControllerClass()
    {
        return entity_get_controller('quiz_result_answer');
    }


}


/**
 * @fieldable yes
 * @configuration no
 * @label Quiz result answer
 */
class QuizResultAnswerDefaultScaleMetadataWrapper extends EntityDrupalWrapper
{

    /**
     * Label
     *
     * The human readable label.
     *
     * @property
     * @var EntityValueWrapper integer
     */
    public $id = null;

    /**
     * Result_id
     *
     * Quiz result answer "result_id" property.
     *
     * @property
     * @var EntityDrupalWrapper quiz_result
     */
    public $result_id = null;

    /**
     * Question_qid
     *
     * Quiz result answer "question_qid" property.
     *
     * @property
     * @var EntityDrupalWrapper node
     */
    public $question_qid = null;

    /**
     * Question_vid
     *
     * Quiz result answer "question_vid" property.
     *
     * @property
     * @var EntityValueWrapper integer
     */
    public $question_vid = null;

    /**
     * Tid
     *
     * Quiz result answer "tid" property.
     *
     * @property
     * @var EntityValueWrapper integer
     */
    public $tid = null;

    /**
     * Is_correct
     *
     * Quiz result answer "is_correct" property.
     *
     * @property
     * @var EntityValueWrapper boolean
     */
    public $is_correct = null;

    /**
     * Is_skipped
     *
     * Quiz result answer "is_skipped" property.
     *
     * @property
     * @var EntityValueWrapper boolean
     */
    public $is_skipped = null;

    /**
     * Points_awarded
     *
     * Quiz result answer "points_awarded" property.
     *
     * @property
     * @var EntityValueWrapper integer
     */
    public $points_awarded = null;

    /**
     * Answer_timestamp
     *
     * Quiz result answer "answer_timestamp" property.
     *
     * @property
     * @var EntityValueWrapper date
     */
    public $answer_timestamp = null;

    /**
     * Number
     *
     * Quiz result answer "number" property.
     *
     * @property
     * @var EntityValueWrapper integer
     */
    public $number = null;

    /**
     * Is_doubtful
     *
     * Quiz result answer "is_doubtful" property.
     *
     * @property
     * @var EntityValueWrapper boolean
     */
    public $is_doubtful = null;

    /**
     * Question type
     *
     * @property
     * @var EntityValueWrapper token
     */
    public $type = null;

    /**
     * @return Drupal\quizz\Entity\AnswerController
     */
    public function getControllerClass()
    {
        return entity_get_controller('quiz_result_answer');
    }


}


/**
 * @fieldable yes
 * @configuration no
 * @label Quiz result answer
 */
class QuizResultAnswerDefaultShortAnswerMetadataWrapper extends EntityDrupalWrapper
{

    /**
     * Label
     *
     * The human readable label.
     *
     * @property
     * @var EntityValueWrapper integer
     */
    public $id = null;

    /**
     * Result_id
     *
     * Quiz result answer "result_id" property.
     *
     * @property
     * @var EntityDrupalWrapper quiz_result
     */
    public $result_id = null;

    /**
     * Question_qid
     *
     * Quiz result answer "question_qid" property.
     *
     * @property
     * @var EntityDrupalWrapper node
     */
    public $question_qid = null;

    /**
     * Question_vid
     *
     * Quiz result answer "question_vid" property.
     *
     * @property
     * @var EntityValueWrapper integer
     */
    public $question_vid = null;

    /**
     * Tid
     *
     * Quiz result answer "tid" property.
     *
     * @property
     * @var EntityValueWrapper integer
     */
    public $tid = null;

    /**
     * Is_correct
     *
     * Quiz result answer "is_correct" property.
     *
     * @property
     * @var EntityValueWrapper boolean
     */
    public $is_correct = null;

    /**
     * Is_skipped
     *
     * Quiz result answer "is_skipped" property.
     *
     * @property
     * @var EntityValueWrapper boolean
     */
    public $is_skipped = null;

    /**
     * Points_awarded
     *
     * Quiz result answer "points_awarded" property.
     *
     * @property
     * @var EntityValueWrapper integer
     */
    public $points_awarded = null;

    /**
     * Answer_timestamp
     *
     * Quiz result answer "answer_timestamp" property.
     *
     * @property
     * @var EntityValueWrapper date
     */
    public $answer_timestamp = null;

    /**
     * Number
     *
     * Quiz result answer "number" property.
     *
     * @property
     * @var EntityValueWrapper integer
     */
    public $number = null;

    /**
     * Is_doubtful
     *
     * Quiz result answer "is_doubtful" property.
     *
     * @property
     * @var EntityValueWrapper boolean
     */
    public $is_doubtful = null;

    /**
     * Question type
     *
     * @property
     * @var EntityValueWrapper token
     */
    public $type = null;

    /**
     * @return Drupal\quizz\Entity\AnswerController
     */
    public function getControllerClass()
    {
        return entity_get_controller('quiz_result_answer');
    }


}


/**
 * @fieldable yes
 * @configuration no
 * @label Quiz result answer
 */
class QuizResultAnswerDefaultLongAnswerMetadataWrapper extends EntityDrupalWrapper
{

    /**
     * Label
     *
     * The human readable label.
     *
     * @property
     * @var EntityValueWrapper integer
     */
    public $id = null;

    /**
     * Result_id
     *
     * Quiz result answer "result_id" property.
     *
     * @property
     * @var EntityDrupalWrapper quiz_result
     */
    public $result_id = null;

    /**
     * Question_qid
     *
     * Quiz result answer "question_qid" property.
     *
     * @property
     * @var EntityDrupalWrapper node
     */
    public $question_qid = null;

    /**
     * Question_vid
     *
     * Quiz result answer "question_vid" property.
     *
     * @property
     * @var EntityValueWrapper integer
     */
    public $question_vid = null;

    /**
     * Tid
     *
     * Quiz result answer "tid" property.
     *
     * @property
     * @var EntityValueWrapper integer
     */
    public $tid = null;

    /**
     * Is_correct
     *
     * Quiz result answer "is_correct" property.
     *
     * @property
     * @var EntityValueWrapper boolean
     */
    public $is_correct = null;

    /**
     * Is_skipped
     *
     * Quiz result answer "is_skipped" property.
     *
     * @property
     * @var EntityValueWrapper boolean
     */
    public $is_skipped = null;

    /**
     * Points_awarded
     *
     * Quiz result answer "points_awarded" property.
     *
     * @property
     * @var EntityValueWrapper integer
     */
    public $points_awarded = null;

    /**
     * Answer_timestamp
     *
     * Quiz result answer "answer_timestamp" property.
     *
     * @property
     * @var EntityValueWrapper date
     */
    public $answer_timestamp = null;

    /**
     * Number
     *
     * Quiz result answer "number" property.
     *
     * @property
     * @var EntityValueWrapper integer
     */
    public $number = null;

    /**
     * Is_doubtful
     *
     * Quiz result answer "is_doubtful" property.
     *
     * @property
     * @var EntityValueWrapper boolean
     */
    public $is_doubtful = null;

    /**
     * Question type
     *
     * @property
     * @var EntityValueWrapper token
     */
    public $type = null;

    /**
     * @return Drupal\quizz\Entity\AnswerController
     */
    public function getControllerClass()
    {
        return entity_get_controller('quiz_result_answer');
    }


}


/**
 * @fieldable yes
 * @configuration no
 * @label Quiz result answer
 */
class QuizResultAnswerDefaultTruefalseMetadataWrapper extends EntityDrupalWrapper
{

    /**
     * Label
     *
     * The human readable label.
     *
     * @property
     * @var EntityValueWrapper integer
     */
    public $id = null;

    /**
     * Result_id
     *
     * Quiz result answer "result_id" property.
     *
     * @property
     * @var EntityDrupalWrapper quiz_result
     */
    public $result_id = null;

    /**
     * Question_qid
     *
     * Quiz result answer "question_qid" property.
     *
     * @property
     * @var EntityDrupalWrapper node
     */
    public $question_qid = null;

    /**
     * Question_vid
     *
     * Quiz result answer "question_vid" property.
     *
     * @property
     * @var EntityValueWrapper integer
     */
    public $question_vid = null;

    /**
     * Tid
     *
     * Quiz result answer "tid" property.
     *
     * @property
     * @var EntityValueWrapper integer
     */
    public $tid = null;

    /**
     * Is_correct
     *
     * Quiz result answer "is_correct" property.
     *
     * @property
     * @var EntityValueWrapper boolean
     */
    public $is_correct = null;

    /**
     * Is_skipped
     *
     * Quiz result answer "is_skipped" property.
     *
     * @property
     * @var EntityValueWrapper boolean
     */
    public $is_skipped = null;

    /**
     * Points_awarded
     *
     * Quiz result answer "points_awarded" property.
     *
     * @property
     * @var EntityValueWrapper integer
     */
    public $points_awarded = null;

    /**
     * Answer_timestamp
     *
     * Quiz result answer "answer_timestamp" property.
     *
     * @property
     * @var EntityValueWrapper date
     */
    public $answer_timestamp = null;

    /**
     * Number
     *
     * Quiz result answer "number" property.
     *
     * @property
     * @var EntityValueWrapper integer
     */
    public $number = null;

    /**
     * Is_doubtful
     *
     * Quiz result answer "is_doubtful" property.
     *
     * @property
     * @var EntityValueWrapper boolean
     */
    public $is_doubtful = null;

    /**
     * Question type
     *
     * @property
     * @var EntityValueWrapper token
     */
    public $type = null;

    /**
     * @return Drupal\quizz\Entity\AnswerController
     */
    public function getControllerClass()
    {
        return entity_get_controller('quiz_result_answer');
    }


}


/**
 * Types of question entity.
 *
 * @fieldable no
 * @configuration yes
 * @label Question type
 */
class QuizQuestionTypeQuizQuestionTypeMetadataWrapper extends EntityDrupalWrapper
{

    /**
     * Internal, numeric question type ID
     *
     * The ID used to identify this question type internally.
     *
     * @property
     * @var EntityValueWrapper integer
     */
    public $id = null;

    /**
     * Machine-readable name
     *
     * The machine-readable name identifying this question type.
     *
     * @property
     * @var EntityValueWrapper token
     */
    public $type = null;

    /**
     * Handler
     *
     * Question type "handler" property.
     *
     * @property
     * @var EntityValueWrapper text
     */
    public $handler = null;

    /**
     * Label
     *
     * The human readable label.
     *
     * @property
     * @var EntityValueWrapper text
     */
    public $label = null;

    /**
     * Weight
     *
     * Question type "weight" property.
     *
     * @property
     * @var EntityValueWrapper integer
     */
    public $weight = null;

    /**
     * Data
     *
     * Question type "data" property.
     *
     * @property
     * @var EntityValueWrapper text
     */
    public $data = null;

    /**
     * Status
     *
     * Question type "status" property.
     *
     * @property
     * @var EntityValueWrapper integer
     */
    public $status = null;

    /**
     * Disabled
     *
     * Question type "disabled" property.
     *
     * @property
     * @var EntityValueWrapper integer
     */
    public $disabled = null;

    /**
     * Module
     *
     * Question type "module" property.
     *
     * @property
     * @var EntityValueWrapper text
     */
    public $module = null;

    /**
     * Description
     *
     * Question type "description" property.
     *
     * @property
     * @var EntityValueWrapper text
     */
    public $description = null;

    /**
     * Help
     *
     * Question type "help" property.
     *
     * @property
     * @var EntityValueWrapper text
     */
    public $help = null;

    /**
     * @return Drupal\quizz_question\Entity\QuestionTypeController
     */
    public function getControllerClass()
    {
        return entity_get_controller('quiz_question_type');
    }


}


/**
 * Quiz question entity
 *
 * @fieldable yes
 * @configuration no
 * @label Question
 */
class QuizQuestionDefaultQuizDirectionsMetadataWrapper extends EntityDrupalWrapper
{

    /**
     * Question ID
     *
     * The unique ID of the question.
     *
     * @property
     * @var EntityValueWrapper integer
     */
    public $qid = null;

    /**
     * Vid
     *
     * Question "vid" property.
     *
     * @property
     * @var EntityValueWrapper integer
     */
    public $vid = null;

    /**
     * Type
     *
     * Question "type" property.
     *
     * @property
     * @var EntityValueWrapper token
     */
    public $type = null;

    /**
     * Language
     *
     * Question "language" property.
     *
     * @property
     * @var EntityValueWrapper text
     */
    public $language = null;

    /**
     * Status
     *
     * Question "status" property.
     *
     * @property
     * @var EntityValueWrapper integer
     */
    public $status = null;

    /**
     * Label
     *
     * The human readable label.
     *
     * @property
     * @var EntityValueWrapper text
     */
    public $title = null;

    /**
     * Created
     *
     * Question "created" property.
     *
     * @property
     * @var EntityValueWrapper date
     */
    public $created = null;

    /**
     * Changed
     *
     * Question "changed" property.
     *
     * @property
     * @var EntityValueWrapper date
     */
    public $changed = null;

    /**
     * Uid
     *
     * Question "uid" property.
     *
     * @property
     * @var EntityDrupalWrapper user
     */
    public $uid = null;

    /**
     * URL
     *
     * The URL of the entity.
     *
     * @property
     * @var EntityValueWrapper uri
     */
    public $url = null;

    /**
     * @return Drupal\quizz_question\Entity\QuestionController
     */
    public function getControllerClass()
    {
        return entity_get_controller('quiz_question_entity');
    }


}


/**
 * Quiz question entity
 *
 * @fieldable yes
 * @configuration no
 * @label Question
 */
class QuizQuestionDefaultQuizPageMetadataWrapper extends EntityDrupalWrapper
{

    /**
     * Question ID
     *
     * The unique ID of the question.
     *
     * @property
     * @var EntityValueWrapper integer
     */
    public $qid = null;

    /**
     * Vid
     *
     * Question "vid" property.
     *
     * @property
     * @var EntityValueWrapper integer
     */
    public $vid = null;

    /**
     * Type
     *
     * Question "type" property.
     *
     * @property
     * @var EntityValueWrapper token
     */
    public $type = null;

    /**
     * Language
     *
     * Question "language" property.
     *
     * @property
     * @var EntityValueWrapper text
     */
    public $language = null;

    /**
     * Status
     *
     * Question "status" property.
     *
     * @property
     * @var EntityValueWrapper integer
     */
    public $status = null;

    /**
     * Label
     *
     * The human readable label.
     *
     * @property
     * @var EntityValueWrapper text
     */
    public $title = null;

    /**
     * Created
     *
     * Question "created" property.
     *
     * @property
     * @var EntityValueWrapper date
     */
    public $created = null;

    /**
     * Changed
     *
     * Question "changed" property.
     *
     * @property
     * @var EntityValueWrapper date
     */
    public $changed = null;

    /**
     * Uid
     *
     * Question "uid" property.
     *
     * @property
     * @var EntityDrupalWrapper user
     */
    public $uid = null;

    /**
     * URL
     *
     * The URL of the entity.
     *
     * @property
     * @var EntityValueWrapper uri
     */
    public $url = null;

    /**
     * @return Drupal\quizz_question\Entity\QuestionController
     */
    public function getControllerClass()
    {
        return entity_get_controller('quiz_question_entity');
    }


}


/**
 * Quiz question entity
 *
 * @fieldable yes
 * @configuration no
 * @label Question
 */
class QuizQuestionDefaultQuizDdlinesMetadataWrapper extends EntityDrupalWrapper
{

    /**
     * Question ID
     *
     * The unique ID of the question.
     *
     * @property
     * @var EntityValueWrapper integer
     */
    public $qid = null;

    /**
     * Vid
     *
     * Question "vid" property.
     *
     * @property
     * @var EntityValueWrapper integer
     */
    public $vid = null;

    /**
     * Type
     *
     * Question "type" property.
     *
     * @property
     * @var EntityValueWrapper token
     */
    public $type = null;

    /**
     * Language
     *
     * Question "language" property.
     *
     * @property
     * @var EntityValueWrapper text
     */
    public $language = null;

    /**
     * Status
     *
     * Question "status" property.
     *
     * @property
     * @var EntityValueWrapper integer
     */
    public $status = null;

    /**
     * Label
     *
     * The human readable label.
     *
     * @property
     * @var EntityValueWrapper text
     */
    public $title = null;

    /**
     * Created
     *
     * Question "created" property.
     *
     * @property
     * @var EntityValueWrapper date
     */
    public $created = null;

    /**
     * Changed
     *
     * Question "changed" property.
     *
     * @property
     * @var EntityValueWrapper date
     */
    public $changed = null;

    /**
     * Uid
     *
     * Question "uid" property.
     *
     * @property
     * @var EntityDrupalWrapper user
     */
    public $uid = null;

    /**
     * URL
     *
     * The URL of the entity.
     *
     * @property
     * @var EntityValueWrapper uri
     */
    public $url = null;

    /**
     * @return Drupal\quizz_question\Entity\QuestionController
     */
    public function getControllerClass()
    {
        return entity_get_controller('quiz_question_entity');
    }


}


/**
 * Quiz question entity
 *
 * @fieldable yes
 * @configuration no
 * @label Question
 */
class QuizQuestionDefaultScaleMetadataWrapper extends EntityDrupalWrapper
{

    /**
     * Question ID
     *
     * The unique ID of the question.
     *
     * @property
     * @var EntityValueWrapper integer
     */
    public $qid = null;

    /**
     * Vid
     *
     * Question "vid" property.
     *
     * @property
     * @var EntityValueWrapper integer
     */
    public $vid = null;

    /**
     * Type
     *
     * Question "type" property.
     *
     * @property
     * @var EntityValueWrapper token
     */
    public $type = null;

    /**
     * Language
     *
     * Question "language" property.
     *
     * @property
     * @var EntityValueWrapper text
     */
    public $language = null;

    /**
     * Status
     *
     * Question "status" property.
     *
     * @property
     * @var EntityValueWrapper integer
     */
    public $status = null;

    /**
     * Label
     *
     * The human readable label.
     *
     * @property
     * @var EntityValueWrapper text
     */
    public $title = null;

    /**
     * Created
     *
     * Question "created" property.
     *
     * @property
     * @var EntityValueWrapper date
     */
    public $created = null;

    /**
     * Changed
     *
     * Question "changed" property.
     *
     * @property
     * @var EntityValueWrapper date
     */
    public $changed = null;

    /**
     * Uid
     *
     * Question "uid" property.
     *
     * @property
     * @var EntityDrupalWrapper user
     */
    public $uid = null;

    /**
     * URL
     *
     * The URL of the entity.
     *
     * @property
     * @var EntityValueWrapper uri
     */
    public $url = null;

    /**
     * @return Drupal\quizz_question\Entity\QuestionController
     */
    public function getControllerClass()
    {
        return entity_get_controller('quiz_question_entity');
    }


}


/**
 * Quiz question entity
 *
 * @fieldable yes
 * @configuration no
 * @label Question
 */
class QuizQuestionDefaultShortAnswerMetadataWrapper extends EntityDrupalWrapper
{

    /**
     * Question ID
     *
     * The unique ID of the question.
     *
     * @property
     * @var EntityValueWrapper integer
     */
    public $qid = null;

    /**
     * Vid
     *
     * Question "vid" property.
     *
     * @property
     * @var EntityValueWrapper integer
     */
    public $vid = null;

    /**
     * Type
     *
     * Question "type" property.
     *
     * @property
     * @var EntityValueWrapper token
     */
    public $type = null;

    /**
     * Language
     *
     * Question "language" property.
     *
     * @property
     * @var EntityValueWrapper text
     */
    public $language = null;

    /**
     * Status
     *
     * Question "status" property.
     *
     * @property
     * @var EntityValueWrapper integer
     */
    public $status = null;

    /**
     * Label
     *
     * The human readable label.
     *
     * @property
     * @var EntityValueWrapper text
     */
    public $title = null;

    /**
     * Created
     *
     * Question "created" property.
     *
     * @property
     * @var EntityValueWrapper date
     */
    public $created = null;

    /**
     * Changed
     *
     * Question "changed" property.
     *
     * @property
     * @var EntityValueWrapper date
     */
    public $changed = null;

    /**
     * Uid
     *
     * Question "uid" property.
     *
     * @property
     * @var EntityDrupalWrapper user
     */
    public $uid = null;

    /**
     * URL
     *
     * The URL of the entity.
     *
     * @property
     * @var EntityValueWrapper uri
     */
    public $url = null;

    /**
     * @return Drupal\quizz_question\Entity\QuestionController
     */
    public function getControllerClass()
    {
        return entity_get_controller('quiz_question_entity');
    }


}


/**
 * Quiz question entity
 *
 * @fieldable yes
 * @configuration no
 * @label Question
 */
class QuizQuestionDefaultLongAnswerMetadataWrapper extends EntityDrupalWrapper
{

    /**
     * Question ID
     *
     * The unique ID of the question.
     *
     * @property
     * @var EntityValueWrapper integer
     */
    public $qid = null;

    /**
     * Vid
     *
     * Question "vid" property.
     *
     * @property
     * @var EntityValueWrapper integer
     */
    public $vid = null;

    /**
     * Type
     *
     * Question "type" property.
     *
     * @property
     * @var EntityValueWrapper token
     */
    public $type = null;

    /**
     * Language
     *
     * Question "language" property.
     *
     * @property
     * @var EntityValueWrapper text
     */
    public $language = null;

    /**
     * Status
     *
     * Question "status" property.
     *
     * @property
     * @var EntityValueWrapper integer
     */
    public $status = null;

    /**
     * Label
     *
     * The human readable label.
     *
     * @property
     * @var EntityValueWrapper text
     */
    public $title = null;

    /**
     * Created
     *
     * Question "created" property.
     *
     * @property
     * @var EntityValueWrapper date
     */
    public $created = null;

    /**
     * Changed
     *
     * Question "changed" property.
     *
     * @property
     * @var EntityValueWrapper date
     */
    public $changed = null;

    /**
     * Uid
     *
     * Question "uid" property.
     *
     * @property
     * @var EntityDrupalWrapper user
     */
    public $uid = null;

    /**
     * URL
     *
     * The URL of the entity.
     *
     * @property
     * @var EntityValueWrapper uri
     */
    public $url = null;

    /**
     * @return Drupal\quizz_question\Entity\QuestionController
     */
    public function getControllerClass()
    {
        return entity_get_controller('quiz_question_entity');
    }


}


/**
 * Quiz question entity
 *
 * @fieldable yes
 * @configuration no
 * @label Question
 */
class QuizQuestionDefaultTruefalseMetadataWrapper extends EntityDrupalWrapper
{

    /**
     * Question ID
     *
     * The unique ID of the question.
     *
     * @property
     * @var EntityValueWrapper integer
     */
    public $qid = null;

    /**
     * Vid
     *
     * Question "vid" property.
     *
     * @property
     * @var EntityValueWrapper integer
     */
    public $vid = null;

    /**
     * Type
     *
     * Question "type" property.
     *
     * @property
     * @var EntityValueWrapper token
     */
    public $type = null;

    /**
     * Language
     *
     * Question "language" property.
     *
     * @property
     * @var EntityValueWrapper text
     */
    public $language = null;

    /**
     * Status
     *
     * Question "status" property.
     *
     * @property
     * @var EntityValueWrapper integer
     */
    public $status = null;

    /**
     * Label
     *
     * The human readable label.
     *
     * @property
     * @var EntityValueWrapper text
     */
    public $title = null;

    /**
     * Created
     *
     * Question "created" property.
     *
     * @property
     * @var EntityValueWrapper date
     */
    public $created = null;

    /**
     * Changed
     *
     * Question "changed" property.
     *
     * @property
     * @var EntityValueWrapper date
     */
    public $changed = null;

    /**
     * Uid
     *
     * Question "uid" property.
     *
     * @property
     * @var EntityDrupalWrapper user
     */
    public $uid = null;

    /**
     * URL
     *
     * The URL of the entity.
     *
     * @property
     * @var EntityValueWrapper uri
     */
    public $url = null;

    /**
     * @return Drupal\quizz_question\Entity\QuestionController
     */
    public function getControllerClass()
    {
        return entity_get_controller('quiz_question_entity');
    }


}


/**
 * Scale collections
 *
 * @fieldable no
 * @configuration yes
 * @label Collection
 */
class ScaleCollectionScaleCollectionMetadataWrapper extends EntityDrupalWrapper
{

    /**
     * Internal, numeric collection ID
     *
     * The ID used to identify this collection internally.
     *
     * @property
     * @var EntityValueWrapper integer
     */
    public $id = null;

    /**
     * Question_type
     *
     * Collection "question_type" property.
     *
     * @property
     * @var EntityValueWrapper text
     */
    public $question_type = null;

    /**
     * Machine-readable name
     *
     * The machine-readable name identifying this collection.
     *
     * @property
     * @var EntityValueWrapper token
     */
    public $name = null;

    /**
     * Label
     *
     * The human readable label.
     *
     * @property
     * @var EntityValueWrapper text
     */
    public $label = null;

    /**
     * Uid
     *
     * Collection "uid" property.
     *
     * @property
     * @var EntityValueWrapper integer
     */
    public $uid = null;

    /**
     * For_all
     *
     * Collection "for_all" property.
     *
     * @property
     * @var EntityValueWrapper integer
     */
    public $for_all = null;

    /**
     * Data
     *
     * Collection "data" property.
     *
     * @property
     * @var EntityValueWrapper text
     */
    public $data = null;

    /**
     * Module
     *
     * Collection "module" property.
     *
     * @property
     * @var EntityValueWrapper text
     */
    public $module = null;

    /**
     * Status
     *
     * Collection "status" property.
     *
     * @property
     * @var EntityValueWrapper integer
     */
    public $status = null;

    /**
     * @return Drupal\quizz_scale\Entity\CollectionController
     */
    public function getControllerClass()
    {
        return entity_get_controller('scale_collection');
    }


}


/**
 * Uploaded file.
 *
 * @fieldable no
 * @configuration no
 * @label File
 */
class FileFileMetadataWrapper extends EntityDrupalWrapper
{

    /**
     * File ID
     *
     * The unique ID of the uploaded file.
     *
     * @property
     * @var EntityValueWrapper integer
     */
    public $fid = null;

    /**
     * File name
     *
     * The name of the file on disk.
     *
     * @property
     */
    public $name = null;

    /**
     * MIME type
     *
     * The MIME type of the file.
     *
     * @property
     */
    public $mime = null;

    /**
     * File size
     *
     * The size of the file, in kilobytes.
     *
     * @property
     * @var EntityValueWrapper integer
     */
    public $size = null;

    /**
     * URL
     *
     * The web-accessible URL for the file.
     *
     * @property
     */
    public $url = null;

    /**
     * Timestamp
     *
     * The date the file was most recently changed.
     *
     * @property
     * @var EntityValueWrapper date
     */
    public $timestamp = null;

    /**
     * Owner
     *
     * The user who originally uploaded the file.
     *
     * @property
     * @var EntityDrupalWrapper user
     */
    public $owner = null;

    /**
     * @return DrupalDefaultEntityController
     */
    public function getControllerClass()
    {
        return entity_get_controller('file');
    }


}


/**
 * Taxonomy terms are used for classifying content.
 *
 * @fieldable yes
 * @configuration no
 * @label Taxonomy term
 */
class TaxonomyTermTagsMetadataWrapper extends EntityDrupalWrapper
{

    /**
     * Term ID
     *
     * The unique ID of the taxonomy term.
     *
     * @property
     * @var EntityValueWrapper integer
     */
    public $tid = null;

    /**
     * Name
     *
     * The name of the taxonomy term.
     *
     * @property
     * @required
     */
    public $name = null;

    /**
     * Description
     *
     * The optional description of the taxonomy term.
     *
     * @property
     */
    public $description = null;

    /**
     * Weight
     *
     * The weight of the term, which is used for ordering terms during display.
     *
     * @property
     * @var EntityValueWrapper integer
     */
    public $weight = null;

    /**
     * Node count
     *
     * The number of nodes tagged with the taxonomy term.
     *
     * @property
     * @var EntityValueWrapper integer
     */
    public $node_count = null;

    /**
     * URL
     *
     * The URL of the taxonomy term.
     *
     * @property
     * @var EntityValueWrapper uri
     */
    public $url = null;

    /**
     * Vocabulary
     *
     * The vocabulary the taxonomy term belongs to.
     *
     * @property
     * @var EntityDrupalWrapper taxonomy_vocabulary
     * @required
     */
    public $vocabulary = null;

    /**
     * Parent terms
     *
     * The parent terms of the taxonomy term.
     *
     * @property
     * @var EntityListWrapper list<taxonomy_term>
     */
    public $parent = null;

    /**
     * All parent terms
     *
     * Ancestors of the term, i.e. parent of all above hierarchy levels.
     *
     * @property
     * @var EntityListWrapper list<taxonomy_term>
     */
    public $parents_all = null;

    /**
     * @return TaxonomyTermController
     */
    public function getControllerClass()
    {
        return entity_get_controller('taxonomy_term');
    }


}


/**
 * Vocabularies contain related taxonomy terms, which are used for classifying
 * content.
 *
 * @fieldable no
 * @configuration no
 * @label Taxonomy vocabulary
 */
class TaxonomyVocabularyTaxonomyVocabularyMetadataWrapper extends EntityDrupalWrapper
{

    /**
     * Vocabulary ID
     *
     * The unique ID of the taxonomy vocabulary.
     *
     * @property
     * @var EntityValueWrapper integer
     */
    public $vid = null;

    /**
     * Name
     *
     * The name of the taxonomy vocabulary.
     *
     * @property
     * @required
     */
    public $name = null;

    /**
     * Machine name
     *
     * The machine name of the taxonomy vocabulary.
     *
     * @property
     * @var EntityValueWrapper token
     * @required
     */
    public $machine_name = null;

    /**
     * Description
     *
     * The optional description of the taxonomy vocabulary.
     *
     * @property
     */
    public $description = null;

    /**
     * Term count
     *
     * The number of terms belonging to the taxonomy vocabulary.
     *
     * @property
     * @var EntityValueWrapper integer
     */
    public $term_count = null;

    /**
     * @return TaxonomyVocabularyController
     */
    public function getControllerClass()
    {
        return entity_get_controller('taxonomy_vocabulary');
    }


}


/**
 * Users who have created accounts on your site.
 *
 * @fieldable yes
 * @configuration no
 * @label User
 */
class UserUserMetadataWrapper extends EntityDrupalWrapper
{

    /**
     * User ID
     *
     * The unique ID of the user account.
     *
     * @property
     * @var EntityValueWrapper integer
     */
    public $uid = null;

    /**
     * Name
     *
     * The login name of the user account.
     *
     * @property
     * @required
     */
    public $name = null;

    /**
     * Email
     *
     * The email address of the user account.
     *
     * @property
     * @required
     */
    public $mail = null;

    /**
     * URL
     *
     * The URL of the account profile page.
     *
     * @property
     * @var EntityValueWrapper uri
     */
    public $url = null;

    /**
     * Edit URL
     *
     * The url of the account edit page.
     *
     * @property
     * @var EntityValueWrapper uri
     */
    public $edit_url = null;

    /**
     * Last access
     *
     * The date the user last accessed the site.
     *
     * @property
     * @var EntityValueWrapper date
     */
    public $last_access = null;

    /**
     * Last login
     *
     * The date the user last logged in to the site.
     *
     * @property
     * @var EntityValueWrapper date
     */
    public $last_login = null;

    /**
     * Created
     *
     * The date the user account was created.
     *
     * @property
     * @var EntityValueWrapper date
     */
    public $created = null;

    /**
     * User roles
     *
     * The roles of the user.
     *
     * @property
     * @var EntityListWrapper list<integer>
     */
    public $roles = null;

    /**
     * Status
     *
     * Whether the user is active or blocked.
     *
     * @property
     * @var EntityValueWrapper integer
     */
    public $status = null;

    /**
     * Default theme
     *
     * The user's default theme.
     *
     * @property
     */
    public $theme = null;

    /**
     * @return UserController
     */
    public function getControllerClass()
    {
        return entity_get_controller('user');
    }


}
