<?php
function ffw_adventskalender_display_frontend() {
    $options = get_option('ffw_adventskalender_options');
    $backgroundImageUrlLarge = get_option('backgroundImageUrlLarge');
    $backgroundImageUrlSmall = get_option('backgroundImageUrlSmall');

    if(empty($options) && empty($backgroundImageUrlLarge)) {
      echo '<div style="display:block;color:red;"><strong>' . esc_html__('Adventskalender-Plugin:', 'ffw_adventskalender') . '</strong><br/>' . esc_html__('Bitte Kalendereinstellungen im Backend vornehmen, um den Adventskalender anzuzeigen.', 'ffw_adventskalender') . '</div>';
    }else {
      $displayModeDoors = get_option('ffw_adventskalender_display_mode');
      $colorDoorBorder = get_option('ffw_adventskalender_border_color', '#000000');
      $colorDoorNumber = get_option('ffw_adventskalender_text_color','#ffffff');
      
      $colorDoorBorder = empty($colorDoorBorder) ? '#000000' : $colorDoorBorder;
      $colorDoorNumber = empty($colorDoorNumber) ? '#FFFFFF' : $colorDoorNumber;

      $shuffleDoors = ($displayModeDoors ==='shuffle')? true : false;
      $days = range(1, 24);

      $styleCSS = 'style="border: 2px dashed '.$colorDoorBorder.' !important; color: '.$colorDoorNumber.' !important;"';
      echo '<div class="advent-calendar">' . "\n";
      if($shuffleDoors) {
        shuffle($days);
        foreach ($days as $day) {
          $day_option = isset($options['day' . $day]) ? $options['day' . $day] : ['image' => '', 'text' => '', 'link' => ''];
          echo '<div class="day" '. $styleCSS.' id="day' . $day . '">';
          echo $day;
          echo '</div>' . "\n";
        }
      } else {
        //Default order 
        $daysDefault = array(5,8,17,12,7,18,20,14,2,22,16,21,6,23,10,24,3,11,13,4,19,15,1,9);
        foreach ($daysDefault as $dayDefault) {
          echo '<div class="day" '. $styleCSS.' id="day'.$dayDefault.'">'.$dayDefault.'</div>' . "\n";
        }
      }
      echo '</div>';
      echo <<<EOL
      <div class="modal fade" id="notTimeModal" tabindex="-1" aria-labelledby="notTimeModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="notTimeModalLabel" style="margin:unset !important;">Noch etwas Geduld</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="notTimeModalBody">
              <!-- Der Text wird per JavaScript gesetzt -->
            </div>
          </div>
        </div>
      </div>
    
      <!-- Aktualisiertes Bootstrap 5 Modal für das angeklickte Bild -->
      <div class="modal fade" id="imageModal" tabindex="-10" aria-labelledby="imageModalLabel" aria-hidden="true">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="imageModalLabel" style="margin:unset !important;">[Text im Backend setzen]</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
              <img id="modalImage" src="" alt="Adventsbild" class="img-fluid">
              <p><div id="modalText"><!-- Der Text wird per JavaScript gesetzt --></div></p>
              <a id="modalLink" href="#" title="" target="_blank" class="btn btn-primary" role="button" style="display:none;">[Text im Backend setzen]</a>
            </div>
          </div>
        </div>
      </div>
EOL;
    }//else
}  //function
?>
