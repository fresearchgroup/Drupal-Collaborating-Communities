<ul class="admin-list quiz-type-list">
  <?php foreach ($question_types as $name => $question_type): ?>
    <?php /* @var $question_type \Drupal\quizz_question\Entity\QuestionType */ ?>
    <li>
      <div class="namel">
        <?php if (!empty($destination)): ?>
          <?php echo l($question_type->label, 'quiz-question/add/' . str_replace('_', '-', $name), array('query' => array('destination' => $destination))); ?>
        <?php else: ?>
          <?php echo l($question_type->label, 'quiz-question/add/' . str_replace('_', '-', $name)); ?>
        <?php endif; ?>
      </div>

      <?php if ($question_type->description): ?>
        <div class="description">
          <?php echo $question_type->description; ?>
        </div>
      <?php endif; ?>
    </li>
  <?php endforeach; ?>
</ul>
