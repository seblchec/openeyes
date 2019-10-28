<?php
$this->renderPartial('view_CatProm5AnswerResult', array(
  'element'=>$element,
));
?>
</section>
<section class="element full">
  <header class="element-header">
    <!-- Add a element remove flag which is used when saving data -->
    <input type="hidden" name="[element_removed]CatProm5EventResult" value="0">
    <!-- Element title -->
    <h3 class="element-title">Questionnaire Score</h3>
  </header>
  <div class="element-data full-width flex-layout">
    <div class="cols-11">
      <div class="flex-layout flex-left">      
        Raw Score : 
        <div class="highlighter large-text" id="idg-js-demo-score"><?= $element->total_raw_score ?></div>
      </div>
    </div>
    <div class="cols-11">
      <div class="flex-layout flex-right">
        Rasch Score : 
        <div class="highlighter large-text" id="idg-js-demo-score"><?= $element->total_rasch_measure ?></div>
      </div>
    </div>
  </div>
